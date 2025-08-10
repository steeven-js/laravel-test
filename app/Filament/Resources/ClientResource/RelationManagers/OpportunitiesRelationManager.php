<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OpportunitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'opportunities';

    protected static ?string $title = 'Opportunités';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('nom')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('etape')->label('Étape')->searchable(),
                Tables\Columns\TextColumn::make('montant')->label('Montant')->money('EUR')->sortable(),
                Tables\Columns\TextColumn::make('date_cloture_prevue')->label('Clôture prévue')->date()->sortable(),
                Tables\Columns\IconColumn::make('active')->label('Active')->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle opportunité')
                    ->modalHeading('Nouvelle opportunité')
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Éditer'),
                Tables\Actions\DeleteAction::make()->label('Supprimer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
