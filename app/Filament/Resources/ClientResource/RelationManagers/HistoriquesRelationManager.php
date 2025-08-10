<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriquesRelationManager extends RelationManager
{
    protected static string $relationship = 'historiques';

    protected static ?string $title = 'Historique';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('titre')->label('Titre')->required(),
            Forms\Components\Textarea::make('description')->label('Description')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('action')->label('Action')->badge(),
                Tables\Columns\TextColumn::make('titre')->label('Titre')->limit(60),
                Tables\Columns\TextColumn::make('user_nom')->label('Par'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Voir les détails'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}


