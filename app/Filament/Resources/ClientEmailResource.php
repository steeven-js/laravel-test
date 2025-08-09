<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ClientEmailResource\Pages;
use App\Models\ClientEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientEmailResource extends Resource
{
    protected static ?string $model = ClientEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return 'Email client';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Emails clients';
    }

    public static function getNavigationLabel(): string
    {
        return 'Emails clients';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Destinataires & message')
                    ->description('Sélection du client, de l’utilisateur et objet du message')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('client_id')->label('Client')->relationship('client', 'nom')->searchable()->preload()->required(),
                                Forms\Components\Select::make('user_id')->label('Utilisateur')->relationship('user', 'name')->searchable()->preload()->required(),
                            ]),
                        Forms\Components\TextInput::make('objet')->required()->maxLength(255),
                        Forms\Components\Textarea::make('contenu')->required()->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Pièces & statut')
                    ->description('Pièces jointes, copie et statut d’envoi')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        Forms\Components\Textarea::make('cc')->columnSpanFull(),
                        Forms\Components\KeyValue::make('attachments')->label('Pièces jointes (JSON)')->keyLabel('Nom')->valueLabel('Valeur / URL')->addButtonLabel('Ajouter une pièce jointe')->reorderable()->nullable()->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('statut')->label('Statut')->required()->options([
                                    'envoye' => 'Envoyé',
                                    'echec' => 'Échec',
                                ])->default('envoye'),
                                Forms\Components\DateTimePicker::make('date_envoi')->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('objet')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_envoi')
                    ->dateTime()
                    ->sortable()
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
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'envoye' => 'Envoyé',
                        'echec' => 'Échec',
                    ]),
                Tables\Filters\SelectFilter::make('client_id')
                    ->relationship('client', 'nom')
                    ->label('Client'),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-paper-airplane')
            ->emptyStateHeading('Aucun email client')
            ->emptyStateDescription('Ajoutez votre premier email client pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouvel email'),
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
            'index' => Pages\ListClientEmails::route('/'),
            'create' => Pages\CreateClientEmail::route('/create'),
            'edit' => Pages\EditClientEmail::route('/{record}/edit'),
        ];
    }
}
