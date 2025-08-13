<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmailsRelationManager extends RelationManager
{
    protected static string $relationship = 'emails';

    protected static ?string $title = 'Emails';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->searchable(),
                Tables\Columns\IconColumn::make('principal')->label('Principal')->boolean(),
                Tables\Columns\IconColumn::make('actif')->label('Actif')->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouvel email'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Détails de l\'email')
                                ->icon('heroicon-o-envelope')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('email')
                                                ->label('Email'),
                                            Infolists\Components\TextEntry::make('type')
                                                ->label('Type'),
                                            Infolists\Components\IconEntry::make('principal')
                                                ->label('Principal')
                                                ->boolean(),
                                            Infolists\Components\IconEntry::make('actif')
                                                ->label('Actif')
                                                ->boolean(),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Dates')
                                ->description('Informations temporelles')
                                ->icon('heroicon-o-calendar')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Créé le')
                                                ->dateTime('d/m/Y H:i'),
                                            Infolists\Components\TextEntry::make('updated_at')
                                                ->label('Modifié le')
                                                ->dateTime('d/m/Y H:i'),
                                        ]),
                                ]),
                        ]),
                    Tables\Actions\EditAction::make()->label('Modifier'),
                    Tables\Actions\DeleteAction::make()->label('Supprimer'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
