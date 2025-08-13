<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientEmailResource\Pages;

use App\Filament\Resources\ClientEmailResource;
use App\Models\Client;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\EmailTemplateRenderer;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Request;

class CreateClientEmail extends CreateRecord
{
    protected static string $resource = ClientEmailResource::class;

    public function mount(): void
    {
        parent::mount();

        $templateId = (int) Request::query('template_id', 0);
        if ($templateId > 0) {
            $template = EmailTemplate::query()->whereKey($templateId)->where('is_active', true)->first();
            if ($template) {
                // Essaye de rendre avec variables si client_id/user_id déjà présents (ex: via valeurs par défaut)
                $renderer = new EmailTemplateRenderer;
                $context = $this->getContextForRendering();
                $this->form->fill([
                    'template_id' => $template->getKey(),
                    'objet' => $renderer->render($template->subject, $context),
                    'contenu' => $renderer->render($template->body, $context),
                ]);
            }
        }
    }

    protected function getContextForRendering(): array
    {
        $data = $this->form->getState();
        $client = isset($data['client_id']) ? Client::find($data['client_id']) : null;
        $user = isset($data['user_id']) ? User::find($data['user_id']) : null;

        return [
            'client' => $client ? [
                'id' => $client->id,
                'nom' => $client->nom,
                'email' => $client->email,
            ] : null,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ] : null,
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Rendu final juste avant création (et donc envoi si relié plus tard)
        $renderer = new EmailTemplateRenderer;
        $context = $this->getContextForRendering();
        $data['objet'] = $renderer->render((string) ($data['objet'] ?? ''), $context);
        $renderedBody = $renderer->render((string) ($data['contenu'] ?? ''), $context);
        $data['contenu'] = $renderedBody;

        return $data;
    }
}
