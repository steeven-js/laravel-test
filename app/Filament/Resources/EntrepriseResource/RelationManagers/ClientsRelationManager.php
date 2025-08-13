<?php

declare(strict_types=1);

namespace App\Filament\Resources\EntrepriseResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';

    protected static ?string $title = 'Clients';

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
                Tables\Columns\TextColumn::make('nom')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('prenom')->label('Prénom')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('telephone')->label('Téléphone')->searchable(),
                Tables\Columns\IconColumn::make('actif')->label('Actif')->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouveau client')
                    ->modalHeading('Nouveau client')
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->infolist([
                            Infolists\Components\Section::make('Informations personnelles')
                                ->description('Détails du client')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('nom')
                                                ->label('Nom'),
                                            Infolists\Components\TextEntry::make('prenom')
                                                ->label('Prénom'),
                                            Infolists\Components\TextEntry::make('email')
                                                ->label('Email'),
                                            Infolists\Components\TextEntry::make('telephone')
                                                ->label('Téléphone'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Adresse')
                                ->description('Coordonnées géographiques')
                                ->icon('heroicon-o-map-pin')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('adresse')
                                                ->label('Adresse'),
                                            Infolists\Components\TextEntry::make('ville')
                                                ->label('Ville'),
                                            Infolists\Components\TextEntry::make('code_postal')
                                                ->label('Code postal'),
                                            Infolists\Components\TextEntry::make('pays')
                                                ->label('Pays'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Statut')
                                ->description('État du client')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\IconEntry::make('actif')
                                                ->label('Actif')
                                                ->boolean(),
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Créé le')
                                                ->dateTime('d/m/Y H:i'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Notes')
                                ->description('Informations complémentaires')
                                ->icon('heroicon-o-pencil-square')
                                ->schema([
                                    Infolists\Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->columnSpanFull(),
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
