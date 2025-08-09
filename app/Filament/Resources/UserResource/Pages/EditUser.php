<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $breadcrumb = 'Modifier';

    protected static ?string $title = 'Modifier l\'utilisateur';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Supprimer')
                ->successNotification(fn () => Notification::make()->success()->title('Utilisateur supprimé')->body('L\'utilisateur a été supprimé.')),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        $name = $this->record->name ?? 'Utilisateur';

        return Notification::make()
            ->success()
            ->title('Utilisateur mis à jour')
            ->body("« {$name} » a été mis à jour avec succès.");
    }
}
