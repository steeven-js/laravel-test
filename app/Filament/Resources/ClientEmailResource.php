<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ClientEmailResource\Pages;
use App\Models\ClientEmail;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateRenderer;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientEmailResource extends Resource
{
    protected static ?string $modelLabel = 'Email client';

    protected static ?string $pluralModelLabel = 'Emails clients';

    protected static ?string $navigationLabel = 'Emails clients';

    protected static ?string $pluralNavigationLabel = 'Emails clients';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = ClientEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return 'Email client';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Emails clients';
    }

    public static function getNavigationLabel(): string
    {
        return 'Emails clients';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Destinataires & message')
                    ->description('Sélection du client, de l’utilisateur et objet du message')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('client_id')->label('Client')->relationship('client', 'nom')->searchable()->preload()->required(),
                                Forms\Components\Select::make('user_id')->label('Utilisateur')->relationship('user', 'name')->searchable()->preload()->required(),
                            ]),
                        Forms\Components\Placeholder::make('preview_help')
                            ->label('Variables disponibles')
                            ->content('Utilisez des variables comme {{client.nom}}, {{user.name}} dans objet et contenu. Elles seront remplacées lors de l\'envoi.'),
                        Forms\Components\Select::make('template_id')
                            ->label('Modèle d\'email')
                            ->options(fn () => EmailTemplate::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->dehydrated(false)
                            ->helperText('Sélectionner un modèle pour pré-remplir l\'objet et le contenu.')
                            ->afterStateUpdated(function ($state, Set $set): void {
                                if (! $state) {
                                    return;
                                }
                                $template = EmailTemplate::find($state);
                                if ($template) {
                                    $renderer = new EmailTemplateRenderer;
                                    // Contexte minimal, le reste sera affiné dans la page (si client/user choisis après)
                                    $set('objet', $renderer->render($template->subject, []));
                                    $set('contenu', $renderer->render($template->body, []));
                                }
                            }),
                        Forms\Components\TextInput::make('objet')->required()->maxLength(255),
                        Forms\Components\MarkdownEditor::make('contenu')
                            ->label('Contenu (Markdown)')
                            ->required()
                            ->columnSpanFull(),
                        Actions::make([
                            FormAction::make('previewRendered')
                                ->label('Aperçu rendu')
                                ->icon('heroicon-o-eye')
                                ->modalHeading('Aperçu rendu de l\'email')
                                ->modalSubmitAction(false)
                                ->action(fn () => null)
                                ->modalContent(function ($livewire) {
                                    $data = $livewire->form->getState();
                                    $renderer = new EmailTemplateRenderer;
                                    $context = [
                                        'client' => isset($data['client_id']) && $data['client_id'] ? optional(\App\Models\Client::find($data['client_id']))->toArray() : null,
                                        'user' => isset($data['user_id']) && $data['user_id'] ? optional(\App\Models\User::find($data['user_id']))->toArray() : null,
                                        'now' => now(),
                                    ];
                                    $subject = $renderer->render((string) ($data['objet'] ?? ''), $context);
                                    $body = $renderer->render((string) ($data['contenu'] ?? ''), $context);

                                    return view('partials.email-preview', compact('subject', 'body'));
                                }),
                        ])->fullWidth(),
                    ]),
                Forms\Components\Section::make('Pièces & statut')
                    ->description('Pièces jointes, copie et statut d’envoi')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        Forms\Components\Textarea::make('cc')->columnSpanFull(),
                        Forms\Components\KeyValue::make('attachments')->label('Pièces jointes (JSON)')->keyLabel('Nom')->valueLabel('Valeur / URL')->addButtonLabel('Ajouter une pièce jointe')->reorderable()->nullable()->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('statut')->label('Statut')->required()->options([
                                    'envoye' => 'Envoyé',
                                    'echec' => 'Échec',
                                ])->default('envoye'),
                                Forms\Components\DateTimePicker::make('date_envoi')->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('objet')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_envoi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'envoye' => 'Envoyé',
                        'echec' => 'Échec',
                    ]),
                Tables\Filters\SelectFilter::make('client_id')
                    ->relationship('client', 'nom')
                    ->label('Client'),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-paper-airplane')
            ->emptyStateHeading('Aucun email client')
            ->emptyStateDescription('Ajoutez votre premier email client pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouvel email'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->name('view')
                        ->label('Aperçu')
                        ->icon('heroicon-o-eye')
                        ->modal()
                        ->url(null)
                        ->modalCancelActionLabel('Fermer')
                        ->modalHeading('Aperçu de l\'email client')
                        ->modalDescription('Détails complets de l\'email client sélectionné')
                        ->modalWidth('4xl')
                        ->infolist([
                            Infolists\Components\Section::make('Destinataires & message')
                                ->description('Client, utilisateur et contenu de l\'email')
                                ->icon('heroicon-o-paper-airplane')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('client.nom')
                                                ->label('Client')
                                                ->badge()
                                                ->color('primary'),
                                            Infolists\Components\TextEntry::make('user.name')
                                                ->label('Utilisateur')
                                                ->badge()
                                                ->color('info'),
                                        ]),
                                    Infolists\Components\TextEntry::make('objet')
                                        ->label('Objet')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold'),
                                    Infolists\Components\TextEntry::make('contenu')
                                        ->label('Contenu')
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Pièces jointes & statut')
                                ->description('Pièces jointes, copie et statut d\'envoi')
                                ->icon('heroicon-o-paper-clip')
                                ->schema([
                                    Infolists\Components\TextEntry::make('cc')
                                        ->label('Copie (CC)')
                                        ->badge()
                                        ->color('warning')
                                        ->placeholder('Aucune copie'),
                                    Infolists\Components\TextEntry::make('attachments')
                                        ->label('Pièces jointes')
                                        ->markdown()
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => json_encode($record->attachments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                                        ->placeholder('Aucune pièce jointe'),
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('statut')
                                                ->label('Statut')
                                                ->badge()
                                                ->color(fn ($record) => $record->statut === 'envoye' ? 'success' : 'danger'),
                                            Infolists\Components\TextEntry::make('date_envoi')
                                                ->label('Date d\'envoi')
                                                ->dateTime('d/m/Y H:i')
                                                ->icon('heroicon-o-clock'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Informations système')
                                ->description('Métadonnées techniques')
                                ->icon('heroicon-o-cog')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Créé le')
                                                ->dateTime('d/m/Y H:i')
                                                ->icon('heroicon-o-calendar'),
                                            Infolists\Components\TextEntry::make('updated_at')
                                                ->label('Modifié le')
                                                ->dateTime('d/m/Y H:i')
                                                ->icon('heroicon-o-clock'),
                                        ]),
                                ]),
                        ]),
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientEmails::route('/'),
            'create' => Pages\CreateClientEmail::route('/create'),
            'edit' => Pages\EditClientEmail::route('/{record}/edit'),
        ];
    }
}
