<?php

declare(strict_types=1);

namespace App\Filament\Resources\EntrepriseResource\Pages;

use App\Filament\Resources\EntrepriseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewEntreprise extends ViewRecord
{
    protected static string $resource = EntrepriseResource::class;

    public function getTitle(): string|Htmlable
    {
        $nom = (string) ($this->record->nom ?? '');

        return $nom !== '' ? $nom : parent::getTitle();
    }

    protected function getHeaderActions(): array
    {
        return [
            // Groupe 1: Actions principales
            Actions\ActionGroup::make([
                Action::make('nouvelle_entreprise')
                    ->label('Nouvel entreprise')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->url(fn (): string => \App\Filament\Resources\EntrepriseResource::getUrl('create', ['entreprise_id' => $this->record->getKey()])),
            ])
                ->label('Actions')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary'),

            // Boutons de navigation directement visibles
            Action::make('precedent')
                ->label('Précédent')
                ->icon('heroicon-m-chevron-left')
                ->color('gray')
                ->url(fn (): string => $this->getPreviousRecordId()
                    ? EntrepriseResource::getUrl('view', ['record' => $this->getPreviousRecordId()])
                    : '#')
                ->disabled(fn (): bool => $this->getPreviousRecordId() === null),
            Action::make('suivant')
                ->label('Suivant')
                ->icon('heroicon-m-chevron-right')
                ->color('gray')
                ->url(fn (): string => $this->getNextRecordId()
                    ? EntrepriseResource::getUrl('view', ['record' => $this->getNextRecordId()])
                    : '#')
                ->disabled(fn (): bool => $this->getNextRecordId() === null),

            // Groupe 3: Actions secondaires
            Actions\EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil')
                ->color('warning'),
        ];
    }

    private function getPreviousRecordId(): ?int
    {
        /** @var Model $record */
        $record = static::getResource()::getEloquentQuery()
            ->whereNull('deleted_at')
            ->where('id', '<', $this->record->getKey())
            ->orderByDesc('id')
            ->first();

        return $record?->getKey();
    }

    private function getNextRecordId(): ?int
    {
        /** @var Model $record */
        $record = static::getResource()::getEloquentQuery()
            ->whereNull('deleted_at')
            ->where('id', '>', $this->record->getKey())
            ->orderBy('id')
            ->first();

        return $record?->getKey();
    }
}
