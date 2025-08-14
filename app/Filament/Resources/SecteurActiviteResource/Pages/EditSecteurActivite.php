<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecteurActiviteResource\Pages;

use App\Filament\Resources\SecteurActiviteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditSecteurActivite extends EditRecord
{
    protected static string $resource = SecteurActiviteResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Modifier le secteur d\'activité ' . $this->record->libelle;
    }

    protected static ?string $breadcrumb = 'Modifier le secteur d\'activité';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
