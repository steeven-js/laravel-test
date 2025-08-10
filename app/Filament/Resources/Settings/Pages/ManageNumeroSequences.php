<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\NumeroSequenceResource;
use Filament\Resources\Pages\ManageRecords;

class ManageNumeroSequences extends ManageRecords
{
    protected static string $resource = NumeroSequenceResource::class;

    protected static ?string $breadcrumb = 'Compteurs';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()->label('Ajouter un compteur'),
        ];
    }
}
