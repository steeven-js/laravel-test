<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TodosRelationManager extends RelationManager
{
    protected static string $relationship = 'todos';

    protected static ?string $title = 'Tâches';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('titre')->label('Titre')->searchable(),
                Tables\Columns\IconColumn::make('termine')->label('Terminé')->boolean(),
                Tables\Columns\TextColumn::make('priorite')->label('Priorité')->searchable(),
                Tables\Columns\TextColumn::make('date_echeance')->label('Échéance')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('termine')->label('Terminé')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle tâche'),
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
