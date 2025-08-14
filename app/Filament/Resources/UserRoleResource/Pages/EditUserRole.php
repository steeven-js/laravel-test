<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserRoleResource\Pages;

use App\Filament\Resources\UserRoleResource;
use App\Services\PermissionService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUserRole extends EditRecord
{
    protected static string $resource = UserRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Navigation
            Action::make('precedent')
                ->label('Précédent')
                ->icon('heroicon-m-chevron-left')
                ->color('gray')
                ->url(fn (): string => ($id = $this->getPreviousRecordId()) ? $this->getResource()::getUrl('edit', ['record' => $id]) : '#')
                ->disabled(fn (): bool => $this->getPreviousRecordId() === null),
            Action::make('suivant')
                ->label('Suivant')
                ->icon('heroicon-m-chevron-right')
                ->color('gray')
                ->url(fn (): string => ($id = $this->getNextRecordId()) ? $this->getResource()::getUrl('edit', ['record' => $id]) : '#')
                ->disabled(fn (): bool => $this->getNextRecordId() === null),

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

    public function getTitle(): string
    {
        $display = $this->record->display_name ?? null;
        $technical = $this->record->name ?? 'rôle';
        return $display ? "Modifier le rôle : {$display}" : "Modifier le rôle : {$technical}";
    }

    private function getPreviousRecordId(): ?int
    {
        if (! $this->record) {
            return null;
        }

        $modelClass = $this->getResource()::getModel();
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $modelClass();

        $prev = $modelClass::query()
            ->where($model->getKeyName(), '<', $this->record->getKey())
            ->orderBy($model->getKeyName(), 'desc')
            ->value($model->getKeyName());

        return $prev ? (int) $prev : null;
    }

    private function getNextRecordId(): ?int
    {
        if (! $this->record) {
            return null;
        }

        $modelClass = $this->getResource()::getModel();
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $modelClass();

        $next = $modelClass::query()
            ->where($model->getKeyName(), '>', $this->record->getKey())
            ->orderBy($model->getKeyName(), 'asc')
            ->value($model->getKeyName());

        return $next ? (int) $next : null;
    }
}
