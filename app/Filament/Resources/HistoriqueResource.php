<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\HistoriqueResource\Pages;
use App\Models\Historique;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriqueResource extends Resource
{
    use \App\Filament\Resources\Traits\HasHistoriqueResource;

    protected static ?string $modelLabel = 'Historique';

    protected static ?string $pluralModelLabel = 'Historiques';

    protected static ?string $navigationLabel = 'Historiques';

    protected static ?string $pluralNavigationLabel = 'Historiques';

    protected static bool $hasTitleCaseModelLabel = false;

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
            ->recordUrl(null)
            ->recordAction('view')
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
                    ->dateTime('d/m/Y H:i')
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->name('view')
                        ->label('Aperçu')
                        ->icon('heroicon-o-eye')
                        ->modal()
                        ->url(null)
                        ->modalCancelActionLabel('Fermer')
                        ->modalHeading('Aperçu de l\'historique')
                        ->modalDescription('Détails complets de l\'événement d\'historique sélectionné')
                        ->modalWidth('4xl')
                        ->infolist([
                            Infolists\Components\Section::make('Événement')
                                ->description('Entité, action et titre de l\'historique')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('entite_type')
                                                ->label('Type d\'entité')
                                                ->badge()
                                                ->color('primary'),
                                            Infolists\Components\TextEntry::make('entite_id')
                                                ->label('ID de l\'entité')
                                                ->badge()
                                                ->color('info'),
                                            Infolists\Components\TextEntry::make('action')
                                                ->label('Action')
                                                ->badge()
                                                ->color('warning'),
                                        ]),
                                    Infolists\Components\TextEntry::make('titre')
                                        ->label('Titre')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold'),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Données')
                                ->description('Avant, après et supplémentaires')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('donnees_avant')
                                                ->label('Données avant')
                                                ->markdown()
                                                ->placeholder('Aucune donnée'),
                                            Infolists\Components\TextEntry::make('donnees_apres')
                                                ->label('Données après')
                                                ->markdown()
                                                ->placeholder('Aucune donnée'),
                                            Infolists\Components\TextEntry::make('donnees_supplementaires')
                                                ->label('Données supplémentaires')
                                                ->markdown()
                                                ->placeholder('Aucune donnée'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Utilisateur & contexte')
                                ->description('Utilisateur, IP et user agent')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('user.name')
                                                ->label('Utilisateur')
                                                ->badge()
                                                ->color('primary'),
                                            Infolists\Components\TextEntry::make('user_nom')
                                                ->label('Nom utilisateur')
                                                ->badge()
                                                ->color('info'),
                                            Infolists\Components\TextEntry::make('user_email')
                                                ->label('Email utilisateur')
                                                ->badge()
                                                ->color('warning'),
                                        ]),
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('ip_address')
                                                ->label('Adresse IP')
                                                ->badge()
                                                ->color('success')
                                                ->icon('heroicon-o-computer-desktop'),
                                            Infolists\Components\TextEntry::make('user_agent')
                                                ->label('User Agent')
                                                ->badge()
                                                ->color('gray')
                                                ->icon('heroicon-o-globe-alt'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Informations système')
                                ->description('Métadonnées techniques')
                                ->icon('heroicon-o-cog')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Créé le')
                                                ->dateTime('d/m/Y H:i')
                                                ->icon('heroicon-o-calendar'),
                                            Infolists\Components\TextEntry::make('updated_at')
                                                ->label('Modifié le')
                                                ->dateTime('d/m/Y H:i')
                                                ->icon('heroicon-o-clock'),
                                        ]),
                                ]),
                        ]),
                    Tables\Actions\EditAction::make(),
                ]),
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

    protected static function getDefaultRelations(): array
    {
        return [
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
