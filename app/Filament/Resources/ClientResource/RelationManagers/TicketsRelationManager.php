<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Infolists;
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
            ->recordAction('view')
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Détails du ticket')
                                ->icon('heroicon-o-lifebuoy')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('titre')
                                                ->label('Titre'),
                                            Infolists\Components\TextEntry::make('type')
                                                ->label('Type'),
                                            Infolists\Components\TextEntry::make('priorite')
                                                ->label('Priorité'),
                                            Infolists\Components\TextEntry::make('statut')
                                                ->label('Statut'),
                                        ]),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Suivi et temps')
                                ->description('Progression et échéances')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('progression')
                                                ->label('Progression')
                                                ->suffix('%'),
                                            Infolists\Components\TextEntry::make('temps_estime')
                                                ->label('Temps estimé')
                                                ->suffix('h'),
                                            Infolists\Components\TextEntry::make('temps_passe')
                                                ->label('Temps passé')
                                                ->suffix('h'),
                                        ]),
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('date_echeance')
                                                ->label('Date d\'échéance')
                                                ->dateTime('d/m/Y H:i'),
                                            Infolists\Components\TextEntry::make('date_resolution')
                                                ->label('Date de résolution')
                                                ->dateTime('d/m/Y H:i'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Attribution')
                                ->description('Personnes impliquées')
                                ->icon('heroicon-o-user-plus')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('user.name')
                                                ->label('Assigné à'),
                                            Infolists\Components\TextEntry::make('creator.name')
                                                ->label('Créé par'),
                                        ]),
                                    Infolists\Components\IconEntry::make('visible_client')
                                        ->label('Visible par le client')
                                        ->boolean(),
                                ]),
                            Infolists\Components\Section::make('Résolution')
                                ->description('Notes et solution')
                                ->icon('heroicon-o-pencil-square')
                                ->schema([
                                    Infolists\Components\TextEntry::make('notes_internes')
                                        ->label('Notes internes')
                                        ->columnSpanFull(),
                                    Infolists\Components\TextEntry::make('solution')
                                        ->label('Solution')
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
