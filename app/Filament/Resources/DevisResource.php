<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DevisResource\Pages;
use App\Filament\Resources\DevisResource\RelationManagers;
use App\Models\Devis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DevisResource extends Resource
{
    protected static ?string $model = Devis::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Ventes';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero_devis')
                    ->label('Numéro de devis')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('administrateur_id')
                    ->label('Administrateur')
                    ->relationship('administrateur', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('date_devis'),
                Forms\Components\DatePicker::make('date_validite'),
                Forms\Components\Select::make('statut')
                    ->label('Statut')
                    ->required()
                    ->options([
                        'brouillon' => 'Brouillon',
                        'en_attente' => 'En attente',
                        'envoye' => 'Envoyé',
                        'accepte' => 'Accepté',
                        'refuse' => 'Refusé',
                        'expire' => 'Expiré',
                    ])
                    ->default('en_attente'),
                Forms\Components\Select::make('statut_envoi')
                    ->label("Statut d'envoi")
                    ->required()
                    ->options([
                        'non_envoye' => 'Non envoyé',
                        'envoye' => 'Envoyé',
                        'echec_envoi' => "Échec d'envoi",
                    ])
                    ->default('non_envoye'),
                Forms\Components\DateTimePicker::make('date_envoi_client'),
                Forms\Components\DateTimePicker::make('date_envoi_admin'),
                Forms\Components\TextInput::make('pdf_file')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pdf_url')
                    ->maxLength(255),
                Forms\Components\TextInput::make('objet')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('montant_ht')
                    ->label('Montant HT')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->prefix('€')
                    ->default(0),
                Forms\Components\TextInput::make('taux_tva')
                    ->label('Taux TVA')
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->default(8.5),
                Forms\Components\TextInput::make('montant_tva')
                    ->label('Montant TVA')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->prefix('€')
                    ->default(0),
                Forms\Components\TextInput::make('montant_ttc')
                    ->label('Montant TTC')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->prefix('€')
                    ->default(0),
                Forms\Components\Textarea::make('conditions')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('date_acceptation'),
                Forms\Components\Toggle::make('archive')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_devis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('administrateur.name')
                    ->label('Administrateur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_devis')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_validite')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut_envoi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_envoi_client')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_envoi_admin')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pdf_file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pdf_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('objet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('montant_ht')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('taux_tva')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_tva')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_ttc')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_acceptation')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('archive')
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
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'brouillon' => 'Brouillon',
                        'en_attente' => 'En attente',
                        'envoye' => 'Envoyé',
                        'accepte' => 'Accepté',
                        'refuse' => 'Refusé',
                        'expire' => 'Expiré',
                    ]),
                Tables\Filters\SelectFilter::make('statut_envoi')
                    ->label("Statut d'envoi")
                    ->options([
                        'non_envoye' => 'Non envoyé',
                        'envoye' => 'Envoyé',
                        'echec_envoi' => "Échec d'envoi",
                    ]),
                Tables\Filters\TernaryFilter::make('archive')->label('Archivé')->boolean(),
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
            DevisResource\RelationManagers\LignesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevis::route('/'),
            'create' => Pages\CreateDevis::route('/create'),
            'edit' => Pages\EditDevis::route('/{record}/edit'),
        ];
    }
}
