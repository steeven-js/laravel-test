<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriquesRelationManager extends RelationManager
{
    protected static string $relationship = 'historiques';

    protected static ?string $title = 'Historiques';

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
                Tables\Columns\TextColumn::make('action')->label('Action')->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Description')->searchable(),
                Tables\Columns\TextColumn::make('date_action')->label('Date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Utilisateur')->searchable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouvel historique'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Détails de l\'action')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('action')
                                                ->label('Action'),
                                            Infolists\Components\TextEntry::make('date_action')
                                                ->label('Date d\'action')
                                                ->dateTime('d/m/Y H:i'),
                                            Infolists\Components\TextEntry::make('user.name')
                                                ->label('Utilisateur'),
                                            Infolists\Components\TextEntry::make('ip_address')
                                                ->label('Adresse IP'),
                                        ]),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Contexte')
                                ->description('Informations complémentaires')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('user_agent')
                                                ->label('User Agent'),
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Créé le')
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
