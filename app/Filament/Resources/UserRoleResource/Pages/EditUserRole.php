<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserRoleResource\Pages;

use App\Filament\Resources\UserRoleResource;
use App\Services\PermissionService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserRole extends EditRecord
{
    protected static string $resource = UserRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Supprimer le rôle')
                ->visible(fn () => $this->record->name !== 'super_admin'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convertir les permissions de la base de données en format formulaire
        if (isset($data['permissions'])) {
            $data['permissions'] = PermissionService::formatPermissionsForForm($data['permissions']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convertir les permissions du formulaire en format JSON pour la base de données
        if (isset($data['permissions'])) {
            $data['permissions'] = PermissionService::formatPermissionsForDatabase($data['permissions']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
