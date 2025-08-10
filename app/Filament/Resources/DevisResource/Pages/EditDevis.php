<?php

declare(strict_types=1);

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDevis extends EditRecord
{
    protected static string $resource = DevisResource::class;

    protected static ?string $breadcrumb = 'Modifier';

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

            Actions\DeleteAction::make(),
        ];
    }
}
