<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDevis extends CreateRecord
{
    protected static string $resource = DevisResource::class;

    protected static ?string $breadcrumb = 'Créer';

    public function getTitle(): string
    {
        return 'Créer ' . DevisResource::getModelLabel();
    }
}
