<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmailsRelationManager extends RelationManager
{
    protected static string $relationship = 'emails';

    protected static ?string $title = 'Emails';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('sujet')->label('Sujet')->required(),
            Forms\Components\Textarea::make('contenu')->label('Contenu')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('sujet')->label('Sujet')->searchable(),
                Tables\Columns\TextColumn::make('statut')->label('Statut')->badge(),
                Tables\Columns\TextColumn::make('created_at')->label('Envoyé le')->dateTime()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouvel email'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Modifier'),
                Tables\Actions\DeleteAction::make()->label('Supprimer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}


