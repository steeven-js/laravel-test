<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;
    protected static ?string $breadcrumb = 'Liste';

    public function getTitle(): string
    {
        return static::getResource()::getPluralModelLabel();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouveau'),
        ];
    }
}
