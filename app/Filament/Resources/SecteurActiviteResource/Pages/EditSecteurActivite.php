<?php

namespace App\Filament\Resources\SecteurActiviteResource\Pages;

use App\Filament\Resources\SecteurActiviteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSecteurActivite extends EditRecord
{
    protected static string $resource = SecteurActiviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
