<?php

declare(strict_types=1);

namespace App\Filament\Resources\Examples;

use App\Filament\Resources\Traits\HasStandardActions;
// use App\Models\Example; // Remplacez par votre modèle réel
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Exemple de ressource utilisant le système d'actions standardisées
 *
 * Cette ressource démontre comment utiliser le trait HasStandardActions
 * pour avoir automatiquement un aperçu modal et un bouton détail.
 */
class ExempleNouvelleResource extends Resource
{
    use HasStandardActions;

    // protected static ?string $model = Example::class; // Remplacez par votre modèle réel

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Exemples';

    protected static ?string $modelLabel = 'Exemple';

    protected static ?string $pluralModelLabel = 'Exemples';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de base')
                    ->description('Détails principaux de l\'exemple')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nom')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code')
                                    ->label('Code')
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('actif')
                            ->label('Actif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('actif')
                    ->label('Statut')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('actif')
                    ->label('Statut actif'),
            ])
            ->searchPlaceholder('Rechercher un exemple...')
            ->emptyStateIcon('heroicon-o-star')
            ->emptyStateHeading('Aucun exemple trouvé')
            ->emptyStateDescription('Créez votre premier exemple pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouvel exemple'),
            ])
            // Utilisation du trait pour les actions standardisées
            ->actions([
                // Action Aperçu (modal)
                Tables\Actions\ViewAction::make()
                    ->label('Aperçu')
                    ->modal()
                    ->url(null)
                    ->modalCancelActionLabel('Fermer')
                    ->modalHeading('Aperçu de l\'exemple')
                    ->modalDescription('Détails de l\'exemple sélectionné')
                    ->modalWidth('4xl')
                    ->infolist([
                        Infolists\Components\Section::make('Informations de base')
                            ->description('Détails principaux de l\'exemple')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Infolists\Components\TextEntry::make('nom')
                                    ->label('Nom')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('code')
                                    ->label('Code')
                                    ->badge()
                                    ->color('primary'),
                                Infolists\Components\TextEntry::make('description')
                                    ->label('Description')
                                    ->markdown()
                                    ->columnSpanFull(),
                                Infolists\Components\IconEntry::make('actif')
                                    ->label('Statut')
                                    ->boolean()
                                    ->trueIcon('heroicon-m-check-circle')
                                    ->falseIcon('heroicon-m-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                        Infolists\Components\Section::make('Informations système')
                            ->description('Métadonnées techniques')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('created_at')
                                            ->label('Créé le')
                                            ->dateTime()
                                            ->icon('heroicon-o-calendar'),
                                        Infolists\Components\TextEntry::make('updated_at')
                                            ->label('Modifié le')
                                            ->dateTime()
                                            ->icon('heroicon-o-clock'),
                                    ]),
                            ]),
                    ]),
                // Action Détail (page complète)
                Tables\Actions\Action::make('detail')
                    ->label('Détail')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn ($record): string => static::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),
                // Action Éditer
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Méthode alternative utilisant le trait pour une configuration plus simple
     *
     * Décommentez cette méthode et commentez la méthode table() ci-dessus
     * pour utiliser le trait automatiquement.
     */
    /*
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('actif')
                    ->label('Statut')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('actif')
                    ->label('Statut actif'),
            ])
            ->searchPlaceholder('Rechercher un exemple...')
            ->emptyStateIcon('heroicon-o-star')
            ->emptyStateHeading('Aucun exemple trouvé')
            ->emptyStateDescription('Créez votre premier exemple pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouvel exemple'),
            ])
            // Utilisation automatique du trait
            ->actions(static::configureStandardActions($table, static::class))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    */

    /*
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExemples::route('/'),
            'create' => Pages\CreateExemple::route('/create'),
            'view' => Pages\ViewExemple::route('/{record}'),
            'edit' => Pages\EditExemple::route('/{record}/edit'),
        ];
    }
    */
}
