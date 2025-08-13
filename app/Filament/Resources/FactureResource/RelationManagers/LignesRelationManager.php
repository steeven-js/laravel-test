<?php

declare(strict_types=1);

namespace App\Filament\Resources\FactureResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LignesRelationManager extends RelationManager
{
    protected static string $relationship = 'lignes';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('service_id')
                ->relationship('service', 'nom')
                ->searchable()
                ->preload()
                ->label('Service')
                ->nullable()
                ->live()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if ($state) {
                        $service = \App\Models\Service::find($state);
                        if ($service) {
                            $set('description_personnalisee', $service->description);
                            $set('prix_unitaire_ht', $service->prix_ht);
                            $set('unite', $service->unite ?? 'unite');
                        }
                    }
                }),
            Forms\Components\TextInput::make('description_personnalisee')
                ->label('Description')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('quantite')
                ->numeric()
                ->default(1)
                ->minValue(1),
            Forms\Components\Select::make('unite')
                ->label('Unité')
                ->options([
                    'heure' => 'Heure',
                    'jour' => 'Jour',
                    'semaine' => 'Semaine',
                    'mois' => 'Mois',
                    'unite' => 'Unité',
                    'forfait' => 'Forfait',
                    'licence' => 'Licence',
                ])
                ->default('heure')
                ->required(),
            Forms\Components\TextInput::make('prix_unitaire_ht')
                ->numeric()
                ->inputMode('decimal')
                ->prefix('€')
                ->required(),
            Forms\Components\TextInput::make('remise_pourcentage')
                ->label('Remise (%)')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->step(0.01)
                ->default(0),
            Forms\Components\TextInput::make('taux_tva')
                ->numeric()
                ->suffix('%')
                ->default(8.5)
                ->required(),
            Forms\Components\TextInput::make('ordre')
                ->numeric()
                ->default(1),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('service.nom')->label('Service')->searchable(),
            Tables\Columns\TextColumn::make('description')
                ->label('Description')
                ->limit(40),
            Tables\Columns\TextColumn::make('quantite')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('prix_unitaire_ht')->money('EUR')->sortable(),
            Tables\Columns\TextColumn::make('taux_tva')->suffix('%')->sortable(),
            Tables\Columns\TextColumn::make('montant_ht')->money('EUR')->sortable(),
            Tables\Columns\TextColumn::make('montant_tva')->money('EUR')->sortable(),
            Tables\Columns\TextColumn::make('montant_ttc')->money('EUR')->sortable(),
            Tables\Columns\TextColumn::make('ordre')->numeric()->sortable(),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }
}
