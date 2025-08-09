<?php

namespace App\Filament\Resources\SecteurActiviteResource\Pages;

use App\Filament\Resources\SecteurActiviteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSecteurActivites extends ListRecords
{
    protected static string $resource = SecteurActiviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
