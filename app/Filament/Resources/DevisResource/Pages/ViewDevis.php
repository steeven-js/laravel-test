<?php

declare(strict_types=1);

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDevis extends ViewRecord
{
    protected static string $resource = DevisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
