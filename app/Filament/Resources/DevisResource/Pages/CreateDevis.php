<?php

declare(strict_types=1);

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDevis extends CreateRecord
{
    protected static string $resource = DevisResource::class;

    protected static ?string $breadcrumb = 'Créer';

    public function getTitle(): string
    {
        return 'Créer ' . DevisResource::getModelLabel();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->after(function () {
                $this->notify('success', 'Devis créé ! Vous pouvez maintenant générer son PDF.');
            });
    }
}
