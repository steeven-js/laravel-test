<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(fn () => Notification::make()->success()->title('Client supprimé')->body('Le client a été supprimé.')),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        $name = trim(($this->record->prenom ?? '') . ' ' . ($this->record->nom ?? 'Client'));

        return Notification::make()
            ->success()
            ->title('Client mis à jour')
            ->body("« {$name} » a été mis à jour avec succès.");
    }
}
