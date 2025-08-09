<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected static ?string $breadcrumb = 'Créer';

    protected function getCreatedNotification(): ?Notification
    {
        $name = trim(($this->record->prenom ?? '') . ' ' . ($this->record->nom ?? 'Client'));

        return Notification::make()
            ->success()
            ->title('Client créé')
            ->body("« {$name} » a été créé avec succès.");
    }
}
