<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientEmailResource\Pages;

use App\Filament\Resources\ClientEmailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientEmail extends CreateRecord
{
    protected static string $resource = ClientEmailResource::class;
}
