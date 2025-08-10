<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ClientEmailResource\Pages;
use App\Models\ClientEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
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
            ->recordUrl(null)
            ->recordAction('view')
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
                Tables\Actions\ViewAction::make()
                    ->name('view')
                    ->label('Aperçu')
                    ->icon('heroicon-o-eye')
                    ->modal()
                    ->url(null)
                    ->modalHeading('Aperçu de l\'email client')
                    ->modalDescription('Détails complets de l\'email client sélectionné')
                    ->modalWidth('4xl')
                    ->infolist([
                        Infolists\Components\Section::make('Destinataires & message')
                            ->description('Client, utilisateur et contenu de l\'email')
                            ->icon('heroicon-o-paper-airplane')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('client.nom')
                                            ->label('Client')
                                            ->badge()
                                            ->color('primary'),
                                        Infolists\Components\TextEntry::make('user.name')
                                            ->label('Utilisateur')
                                            ->badge()
                                            ->color('info'),
                                    ]),
                                Infolists\Components\TextEntry::make('objet')
                                    ->label('Objet')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('contenu')
                                    ->label('Contenu')
                                    ->markdown()
                                    ->columnSpanFull(),
                            ]),
                        Infolists\Components\Section::make('Pièces jointes & statut')
                            ->description('Pièces jointes, copie et statut d\'envoi')
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                Infolists\Components\TextEntry::make('cc')
                                    ->label('Copie (CC)')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('Aucune copie'),
                                Infolists\Components\TextEntry::make('attachments')
                                    ->label('Pièces jointes')
                                    ->markdown()
                                    ->columnSpanFull()
                                    ->getStateUsing(fn ($record) => json_encode($record->attachments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                                    ->placeholder('Aucune pièce jointe'),
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('statut')
                                            ->label('Statut')
                                            ->badge()
                                            ->color(fn ($record) => $record->statut === 'envoye' ? 'success' : 'danger'),
                                        Infolists\Components\TextEntry::make('date_envoi')
                                            ->label('Date d\'envoi')
                                            ->dateTime()
                                            ->icon('heroicon-o-clock'),
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
                                            ->dateTime()
                                            ->icon('heroicon-o-calendar'),
                                        Infolists\Components\TextEntry::make('updated_at')
                                            ->label('Modifié le')
                                            ->dateTime()
                                            ->icon('heroicon-o-clock'),
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
            'index' => Pages\ListClientEmails::route('/'),
            'create' => Pages\CreateClientEmail::route('/create'),
            'edit' => Pages\EditClientEmail::route('/{record}/edit'),
        ];
    }
}
