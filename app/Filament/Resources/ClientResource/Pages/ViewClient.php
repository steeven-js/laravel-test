<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use App\Filament\Widgets\ClientQuickOverview;
use Filament\Actions\Action;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

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
                Section::make('Statistiques')
                    ->description('Aperçu des performances et informations commerciales')
                    ->schema([
                        // Ligne 1: 4 cartes KPI en grille responsive (1 / 2 / 4 colonnes)
                        InfoGrid::make()
                            ->columns(1)
                            ->columns(2, 'md')
                            ->columns(4, 'xl')
                            ->schema([
                                Section::make('Total devis')
                                    ->icon('heroicon-m-document-text')
                                    ->columnSpan(1)
                                    ->schema([
                                        TextEntry::make('total_devis')
                                            ->label('')
                                            ->getStateUsing(fn ($record) => $record->devis()->count())
                                            ->formatStateUsing(fn ($state) => (string) $state)
                                            ->extraAttributes(['class' => 'text-2xl font-semibold']),
                                    ]),
                                Section::make('Devis acceptés')
                                    ->icon('heroicon-m-check-circle')
                                    ->columnSpan(1)
                                    ->schema([
                                        TextEntry::make('devis_acceptes_card')
                                            ->label('')
                                            ->getStateUsing(fn ($record) => $record->devis()->where('statut', 'accepte')->count())
                                            ->formatStateUsing(fn ($state) => (string) $state)
                                            ->extraAttributes(['class' => 'text-2xl font-semibold']),
                                    ]),
                                Section::make('Taux de conversion')
                                    ->icon('heroicon-m-chart-bar')
                                    ->columnSpan(1)
                                    ->schema([
                                        TextEntry::make('taux_conversion_card')
                                            ->label('')
                                            ->getStateUsing(function ($record) {
                                                $total = $record->devis()->count();
                                                $accepted = $record->devis()->where('statut', 'accepte')->count();
                                                return $total > 0 ? number_format(($accepted / $total) * 100, 1, ',', ' ') . ' %' : '0,0 %';
                                            })
                                            ->extraAttributes(['class' => 'text-2xl font-semibold']),
                                    ]),
                                Section::make('CA total')
                                    ->icon('heroicon-m-currency-euro')
                                    ->columnSpan(1)
                                    ->schema([
                                        TextEntry::make('ca_total_card')
                                            ->label('')
                                            ->getStateUsing(fn ($record) => number_format((float) $record->factures()->sum('montant_ttc'), 2, ',', ' ') . ' €')
                                            ->extraAttributes(['class' => 'text-2xl font-semibold']),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        // Ligne 2: 2 colonnes (répartition / informations commerciales)
                        InfoGrid::make(2)
                            ->schema([
                                Section::make('Répartition des devis')
                                    ->schema([
                                        InfoGrid::make(1)
                                            ->schema([
                                                TextEntry::make('devis_acceptes')
                                                    ->label('Acceptés')
                                                    ->getStateUsing(fn ($record) => $record->devis()->where('statut', 'accepte')->count()),
                                                TextEntry::make('devis_en_attente')
                                                    ->label('En attente')
                                                    ->getStateUsing(fn ($record) => $record->devis()->where('statut', 'en_attente')->count()),
                                                TextEntry::make('devis_refuses')
                                                    ->label('Refusés')
                                                    ->getStateUsing(fn ($record) => $record->devis()->where('statut', 'refuse')->count()),
                                            ]),
                                    ]),
                                Section::make('Informations commerciales')
                                    ->schema([
                                        InfoGrid::make(2)
                                            ->schema([
                                                TextEntry::make('premier_devis')
                                                    ->label('Premier devis')
                                                    ->getStateUsing(fn ($record) => optional($record->devis()->oldest('created_at')->first())->created_at?->format('d/m/Y') ?? 'N/A'),
                                                TextEntry::make('dernier_devis')
                                                    ->label('Dernier devis')
                                                    ->getStateUsing(fn ($record) => optional($record->devis()->latest('created_at')->first())->created_at?->format('d/m/Y') ?? 'N/A'),
                                                TextEntry::make('panier_moyen')
                                                    ->label('Panier moyen')
                                                    ->getStateUsing(fn ($record) => number_format((float) $record->devis()->where('statut', 'accepte')->avg('montant_ttc') ?? 0, 2, ',', ' ') . ' €'),
                                                TextEntry::make('valeur_totale')
                                                    ->label('Valeur totale')
                                                    ->getStateUsing(fn ($record) => number_format((float) $record->factures()->sum('montant_ttc'), 2, ',', ' ') . ' €'),
                                            ]),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Cartes KPI (4 colonnes responsives)
            ClientQuickOverview::class,
        ];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'clientId' => $this->record->getKey(),
        ];
    }
}
