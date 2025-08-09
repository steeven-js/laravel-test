<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $breadcrumb = 'Créer';

    protected static ?string $title = 'Créer un utilisateur';

    protected function getCreatedNotification(): ?Notification
    {
        $name = $this->record->name ?? 'Utilisateur';

        return Notification::make()
            ->success()
            ->title('Utilisateur créé')
            ->body("« {$name} » a été créé avec succès.");
    }
}
