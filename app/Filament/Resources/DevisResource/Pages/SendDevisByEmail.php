<?php

declare(strict_types=1);

namespace App\Filament\Resources\DevisResource\Pages;

use App\Enums\DevisEnvoiStatus;
use App\Filament\Resources\DevisResource;
use App\Models\Devis;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Notifications\DevisEmailNotification;
use App\Services\EmailTemplateRenderer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class SendDevisByEmail extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = DevisResource::class;

    protected static string $view = 'filament.resources.devis-resource.pages.send-by-email';

    public Devis $record;

    public ?array $data = [];

    public function mount(Devis $record): void
    {
        $this->record = $record;

        // Pré-vérification PDF: s'il n'existe pas d'URL, tenter de générer
        if (blank($record->pdf_url)) {
            $this->tryEnsurePdfUrl($record);
        }
        // Toujours rafraîchir l'enregistrement pour récupérer l'URL à jour
        $record->refresh();
        $this->record = $record;

        $admin = Auth::user();
        $client = $record->client;

        $defaultTemplateId = EmailTemplate::query()
            ->where('is_active', true)
            ->where('is_default', true)
            ->where('category', 'envoi_initial')
            ->value('id');
        $defaultTemplate = $defaultTemplateId ? EmailTemplate::find($defaultTemplateId) : null;

        $defaults = [
            'admin_email' => $admin?->email,
            'client_email' => $client?->email,
            'cc_admin' => true,
            'template_category' => $defaultTemplate?->category ?? 'envoi_initial',
            'template_id' => $defaultTemplateId,
            'include_pdf' => true,
            'include_url' => true,
            'pdf_url_preview' => $record->pdf_url,
        ];

        // Pré-remplir objet et corps si un modèle est sélectionné
        if (! empty($defaults['template_id'])) {
            $template = EmailTemplate::find($defaults['template_id']);
            if ($template) {
                $renderer = new EmailTemplateRenderer;
                $context = $this->buildContext();
                $defaults['subject'] = $renderer->render((string) $template->subject, $context);
                $defaults['body'] = $renderer->render((string) $template->body, $context);
            }
        }

        $this->form->fill($defaults);
    }

    public function form(Form $form): Form
    {
        $devis = $this->record;

        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Vérification PDF')
                        ->icon('heroicon-m-document-arrow-down')
                        ->schema([
                            Forms\Components\Placeholder::make('pdf_status')
                                ->label('Statut du PDF')
                                ->content(function () use ($devis) {
                                    $hasUrl = filled($devis->pdf_url);
                                    return $hasUrl
                                        ? 'PDF déjà généré et disponible.'
                                        : 'Aucun PDF détecté. Il sera généré automatiquement.';
                                }),
                            Forms\Components\TextInput::make('pdf_url_preview')
                                ->label('URL du PDF')
                                ->default($devis->pdf_url)
                                ->disabled(),
                            Forms\Components\Toggle::make('regenerate_pdf')
                                ->label('Régénérer le PDF avant envoi')
                                ->default(false),
                        ]),

                    Wizard\Step::make('Destinataires & modèle')
                        ->icon('heroicon-m-envelope')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('admin_email')
                                        ->label("Email de l'admin")
                                        ->email()
                                        ->required(),
                                    Forms\Components\TextInput::make('client_email')
                                        ->label("Email du client")
                                        ->email()
                                        ->required(),
                                ]),
                            Forms\Components\Toggle::make('cc_admin')
                                ->label("Mettre l'admin en copie (CC)")
                                ->default(true),
                            Forms\Components\ToggleButtons::make('template_category')
                                ->label('Catégorie')
                                ->options([
                                    'envoi_initial' => 'Envoi initial',
                                    'rappel' => 'Rappel',
                                    'confirmation' => 'Confirmation',
                                ])
                                ->colors([
                                    'envoi_initial' => 'success',
                                    'rappel' => 'warning',
                                    'confirmation' => 'info',
                                ])
                                ->icons([
                                    'envoi_initial' => 'heroicon-m-paper-airplane',
                                    'rappel' => 'heroicon-m-bell-alert',
                                    'confirmation' => 'heroicon-m-check-badge',
                                ])
                                ->inline()
                                ->default('envoi_initial')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set): void {
                                    // Quand la catégorie change, sélectionner automatiquement le modèle par défaut de la catégorie
                                    $templateId = EmailTemplate::query()
                                        ->where('is_active', true)
                                        ->where('category', $state)
                                        ->where('is_default', true)
                                        ->value('id');
                                    // Fallback: premier actif de la catégorie si aucun par défaut
                                    if (! $templateId) {
                                        $templateId = EmailTemplate::query()
                                            ->where('is_active', true)
                                            ->where('category', $state)
                                            ->orderByDesc('is_default')
                                            ->orderByDesc('updated_at')
                                            ->orderByDesc('id')
                                            ->value('id');
                                    }
                                    $set('template_id', $templateId);
                                    if ($templateId) {
                                        $template = EmailTemplate::find($templateId);
                                        if ($template) {
                                            $renderer = new EmailTemplateRenderer;
                                            $context = $this->buildContext();
                                            $set('subject', $renderer->render((string) $template->subject, $context));
                                            $set('body', $renderer->render((string) $template->body, $context));
                                        }
                                    }
                                }),
                            Forms\Components\Select::make('template_id')
                                ->label('Modèle d\'email')
                                ->options(function (callable $get) {
                                    $category = (string) ($get('template_category') ?? '');
                                    $query = EmailTemplate::query()->where('is_active', true);
                                    if ($category !== '') {
                                        $query->where('category', $category);
                                    }
                                    $templates = $query
                                        ->orderByDesc('is_default')
                                        ->orderBy('name')
                                        ->get(['id', 'name', 'is_default']);
                                    return $templates->mapWithKeys(function ($t) {
                                        $label = (string) $t->name;
                                        if ((bool) $t->is_default) {
                                            $label .= ' — par défaut';
                                        }
                                        return [$t->id => $label];
                                    });
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set): void {
                                    if (! $state) {
                                        return;
                                    }
                                    $template = EmailTemplate::find($state);
                                    if ($template) {
                                        $renderer = new EmailTemplateRenderer;
                                        $context = $this->buildContext();
                                        $set('subject', $renderer->render((string) $template->subject, $context));
                                        $set('body', $renderer->render((string) $template->body, $context));
                                    }
                                }),

                            Forms\Components\Placeholder::make('template_default_indicator')
                                ->label('Statut du modèle')
                                ->content(function (Get $get) {
                                    $id = $get('template_id');
                                    if (! $id) {
                                        return '—';
                                    }
                                    $tmpl = EmailTemplate::find($id);
                                    if (! $tmpl) {
                                        return '—';
                                    }
                                    return $tmpl->is_default ? 'Par défaut (catégorie)' : 'Non par défaut';
                                }),
                            Forms\Components\TextInput::make('subject')
                                ->label('Objet')
                                ->required(),
                            Forms\Components\MarkdownEditor::make('body')
                                ->label('Contenu (Markdown)')
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('previewRendered')
                                    ->label('Aperçu rendu')
                                    ->icon('heroicon-o-eye')
                                    ->modalHeading("Aperçu de l'email")
                                    ->modalSubmitAction(false)
                                    ->action(fn () => null)
                                    ->modalContent(function ($livewire) {
                                        $data = $livewire->form->getState();
                                        $renderer = new EmailTemplateRenderer;
                                        $context = $this->buildContext();
                                        $subject = $renderer->render((string) ($data['subject'] ?? ''), $context);
                                        $bodyRaw = $renderer->render((string) ($data['body'] ?? ''), $context);
                                        $body = $renderer->renderMarkdown($bodyRaw);
                                        return view('partials.email-preview', compact('subject', 'body'));
                                    }),
                            ])->fullWidth(),
                        ]),

                    Wizard\Step::make('Pièces & options')
                        ->icon('heroicon-m-paper-clip')
                        ->schema([
                            Forms\Components\Toggle::make('include_pdf')
                                ->label('Joindre le PDF du devis')
                                ->default(true),
                            Forms\Components\Toggle::make('include_url')
                                ->label("Inclure l'URL du PDF dans le message")
                                ->default(true),
                        ]),

                    Wizard\Step::make('Confirmation')
                        ->icon('heroicon-m-check-circle')
                        ->schema([
                            Forms\Components\View::make('filament.resources.devis-resource.pages.partials.send-email-recap')
                                ->viewData(function (Get $get) {
                                    $renderer = new EmailTemplateRenderer;
                                    $context = $this->buildContext();
                                    $subject = $renderer->render((string) ($get('subject') ?? ''), $context);
                                    $bodyRaw = $renderer->render((string) ($get('body') ?? ''), $context);
                                    $body = $renderer->renderMarkdown($bodyRaw);

                                    $from = (string) ($get('admin_email') ?? '');
                                    $to = (string) ($get('client_email') ?? '');
                                    $cc = ((bool) ($get('cc_admin') ?? false)) ? $from : null;
                                    $includePdf = (bool) ($get('include_pdf') ?? true);
                                    $includeUrl = (bool) ($get('include_url') ?? true);
                                    $pdfUrl = (string) ($this->record->pdf_url ?? '');
                                    $devis = $this->record;

                                    return compact('from', 'to', 'cc', 'includePdf', 'includeUrl', 'pdfUrl', 'subject', 'body', 'devis');
                                })
                                ->columnSpanFull(),
                        ]),
                ])
                    ->skippable(false)
                    ->submitAction(
                        \Filament\Actions\Action::make('submit')
                            ->label('Envoyer')
                            ->icon('heroicon-m-paper-airplane')
                            ->color('primary')
                            ->submit('submit')
                    ),
            ])
            ->statePath('data');
    }

    public function updatedData($key, $value): void
    {
        // Charger le template sélectionné pour pré-remplir objet et contenu
        if ($key === 'template_id' && $value) {
            $template = EmailTemplate::find($value);
            if ($template) {
                $renderer = new EmailTemplateRenderer;
                $context = $this->buildContext();
                $this->form->fill([
                    'subject' => $renderer->render((string) $template->subject, $context),
                    'body' => $renderer->render((string) $template->body, $context),
                ] + ($this->data ?? []));
            }
        }
    }

    protected function buildContext(): array
    {
        $devis = $this->record->fresh(['client', 'administrateur']);
        $admin = Auth::user();

        $euro = function ($v): string {
            $num = (float) ($v ?? 0);
            return number_format($num, 2, ',', ' ') . ' €';
        };

        $validiteStr = $devis->date_validite?->toDateString();
        if ($devis->date_devis && $devis->date_validite) {
            $days = $devis->date_devis->diffInDays($devis->date_validite, false);
            if ($days >= 0) {
                $validiteStr = $days . ' jours';
            }
        }

        $client = $devis->client;

        return [
            'client' => optional($client)->toArray(),
            'user' => optional($admin)->toArray(),
            'devis' => [
                'numero' => $devis->numero_devis,
                'numero_devis' => $devis->numero_devis,
                'montant_ht' => $devis->montant_ht,
                'montant_ttc' => $devis->montant_ttc,
                'taux_tva' => $devis->taux_tva,
                'validite' => $validiteStr,
                'pdf_url' => $devis->pdf_url,
            ],
            // alias à plat pour compatibilité avec d'anciens modèles
            'client_nom' => $client?->nom,
            'client_email' => $client?->email,
            'user_name' => $admin?->name,
            'user_email' => $admin?->email,
            'devis_numero' => $devis->numero_devis,
            'numero_devis' => $devis->numero_devis,
            'devis_montant' => $euro($devis->montant_ttc),
            'devis_montant_ttc' => $euro($devis->montant_ttc),
            'devis_montant_ht' => $euro($devis->montant_ht),
            'devis_taux_tva' => (string) $devis->taux_tva,
            'devis_validite' => $validiteStr,
            'devis_pdf_url' => $devis->pdf_url,
            'entreprise' => [
                'nom' => config('app.name', 'Madinia'),
            ],
            'entreprise_nom' => config('app.name', 'Madinia'),
            'now' => now(),
        ];
    }

    protected function tryEnsurePdfUrl(Devis $devis, bool $force = false): void
    {
        $functionsBaseUrl = env('SUPABASE_FUNCTIONS_URL', 'https://uemmyrtikobkczqmnpbi.supabase.co/functions/v1');
        $base = rtrim($functionsBaseUrl, '/');
        $needle = '/functions/v1';
        if (($pos = strpos($base, $needle)) !== false) {
            $base = substr($base, 0, $pos + strlen($needle));
        }
        $base = rtrim($base, '/');
        $anonKey = env('SUPABASE_ANON_KEY');
        if (blank($anonKey)) {
            return;
        }
        if (! $force && filled($devis->pdf_url)) {
            return;
        }
        $numero = (string) ($devis->numero_devis ?? $devis->id);
        $sanitized = preg_replace('/[^A-Za-z0-9._-]/', '-', $numero) ?: (string) $devis->id;
        $desiredPath = 'devis/' . $sanitized . '.pdf';
        $response = Http::withToken($anonKey)
            ->post($base . "/generate-devis-pdf/{$devis->id}?public=true&path=" . urlencode($desiredPath));
        if ($response->ok()) {
            $data = $response->json();
            $devis->pdf_url = $data['publicUrl'] ?? $devis->pdf_url;
            $devis->pdf_file = $data['storagePath'] ?? $devis->pdf_file;
            $devis->save();
        }
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $devis = $this->record->fresh();

        if (! empty($data['regenerate_pdf'])) {
            $this->tryEnsurePdfUrl($devis, force: true);
            $devis->refresh();
        }

        $adminEmail = (string) ($data['admin_email'] ?? '');
        $clientEmail = (string) ($data['client_email'] ?? '');

        if ($adminEmail === '' || $clientEmail === '') {
            Notification::make()->title('Emails requis')->danger()->body("Renseignez l'email admin et l'email client")->send();
            return;
        }

        $renderer = new EmailTemplateRenderer;
        $context = $this->buildContext();
        $subject = $renderer->render((string) ($data['subject'] ?? ''), $context);
        $bodyMarkdown = $renderer->render((string) ($data['body'] ?? ''), $context);

        try {
            // Envoi via Mailable Markdown
            $mailable = new \App\Mail\DevisMarkdownMail($subject, $bodyMarkdown, $devis, (bool) ($data['include_url'] ?? true));
            if (!empty($data['include_pdf']) && filled($devis->pdf_url)) {
                // Télécharger la pièce et l'attacher côté serveur
                try {
                    $tmp = tempnam(sys_get_temp_dir(), 'devis_pdf_') ?: null;
                    if ($tmp) {
                        $pdf = file_get_contents((string) $devis->pdf_url);
                        if ($pdf !== false) {
                            file_put_contents($tmp, $pdf);
                            $mailable->attach($tmp, [
                                'as' => 'Devis_' . ($devis->numero_devis ?: $devis->id) . '.pdf',
                                'mime' => 'application/pdf',
                            ]);
                        }
                    }
                } catch (\Throwable) {
                    // pièce jointe best-effort, on continue
                }
            }

            $mailer = Mail::to($clientEmail);
            if (!empty($data['cc_admin'])) {
                $mailer->cc([$adminEmail]);
            }
            $mailer->send($mailable);

            // Statut & date
            $devis->statut_envoi = DevisEnvoiStatus::Envoye->value;
            $devis->date_envoi_client = Carbon::now();
            $devis->date_envoi_admin = !empty($data['cc_admin']) ? Carbon::now() : $devis->date_envoi_admin;
            $devis->save();

            // Notification applicative de succès
            try {
                $admin = Auth::user();
                if ($admin instanceof User) {
                    $admin->notify(new DevisEmailNotification($devis, true));
                }
            } catch (\Throwable) {
                // ne bloque pas
            }

            Notification::make()
                ->title('Email envoyé')
                ->body('Le devis a été envoyé avec succès.')
                ->success()
                ->send();

            $this->redirect(route('filament.admin.resources.devis.view', ['record' => $devis]));
        } catch (\Throwable $e) {
            $devis->statut_envoi = DevisEnvoiStatus::EchecEnvoi->value;
            $devis->save();

            try {
                $admin = Auth::user();
                if ($admin instanceof User) {
                    $admin->notify(new DevisEmailNotification($devis, false, $e->getMessage()));
                }
            } catch (\Throwable) {
            }

            Notification::make()
                ->title("Échec de l'envoi")
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}


