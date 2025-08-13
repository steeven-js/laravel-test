<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EntrepriseResource\Pages;
use App\Filament\Resources\Traits\HasStandardActions;
use App\Models\Entreprise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class EntrepriseResource extends Resource
{
    use HasStandardActions;

    protected static ?string $modelLabel = 'Entreprise';

    protected static ?string $pluralModelLabel = 'Entreprises';

    protected static ?string $navigationLabel = 'Entreprises';

    protected static ?string $pluralNavigationLabel = 'Entreprises';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Entreprise::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return 'Entreprise';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Entreprises';
    }

    public static function getNavigationLabel(): string
    {
        return 'Entreprises';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité de l’entreprise')
                    ->description('Informations générales et références légales')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nom')->required()->maxLength(255),
                                Forms\Components\TextInput::make('nom_commercial')->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('siret')->maxLength(255),
                                Forms\Components\TextInput::make('siren')->maxLength(255),
                                Forms\Components\TextInput::make('secteur_activite')->maxLength(255),
                            ]),
                        Forms\Components\Select::make('secteur_activite_id')
                            ->label("Secteur d'activité (référence)")
                            ->relationship('secteurActivite', 'libelle')
                            ->searchable()
                            ->preload(),
                    ]),
                Forms\Components\Section::make('Coordonnées')
                    ->description('Adresse postale et moyens de contact')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Textarea::make('adresse')->columnSpanFull(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('ville')->maxLength(255),
                                Forms\Components\TextInput::make('code_postal')->maxLength(255),
                                Forms\Components\TextInput::make('pays')->maxLength(255)->default('France'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                PhoneInput::make('telephone')
                                    ->label('Téléphone')
                                    ->defaultCountry('FR')
                                    ->formatAsYouType(true)
                                    ->displayNumberFormat(\Ysfkaya\FilamentPhoneInput\PhoneInputNumberType::NATIONAL)
                                    ->inputNumberFormat(\Ysfkaya\FilamentPhoneInput\PhoneInputNumberType::E164),
                                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                                Forms\Components\TextInput::make('site_web')->maxLength(255),
                            ]),
                    ]),
                Forms\Components\Section::make('Paramètres')
                    ->description('Activation et notes internes')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Forms\Components\Toggle::make('actif')->required(),
                        Forms\Components\Textarea::make('notes')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery()->whereNull('deleted_at'))
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['devis', 'factures']))
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nom_commercial')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('siret')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('siren')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('secteur_activite')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('secteurActivite.libelle')
                    ->label('Secteur (réf)')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ville')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('code_postal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pays')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('site_web')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('devis_count')
                    ->label('Devis')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('factures_count')
                    ->label('Factures')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('actif')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-building-office')
            ->emptyStateHeading('Aucune entreprise')
            ->emptyStateDescription('Ajoutez votre première entreprise pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle entreprise'),
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
                        ->modalHeading('Aperçu de l\'entreprise')
                        ->modalDescription('Détails complets de l\'entreprise sélectionnée')
                        ->modalWidth('4xl')
                        ->infolist([
                            Infolists\Components\Section::make('Identité de l\'entreprise')
                                ->description('Informations générales et références légales')
                                ->icon('heroicon-o-building-office')
                                ->schema([
                                    Infolists\Components\TextEntry::make('nom')
                                        ->label('Nom')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold'),
                                    Infolists\Components\TextEntry::make('nom_commercial')
                                        ->label('Nom commercial')
                                        ->badge()
                                        ->color('info'),
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('siret')
                                                ->label('SIRET')
                                                ->badge()
                                                ->color('primary'),
                                            Infolists\Components\TextEntry::make('siren')
                                                ->label('SIREN')
                                                ->badge()
                                                ->color('primary'),
                                        ]),
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('secteur_activite')
                                                ->label('Secteur d\'activité')
                                                ->badge()
                                                ->color('warning'),
                                            Infolists\Components\TextEntry::make('secteurActivite.libelle')
                                                ->label('Secteur (référence)')
                                                ->badge()
                                                ->color('success'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Coordonnées')
                                ->description('Adresse postale et moyens de contact')
                                ->icon('heroicon-o-map-pin')
                                ->schema([
                                    Infolists\Components\TextEntry::make('adresse')
                                        ->label('Adresse')
                                        ->columnSpanFull()
                                        ->icon('heroicon-o-map-pin'),
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('ville')
                                                ->label('Ville')
                                                ->badge()
                                                ->color('info'),
                                            Infolists\Components\TextEntry::make('code_postal')
                                                ->label('Code postal')
                                                ->badge()
                                                ->color('warning'),
                                            Infolists\Components\TextEntry::make('pays')
                                                ->label('Pays')
                                                ->badge()
                                                ->color('success'),
                                        ]),
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('telephone')
                                                ->label('Téléphone')
                                                ->icon('heroicon-o-phone')
                                                ->badge()
                                                ->color('primary'),
                                            Infolists\Components\TextEntry::make('email')
                                                ->label('Email')
                                                ->icon('heroicon-o-envelope')
                                                ->badge()
                                                ->color('info'),
                                            Infolists\Components\TextEntry::make('site_web')
                                                ->label('Site web')
                                                ->icon('heroicon-o-globe-alt')
                                                ->badge()
                                                ->color('success'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Paramètres')
                                ->description('Activation et notes internes')
                                ->icon('heroicon-o-adjustments-horizontal')
                                ->schema([
                                    Infolists\Components\IconEntry::make('actif')
                                        ->label('Statut')
                                        ->boolean()
                                        ->size(Infolists\Components\IconEntry\IconEntrySize::Large)
                                        ->trueIcon('heroicon-m-check-circle')
                                        ->falseIcon('heroicon-m-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                    Infolists\Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->markdown()
                                        ->columnSpanFull()
                                        ->placeholder('Aucune note'),
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
                    Tables\Actions\Action::make('detail')
                        ->label('Détail')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('info')
                        ->url(fn (Entreprise $record): string => static::getUrl('view', ['record' => $record]))
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
            \App\Filament\Resources\EntrepriseResource\RelationManagers\ClientsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntreprises::route('/'),
            'create' => Pages\CreateEntreprise::route('/create'),
            'view' => Pages\ViewEntreprise::route('/{record}'),
            'edit' => Pages\EditEntreprise::route('/{record}/edit'),
        ];
    }
}
