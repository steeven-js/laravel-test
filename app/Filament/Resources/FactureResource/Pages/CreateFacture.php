<?php

declare(strict_types=1);

namespace App\Filament\Resources\FactureResource\Pages;

use App\Filament\Resources\FactureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFacture extends CreateRecord
{
    protected static string $resource = FactureResource::class;
}
