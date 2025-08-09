<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FactureResource\Pages;
use App\Filament\Resources\FactureResource\RelationManagers;
use App\Models\Facture;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FactureResource extends Resource
{
    protected static ?string $model = Facture::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?string $navigationGroup = 'Ventes';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero_facture')
                    ->label('Numéro de facture')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('devis_id')
                    ->label('Devis')
                    ->relationship('devis', 'numero_devis')
                    ->searchable()
                    ->preload(),
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
                Forms\Components\DatePicker::make('date_facture')
                    ->required(),
                Forms\Components\DatePicker::make('date_echeance')
                    ->required(),
                Forms\Components\Select::make('statut')
                    ->label('Statut')
                    ->required()
                    ->options([
                        'brouillon' => 'Brouillon',
                        'en_attente' => 'En attente',
                        'envoyee' => 'Envoyée',
                        'payee' => 'Payée',
                        'en_retard' => 'En retard',
                        'annulee' => 'Annulée',
                    ])
                    ->default('en_attente'),
                Forms\Components\Select::make('statut_envoi')
                    ->label("Statut d'envoi")
                    ->required()
                    ->options([
                        'non_envoyee' => 'Non envoyée',
                        'envoyee' => 'Envoyée',
                        'echec_envoi' => "Échec d'envoi",
                    ])
                    ->default('non_envoyee'),
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
                Forms\Components\Textarea::make('conditions_paiement')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('date_paiement'),
                Forms\Components\TextInput::make('mode_paiement')
                    ->maxLength(255),
                Forms\Components\Textarea::make('reference_paiement')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('archive')
                    ->required(),
                Forms\Components\DateTimePicker::make('date_envoi_client'),
                Forms\Components\DateTimePicker::make('date_envoi_admin'),
                Forms\Components\Select::make('mode_paiement_propose')
                    ->label('Mode de paiement proposé')
                    ->required()
                    ->options([
                        'virement' => 'Virement',
                        'stripe' => 'Stripe',
                    ])
                    ->default('virement'),
                Forms\Components\TextInput::make('stripe_payment_url')
                    ->maxLength(2048),
                Forms\Components\TextInput::make('stripe_session_id')
                    ->maxLength(512),
                Forms\Components\TextInput::make('stripe_payment_intent_id')
                    ->maxLength(512),
                Forms\Components\TextInput::make('stripe_invoice_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('stripe_customer_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('stripe_receipt_url')
                    ->maxLength(255),
                Forms\Components\TextInput::make('stripe_status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('stripe_metadata'),
                Forms\Components\DateTimePicker::make('stripe_created_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_facture')
                    ->searchable(),
                Tables\Columns\TextColumn::make('devis.numero_devis')
                    ->label('Devis')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('administrateur.name')
                    ->label('Administrateur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_facture')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut_envoi')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('date_paiement')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mode_paiement')
                    ->searchable(),
                Tables\Columns\IconColumn::make('archive')
                    ->boolean(),
                Tables\Columns\TextColumn::make('date_envoi_client')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_envoi_admin')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mode_paiement_propose')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_payment_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_session_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_payment_intent_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_invoice_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_customer_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_receipt_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_created_at')
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
                        'brouillon' => 'Brouillon',
                        'en_attente' => 'En attente',
                        'envoyee' => 'Envoyée',
                        'payee' => 'Payée',
                        'en_retard' => 'En retard',
                        'annulee' => 'Annulée',
                    ]),
                Tables\Filters\SelectFilter::make('mode_paiement_propose')
                    ->label('Mode proposé')
                    ->options([
                        'virement' => 'Virement',
                        'stripe' => 'Stripe',
                    ]),
                Tables\Filters\TernaryFilter::make('archive')->label('Archivée')->boolean(),
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
            FactureResource\RelationManagers\LignesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFactures::route('/'),
            'create' => Pages\CreateFacture::route('/create'),
            'edit' => Pages\EditFacture::route('/{record}/edit'),
        ];
    }
}
