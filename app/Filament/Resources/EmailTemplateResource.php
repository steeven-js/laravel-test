<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $modelLabel = 'Template email';

    protected static ?string $pluralModelLabel = 'Templates emails';

    protected static ?string $navigationLabel = 'Templates emails';

    protected static ?string $pluralNavigationLabel = 'Templates emails';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return 'Modèle d\'email';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Modèles d\'email';
    }

    public static function getNavigationLabel(): string
    {
        return 'Modèles d\'email';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Métadonnées')
                    ->description('Nom, catégories et statut du template')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\TextInput::make('subject')->required()->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('category')->label('Catégorie')->required()->options([
                                    'envoi_initial' => 'Envoi initial',
                                    'rappel' => 'Rappel',
                                    'relance' => 'Relance',
                                    'confirmation' => 'Confirmation',
                                ]),
                                Forms\Components\Select::make('sub_category')->label('Sous-catégorie')->required()->options([
                                    'promotionnel' => 'Promotionnel',
                                    'concis_direct' => 'Concis & direct',
                                    'standard_professionnel' => 'Standard professionnel',
                                    'detaille_etapes' => 'Détaillé (étapes)',
                                    'personnalise_chaleureux' => 'Personnalisé & chaleureux',
                                    'rappel_offre_speciale' => 'Rappel offre spéciale',
                                    'rappel_date_expiration' => "Rappel date d'expiration",
                                    'rappel_standard' => 'Rappel standard',
                                    'suivi_standard' => 'Suivi standard',
                                    'suivi_ajustements' => 'Suivi ajustements',
                                    'suivi_feedback' => 'Suivi feedback',
                                    'confirmation_infos' => 'Confirmation avec infos',
                                    'confirmation_etapes' => 'Confirmation (étapes)',
                                    'confirmation_standard' => 'Confirmation standard',
                                ]),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_default')->required(),
                                Forms\Components\Toggle::make('is_active')->required(),
                            ]),
                    ]),
                Forms\Components\Section::make('Contenu')
                    ->description('Corps et variables disponibles')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Textarea::make('body')->required()->columnSpanFull(),
                        Forms\Components\KeyValue::make('variables')->label('Variables (JSON)')->keyLabel('Variable')->valueLabel('Exemple')->addButtonLabel('Ajouter une variable')->reorderable()->nullable()->columnSpanFull(),
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sub_category')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
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
            ->emptyStateIcon('heroicon-o-envelope')
            ->emptyStateHeading("Aucun modèle d'email")
            ->emptyStateDescription('Ajoutez votre premier modèle pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouveau modèle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modal()
                    ->url(null)
                    ->modalCancelActionLabel('Fermer')
                    ->infolist([
                        Infolists\Components\Section::make('Métadonnées')
                            ->description('Nom, catégories et statut du template')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Nom'),
                                Infolists\Components\TextEntry::make('subject')
                                    ->label('Objet'),
                                Infolists\Components\TextEntry::make('category')
                                    ->label('Catégorie'),
                                Infolists\Components\TextEntry::make('sub_category')
                                    ->label('Sous-catégorie'),
                                Infolists\Components\IconEntry::make('is_default')
                                    ->label('Par défaut')
                                    ->boolean(),
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Actif')
                                    ->boolean(),
                            ]),
                        Infolists\Components\Section::make('Contenu')
                            ->description('Corps et variables disponibles')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Infolists\Components\TextEntry::make('body')
                                    ->label('Corps du message')
                                    ->markdown(),
                                Infolists\Components\TextEntry::make('variables')
                                    ->label('Variables')
                                    ->json(),
                                Infolists\Components\TextEntry::make('description')
                                    ->label('Description')
                                    ->markdown(),
                            ]),
                        Infolists\Components\Section::make('Informations système')
                            ->description('Métadonnées techniques')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Créé le')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Modifié le')
                                    ->dateTime(),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
