<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Infolists;
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
            ->recordAction('view')
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Détails de l\'opportunité')
                                ->icon('heroicon-o-light-bulb')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('nom')
                                                ->label('Nom'),
                                            Infolists\Components\TextEntry::make('etape')
                                                ->label('Étape'),
                                            Infolists\Components\TextEntry::make('probabilite')
                                                ->label('Probabilité')
                                                ->suffix('%'),
                                            Infolists\Components\TextEntry::make('montant')
                                                ->label('Montant')
                                                ->money('EUR'),
                                        ]),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Dates et échéances')
                                ->description('Planning de l\'opportunité')
                                ->icon('heroicon-o-calendar')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('date_cloture_prevue')
                                                ->label('Clôture prévue')
                                                ->date('d/m/Y'),
                                            Infolists\Components\TextEntry::make('date_cloture_reelle')
                                                ->label('Clôture réelle')
                                                ->date('d/m/Y'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Responsabilités')
                                ->description('Personnes impliquées')
                                ->icon('heroicon-o-user-group')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('user.name')
                                                ->label('Responsable'),
                                            Infolists\Components\IconEntry::make('active')
                                                ->label('Active')
                                                ->boolean(),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Notes et commentaires')
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
