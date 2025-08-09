<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Filament\Resources\EmailTemplateResource\RelationManagers;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category')
                    ->label('Catégorie')
                    ->required()
                    ->options([
                        'envoi_initial' => 'Envoi initial',
                        'rappel' => 'Rappel',
                        'relance' => 'Relance',
                        'confirmation' => 'Confirmation',
                    ]),
                Forms\Components\Select::make('sub_category')
                    ->label('Sous-catégorie')
                    ->required()
                    ->options([
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
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_default')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\KeyValue::make('variables')
                    ->label('Variables (JSON)')
                    ->keyLabel('Variable')
                    ->valueLabel('Exemple')
                    ->addButtonLabel('Ajouter une variable')
                    ->reorderable()
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
