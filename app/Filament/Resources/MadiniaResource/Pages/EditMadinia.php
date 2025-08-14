<?php

declare(strict_types=1);

namespace App\Filament\Resources\MadiniaResource\Pages;

use App\Filament\Resources\MadiniaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditMadinia extends EditRecord
{
    protected static string $resource = MadiniaResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Modifier Madinia';
    }

    protected static ?string $breadcrumb = 'Modifier Madinia';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
