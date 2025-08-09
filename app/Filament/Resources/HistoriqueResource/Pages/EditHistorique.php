<?php

namespace App\Filament\Resources\HistoriqueResource\Pages;

use App\Filament\Resources\HistoriqueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistorique extends EditRecord
{
    protected static string $resource = HistoriqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
