<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientEmailResource\Pages;

use App\Filament\Resources\ClientEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientEmails extends ListRecords
{
    protected static string $resource = ClientEmailResource::class;

    protected static ?string $breadcrumb = 'Liste';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouvel email'),
        ];
    }
}
