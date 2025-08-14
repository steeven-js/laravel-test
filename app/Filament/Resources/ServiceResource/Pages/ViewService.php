<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Voir le service ' . $this->record->nom;
    }

    protected static ?string $breadcrumb = 'Voir le service';

    protected function getHeaderActions(): array
    {
        return [
            // Groupe 1: Actions principales (vide pour l'instant)
            Actions\ActionGroup::make([])
                ->label('Actions')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary'),

            // Boutons de navigation directement visibles
            Actions\Action::make('precedent')
                ->label('Précédent')
                ->icon('heroicon-m-chevron-left')
                ->color('gray')
                ->url(fn (): string => $this->getPreviousRecordId()
                    ? ServiceResource::getUrl('view', ['record' => $this->getPreviousRecordId()])
                    : '#')
                ->disabled(fn (): bool => $this->getPreviousRecordId() === null),
            Actions\Action::make('suivant')
                ->label('Suivant')
                ->icon('heroicon-m-chevron-right')
                ->color('gray')
                ->url(fn (): string => $this->getNextRecordId()
                    ? ServiceResource::getUrl('view', ['record' => $this->getNextRecordId()])
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
        /** @var \Illuminate\Database\Eloquent\Model $record */
        $record = static::getResource()::getEloquentQuery()
            ->whereNull('deleted_at')
            ->where('id', '<', $this->record->getKey())
            ->orderByDesc('id')
            ->first();

        return $record?->getKey();
    }

    private function getNextRecordId(): ?int
    {
        /** @var \Illuminate\Database\Eloquent\Model $record */
        $record = static::getResource()::getEloquentQuery()
            ->whereNull('deleted_at')
            ->where('id', '>', $this->record->getKey())
            ->orderBy('id')
            ->first();

        return $record?->getKey();
    }
}
