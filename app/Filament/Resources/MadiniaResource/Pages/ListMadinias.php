<?php

namespace App\Filament\Resources\MadiniaResource\Pages;

use App\Filament\Resources\MadiniaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMadinias extends ListRecords
{
    protected static string $resource = MadiniaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
