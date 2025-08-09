<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\HistoriqueResource\Pages;
use App\Models\Historique;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriqueResource extends Resource
{
    protected static ?string $model = Historique::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Événement')
                    ->description('Entité, action et titre de l\'historique')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('entite_type')->required()->maxLength(255),
                                Forms\Components\TextInput::make('entite_id')->required()->numeric(),
                                Forms\Components\TextInput::make('action')->required()->maxLength(255),
                            ]),
                        Forms\Components\TextInput::make('titre')->required()->maxLength(255),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Données')
                    ->description('Avant, après et supplémentaires')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('donnees_avant'),
                                Forms\Components\TextInput::make('donnees_apres'),
                                Forms\Components\TextInput::make('donnees_supplementaires'),
                            ]),
                    ]),
                Forms\Components\Section::make('Utilisateur & contexte')
                    ->description('Utilisateur, IP et user agent')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('user_id')->relationship('user', 'name')->required(),
                                Forms\Components\TextInput::make('user_nom')->required()->maxLength(255),
                                Forms\Components\TextInput::make('user_email')->email()->required()->maxLength(255),
                            ]),
                        Forms\Components\TextInput::make('ip_address')->maxLength(255),
                        Forms\Components\Textarea::make('user_agent')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entite_type')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('entite_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('action')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('titre')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user_nom')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user_email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->emptyStateHeading('Aucun historique')
            ->emptyStateDescription("Il n'y a pas encore d'événements d'historique.")
            ->emptyStateActions([
                // pas de création manuelle d'historique
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistoriques::route('/'),
            'create' => Pages\CreateHistorique::route('/create'),
            'edit' => Pages\EditHistorique::route('/{record}/edit'),
        ];
    }
}
