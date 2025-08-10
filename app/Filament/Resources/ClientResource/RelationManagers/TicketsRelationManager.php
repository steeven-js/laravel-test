<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $title = 'Tickets';

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
                Tables\Columns\TextColumn::make('priorite')->label('Priorité')->searchable(),
                Tables\Columns\TextColumn::make('statut')->label('Statut')->searchable(),
                Tables\Columns\TextColumn::make('date_echeance')->label('Échéance')->dateTime()->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouveau ticket')
                    ->modalHeading('Nouveau ticket')
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
