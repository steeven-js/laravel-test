<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\Traits\HasStandardActions;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    use HasStandardActions;

    protected static ?string $modelLabel = 'Service';

    protected static ?string $pluralModelLabel = 'Services';

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $pluralNavigationLabel = 'Services';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Référentiels';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return 'Service';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Services';
    }

    public static function getNavigationLabel(): string
    {
        return 'Services';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du service')
                    ->description('Nom, code, description et paramètres par défaut')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nom')->required()->maxLength(255),
                                Forms\Components\TextInput::make('code')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Code unique du service (optionnel).'),
                            ]),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('prix_ht')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->prefix('€')
                                    ->helperText('Prix unitaire hors taxes.'),
                                Forms\Components\TextInput::make('qte_defaut')
                                    ->numeric()->default(1)->minValue(1),
                                Forms\Components\Select::make('unite')
                                    ->required()
                                    ->options([
                                        'heure' => 'Heure',
                                        'jour' => 'Jour',
                                        'semaine' => 'Semaine',
                                        'mois' => 'Mois',
                                        'unite' => 'Unité',
                                        'forfait' => 'Forfait',
                                        'licence' => 'Licence',
                                    ])
                                    ->default('heure'),
                            ]),
                        Forms\Components\Toggle::make('actif'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery()->whereNull('deleted_at'))
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('prix_ht')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('qte_defaut')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('unite')
                    ->searchable()
                    ->toggleable(),
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
                Tables\Filters\TernaryFilter::make('actif')
                    ->boolean(),
                Tables\Filters\SelectFilter::make('unite')
                    ->options([
                        'heure' => 'Heure',
                        'journee' => 'Journée',
                        'semaine' => 'Semaine',
                        'mois' => 'Mois',
                        'unite' => 'Unité',
                        'forfait' => 'Forfait',
                        'licence' => 'Licence',
                    ]),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-briefcase')
            ->emptyStateHeading('Aucun service')
            ->emptyStateDescription('Ajoutez votre premier service pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouveau service'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Aperçu')
                        ->icon('heroicon-o-eye')
                        ->modal()
                        ->url(null)
                        ->modalCancelActionLabel('Fermer')
                        ->modalHeading('Aperçu du service')
                        ->modalDescription('Détails complets du service sélectionné')
                        ->modalWidth('4xl')
                        ->infolist([
                            Infolists\Components\Section::make('Informations du service')
                                ->description('Nom, code, description et paramètres par défaut')
                                ->icon('heroicon-o-briefcase')
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
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('prix_ht')
                                                ->label('Prix HT')
                                                ->money('EUR')
                                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                                ->color('success'),
                                            Infolists\Components\TextEntry::make('qte_defaut')
                                                ->label('Quantité par défaut')
                                                ->badge(),
                                            Infolists\Components\TextEntry::make('unite')
                                                ->label('Unité')
                                                ->badge()
                                                ->color('info'),
                                        ]),
                                    Infolists\Components\IconEntry::make('actif')
                                        ->label('Statut')
                                        ->boolean()
                                        ->size(Infolists\Components\IconEntry\IconEntrySize::Large),
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
                    Tables\Actions\Action::make('detail')
                        ->label('Détail')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('info')
                        ->url(fn (Service $record): string => static::getUrl('view', ['record' => $record]))
                        ->openUrlInNewTab(false),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
