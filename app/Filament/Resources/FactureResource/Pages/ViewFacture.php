<?php

declare(strict_types=1);

namespace App\Filament\Resources\FactureResource\Pages;

use App\Filament\Resources\FactureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFacture extends ViewRecord
{
    protected static string $resource = FactureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Groupe 1: Actions principales
            Actions\ActionGroup::make([
                Actions\Action::make('preview_pdf_modal')
                    ->label('Aperçu PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn () => "Aperçu PDF - Facture {$this->record->numero_facture}")
                    ->modalContent(fn () => view('pdf.preview-modal-facture', [
                        'pdfUrl' => route('factures.pdf', $this->record),
                        'facture' => $this->record,
                    ]))
                    ->modalWidth('7xl')
                    ->modalCancelActionLabel('Fermer')
                    ->modalSubmitAction(false),
            ])
                ->label('Actions')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary'),

            // Boutons de navigation directement visibles
            Actions\Action::make('precedent')
                ->label('Précédent')
                ->icon('heroicon-m-chevron-left')
                ->color('gray')
                ->url(fn (): string => $this->getPreviousRecordId()
                    ? FactureResource::getUrl('view', ['record' => $this->getPreviousRecordId()])
                    : '#')
                ->disabled(fn (): bool => $this->getPreviousRecordId() === null),
            Actions\Action::make('suivant')
                ->label('Suivant')
                ->icon('heroicon-m-chevron-right')
                ->color('gray')
                ->url(fn (): string => $this->getNextRecordId()
                    ? FactureResource::getUrl('view', ['record' => $this->getNextRecordId()])
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
