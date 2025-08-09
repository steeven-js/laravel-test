<?php

declare(strict_types=1);

namespace App\Filament\Resources\HistoriqueResource\Pages;

use App\Filament\Resources\HistoriqueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistoriques extends ListRecords
{
    protected static string $resource = HistoriqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
