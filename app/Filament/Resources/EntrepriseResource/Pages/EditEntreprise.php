<?php

declare(strict_types=1);

namespace App\Filament\Resources\EntrepriseResource\Pages;

use App\Filament\Resources\EntrepriseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditEntreprise extends EditRecord
{
    protected static string $resource = EntrepriseResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Modifier l\'entreprise ' . $this->record->nom;
    }

    protected static ?string $breadcrumb = 'Modifier l\'entreprise';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
