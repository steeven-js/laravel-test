<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Filament\Widgets\client\ClientStats;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    public function getTitle(): string | Htmlable
    {
        $nom = (string) ($this->record->nom ?? '');
        $prenom = (string) ($this->record->prenom ?? '');
        $fullName = trim("{$nom} {$prenom}");

        return $fullName !== '' ? $fullName : parent::getTitle();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('nouveau_devis')
                ->label('Nouveau devis')
                ->icon('heroicon-o-document-plus')
                ->color('primary')
                ->url(fn (): string => \App\Filament\Resources\DevisResource::getUrl('create', ['client_id' => $this->record->getKey()])),
            Action::make('envoyer_email')
                ->label('Envoyer un email')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->url(fn (): string => \App\Filament\Resources\ClientEmailResource::getUrl('create', ['client_id' => $this->record->getKey()])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Client')
                    ->description('Informations personnelles')
                    ->schema([
                        InfoGrid::make(2)
                            ->schema([
                                TextEntry::make('nom')->label('Nom'),
                                TextEntry::make('prenom')->label('Prénom'),
                                TextEntry::make('email')->label('Email'),
                                TextEntry::make('telephone')->label('Téléphone'),
                            ]),
                    ]),
                Section::make('Adresse')
                    ->schema([
                        InfoGrid::make(2)
                            ->schema([
                                TextEntry::make('adresse')->label('Adresse')->columnSpanFull(),
                                TextEntry::make('ville')->label('Ville'),
                                TextEntry::make('code_postal')->label('Code postal'),
                                TextEntry::make('pays')->label('Pays'),
                            ]),
                    ]),
                Section::make('Entreprise')
                    ->schema([
                        InfoGrid::make(2)
                            ->schema([
                                TextEntry::make('entreprise.nom')->label('Entreprise'),
                                IconEntry::make('actif')->label('Statut')->boolean(),
                                TextEntry::make('notes')->label('Notes')->markdown()->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClientStats::class,
        ];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            ClientStats::class => [
                'clientId' => $this->record->getKey(),
            ],
        ];
    }
}
