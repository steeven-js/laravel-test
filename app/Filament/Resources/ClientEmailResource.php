<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientEmailResource\Pages;
use App\Filament\Resources\ClientEmailResource\RelationManagers;
use App\Models\ClientEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientEmailResource extends Resource
{
    protected static ?string $model = ClientEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('objet')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('contenu')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('cc')
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('attachments')
                    ->label('Pièces jointes (JSON)')
                    ->keyLabel('Nom')
                    ->valueLabel('Valeur / URL')
                    ->addButtonLabel('Ajouter une pièce jointe')
                    ->reorderable()
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\Select::make('statut')
                    ->label('Statut')
                    ->required()
                    ->options([
                        'envoye' => 'Envoyé',
                        'echec' => 'Échec',
                    ])
                    ->default('envoye'),
                Forms\Components\DateTimePicker::make('date_envoi')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('objet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_envoi')
                    ->dateTime()
                    ->sortable(),
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
