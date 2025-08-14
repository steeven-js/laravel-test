<?php

declare(strict_types=1);

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use App\Models\Facture;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditDevis extends EditRecord
{
    protected static string $resource = DevisResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Modifier le devis ' . $this->record->numero_devis;
    }

    protected static ?string $breadcrumb = 'Modifier le devis';

    protected function getHeaderActions(): array
    {
        return [
            // Groupe 1: Actions principales
            Actions\ActionGroup::make([
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
                    ->disabled(fn (): bool => Facture::query()->where('devis_id', $this->record->id)->exists())
                    ->tooltip(fn (): ?string => Facture::query()->where('devis_id', $this->record->id)->exists()
                        ? 'Une facture existe déjà pour ce devis.'
                        : 'Le statut du devis sera automatiquement passé à \'Accepté\' lors de la transformation.')
                    ->url(fn (): string => DevisResource::getUrl('transform', ['record' => $this->record]))
                    ->openUrlInNewTab(false),
            ])
                ->label('Actions')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary'),

            // Groupe 2: Actions secondaires
            Actions\DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }
}
