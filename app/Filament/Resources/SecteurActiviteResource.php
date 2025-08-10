<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SecteurActiviteResource\Pages;
use App\Models\SecteurActivite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SecteurActiviteResource extends Resource
{
    protected static ?string $model = SecteurActivite::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Référentiels';

    protected static ?int $navigationSort = 20;

    protected static ?string $modelLabel = 'Secteur d\'activité';

    protected static ?string $pluralModelLabel = 'Secteurs d\'activités';

    protected static ?string $navigationLabel = 'Secteurs d\'activités';

    protected static ?string $pluralNavigationLabel = 'Secteurs d\'activités';

    protected static bool $hasTitleCaseModelLabel = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("Secteur d'activité")
                    ->description('Code NAF/APE, libellé et classification')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('code')->required()->maxLength(10),
                                Forms\Components\TextInput::make('libelle')->required()->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('division')->maxLength(2),
                                Forms\Components\TextInput::make('section')->maxLength(1),
                            ]),
                        Forms\Components\Toggle::make('actif')->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('libelle')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('division')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('section')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('actif')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->emptyStateHeading("Aucun secteur d'activité")
            ->emptyStateDescription("Ajoutez votre premier secteur d'activité pour commencer.")
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouveau secteur'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->name('view')
                    ->label('Aperçu')
                    ->icon('heroicon-o-eye')
                    ->modal()
                    ->url(null)
                    ->modalCancelActionLabel('Fermer')
                    ->modalHeading("Aperçu du secteur d'activité")
                    ->modalDescription("Détails complets du secteur d'activité sélectionné")
                    ->modalWidth('4xl')
                    ->infolist([
                        Infolists\Components\Section::make("Secteur d'activité")
                            ->description('Code NAF/APE, libellé et classification')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('code')->label('Code')->badge()->color('primary'),
                                        Infolists\Components\TextEntry::make('libelle')->label('Libellé')->size(Infolists\Components\TextEntry\TextEntrySize::Large)->weight('bold'),
                                    ]),
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('division')->label('Division')->badge(),
                                        Infolists\Components\TextEntry::make('section')->label('Section')->badge(),
                                    ]),
                                Infolists\Components\IconEntry::make('actif')->label('Actif')->boolean(),
                            ]),
                        Infolists\Components\Section::make('Informations système')
                            ->description('Métadonnées techniques')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('created_at')->label('Créé le')->dateTime(),
                                        Infolists\Components\TextEntry::make('updated_at')->label('Modifié le')->dateTime(),
                                    ]),
                            ]),
                    ]),
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
            'index' => Pages\ListSecteurActivites::route('/'),
            'create' => Pages\CreateSecteurActivite::route('/create'),
            'edit' => Pages\EditSecteurActivite::route('/{record}/edit'),
        ];
    }
}
