<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserRoleResource\Pages;

use App\Filament\Resources\UserRoleResource;
use App\Services\PermissionService;
use Filament\Resources\Pages\CreateRecord;

class CreateUserRole extends CreateRecord
{
    protected static string $resource = UserRoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
