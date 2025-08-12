<?php

declare(strict_types=1);

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use App\Models\Facture;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ViewDevis extends ViewRecord
{
    protected static string $resource = DevisResource::class;

    public function getTitle(): string|Htmlable
    {
        $numeroDevis = (string) ($this->record->numero_devis ?? '');

        return $numeroDevis !== '' ? $numeroDevis : parent::getTitle();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview_pdf_modal')
                ->label('Aperçu PDF')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->modalHeading(fn () => "Aperçu PDF - Devis {$this->record->numero_devis}")
                ->modalContent(fn () => view('pdf.preview-modal', [
                    'pdfUrl' => route('devis.pdf', $this->record),
                    'devis' => $this->record,
                ]))
                ->modalWidth('7xl')
                ->modalCancelActionLabel('Fermer')
                ->modalSubmitAction(false),

            Actions\Action::make('convert_to_invoice')
                ->label('Transformer en facture')
                ->icon('heroicon-m-arrow-right-circle')
                ->color('success')
                // Toujours visible à côté d'Aperçu PDF, désactivé seulement si une facture existe déjà
                ->disabled(function (): bool {
                    if (! $this->record) {
                        return true;
                    }
                    $alreadyConverted = Facture::query()->where('devis_id', $this->record->id)->exists();

                    return $alreadyConverted;
                })
                ->tooltip(function (): ?string {
                    if (! $this->record) {
                        return null;
                    }
                    if (Facture::query()->where('devis_id', $this->record->id)->exists()) {
                        return 'Une facture existe déjà pour ce devis.';
                    }

                    return 'Le statut du devis sera automatiquement passé à ‘Accepté’ lors de la transformation.';
                })
                ->url(fn (): string => DevisResource::getUrl('transform', ['record' => $this->record]))
                ->openUrlInNewTab(false),

            Actions\EditAction::make(),
            Action::make('precedent')
                ->label('Précédent')
                ->icon('heroicon-m-chevron-left')
                ->color('gray')
                ->url(fn (): string => $this->getPreviousRecordId()
                    ? DevisResource::getUrl('view', ['record' => $this->getPreviousRecordId()])
                    : '#')
                ->disabled(fn (): bool => $this->getPreviousRecordId() === null),
            Action::make('suivant')
                ->label('Suivant')
                ->icon('heroicon-m-chevron-right')
                ->color('gray')
                ->url(fn (): string => $this->getNextRecordId()
                    ? DevisResource::getUrl('view', ['record' => $this->getNextRecordId()])
                    : '#')
                ->disabled(fn (): bool => $this->getNextRecordId() === null),
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
