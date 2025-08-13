<?php

declare(strict_types=1);

namespace App\Filament\Resources\FactureResource\Pages;

use App\Filament\Resources\FactureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFacture extends EditRecord
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

            // Groupe 2: Actions secondaires
            Actions\DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }
}
