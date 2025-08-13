<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\FactureEnvoiStatus;
use App\Enums\FactureStatus;
use App\Filament\Resources\FactureResource\Pages;
use App\Models\Facture;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FactureResource extends Resource
{
    protected static ?string $modelLabel = 'Facture';

    protected static ?string $pluralModelLabel = 'Factures';

    protected static ?string $navigationLabel = 'Factures';

    protected static ?string $pluralNavigationLabel = 'Factures';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Facture::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return 'Facture';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Factures';
    }

    public static function getNavigationLabel(): string
    {
        return 'Factures';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->description('Client, devis, dates et statuts de la facture')
                    ->icon('heroicon-o-receipt-refund')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('numero_facture')->label('Numéro de facture')->required()->unique(ignoreRecord: true)->maxLength(255),
                                Forms\Components\Select::make('devis_id')->label('Devis')->relationship('devis', 'numero_devis')->searchable()->preload(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('client_id')->label('Client')->relationship('client', 'nom')->searchable()->preload()->required(),
                                Forms\Components\Select::make('administrateur_id')->label('Administrateur')->relationship('administrateur', 'name')->searchable()->preload(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_facture')->required(),
                                Forms\Components\DatePicker::make('date_echeance')->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\ToggleButtons::make('statut')
                                    ->label('Statut')
                                    ->inline()
                                    ->options(FactureStatus::class)
                                    ->required()
                                    ->default(FactureStatus::Emise),
                                Forms\Components\ToggleButtons::make('statut_envoi')
                                    ->label("Statut d'envoi")
                                    ->inline()
                                    ->options(FactureEnvoiStatus::class)
                                    ->required()
                                    ->default(FactureEnvoiStatus::NonEnvoyee),
                            ]),
                    ]),
                Forms\Components\Section::make('Montants')
                    ->description('HT, TVA et TTC')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('montant_ht')->label('Montant HT')->required()->numeric()->inputMode('decimal')->prefix('€')->default(0),
                                Forms\Components\TextInput::make('taux_tva')->label('Taux TVA')->required()->numeric()->suffix('%')->default(8.5),
                                Forms\Components\TextInput::make('montant_ttc')->label('Montant TTC')->required()->numeric()->inputMode('decimal')->prefix('€')->default(0),
                            ]),
                        Forms\Components\TextInput::make('montant_tva')->label('Montant TVA')->required()->numeric()->inputMode('decimal')->prefix('€')->default(0),
                    ]),
                Forms\Components\Section::make('Documents & envois')
                    ->description('Fichiers et dates d’envoi')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('pdf_file')->maxLength(255),
                                Forms\Components\TextInput::make('pdf_url')->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('objet')->maxLength(255),
                                Forms\Components\Toggle::make('archive')->label('Archivée')->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('date_envoi_client'),
                                Forms\Components\DateTimePicker::make('date_envoi_admin'),
                            ]),
                    ]),
                Forms\Components\Section::make('Paiement')
                    ->description('Informations et preuves de paiement')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_paiement'),
                                Forms\Components\TextInput::make('mode_paiement')->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make('reference_paiement')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Stripe')
                    ->description('Informations de paiement en ligne')
                    ->icon('heroicon-o-bolt')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('mode_paiement_propose')->label('Mode de paiement proposé')->required()->options([
                                    'virement' => 'Virement',
                                    'stripe' => 'Stripe',
                                ])->default('virement'),
                                Forms\Components\TextInput::make('stripe_payment_url')->maxLength(2048),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('stripe_session_id')->maxLength(512),
                                Forms\Components\TextInput::make('stripe_payment_intent_id')->maxLength(512),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('stripe_invoice_id')->maxLength(255),
                                Forms\Components\TextInput::make('stripe_customer_id')->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('stripe_receipt_url')->maxLength(255),
                                Forms\Components\TextInput::make('stripe_status')->maxLength(255),
                            ]),
                        Forms\Components\TextInput::make('stripe_metadata'),
                        Forms\Components\DateTimePicker::make('stripe_created_at'),
                    ]),
                Forms\Components\Section::make('Contenus')
                    ->description('Description et notes internes')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery()->whereNull('deleted_at'))
            ->recordUrl(fn ($record): string => static::getUrl('view', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('numero_facture')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('devis.numero_devis')
                    ->label('Devis')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('administrateur.name')
                    ->label('Administrateur')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_facture')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en_attente' => 'En attente',
                        default => FactureStatus::from($state)->getLabel(),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'en_attente' => 'warning',
                        'brouillon' => 'gray',
                        'emise' => 'info',
                        'envoyee' => 'warning',
                        'payee' => 'success',
                        'en_retard' => 'danger',
                        'annulee' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'en_attente' => 'heroicon-m-clock',
                        'brouillon' => 'heroicon-m-document-text',
                        'emise' => 'heroicon-m-document',
                        'envoyee' => 'heroicon-m-paper-airplane',
                        'payee' => 'heroicon-m-banknotes',
                        'en_retard' => 'heroicon-m-exclamation-triangle',
                        'annulee' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('statut_envoi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => FactureEnvoiStatus::from($state)->getLabel())
                    ->color(fn (string $state): string => match ($state) {
                        'non_envoyee' => 'gray',
                        'envoyee' => 'success',
                        'echec_envoi' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'non_envoyee' => 'heroicon-m-paper-airplane',
                        'envoyee' => 'heroicon-m-check-circle',
                        'echec_envoi' => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pdf_file')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pdf_url')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('objet')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('montant_ht')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('taux_tva')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('montant_tva')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('montant_ttc')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_paiement')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mode_paiement')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('archive')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_envoi_client')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_envoi_admin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mode_paiement_propose')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_payment_url')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_session_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_payment_intent_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_invoice_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_customer_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_receipt_url')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_status')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('statut')
                    ->options(FactureStatus::class),
                Tables\Filters\SelectFilter::make('statut_envoi')
                    ->label("Statut d'envoi")
                    ->options(FactureEnvoiStatus::class),
                Tables\Filters\SelectFilter::make('mode_paiement_propose')
                    ->label('Mode proposé')
                    ->options([
                        'virement' => 'Virement',
                        'stripe' => 'Stripe',
                    ]),
                Tables\Filters\TernaryFilter::make('archive')->label('Archivée')->boolean(),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-receipt-refund')
            ->emptyStateHeading('Aucune facture')
            ->emptyStateDescription('Ajoutez votre première facture pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle facture'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record): string => static::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('preview_pdf_modal')
                        ->label('Aperçu PDF')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading(fn ($record) => "Aperçu PDF - Facture {$record->numero_facture}")
                        ->modalContent(fn ($record) => view('pdf.preview-modal-facture', [
                            'pdfUrl' => route('factures.pdf', $record),
                            'facture' => $record,
                        ]))
                        ->modalWidth('7xl')
                        ->modalCancelActionLabel('Fermer')
                        ->modalSubmitAction(false)
                        ->visible(fn ($record) => $record !== null),

                    Tables\Actions\Action::make('detail')
                        ->label('Détail')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('info')
                        ->url(fn ($record): string => static::getUrl('view', ['record' => $record]))
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
            FactureResource\RelationManagers\LignesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFactures::route('/'),
            'create' => Pages\CreateFacture::route('/create'),
            'view' => Pages\ViewFacture::route('/{record}'),
            'edit' => Pages\EditFacture::route('/{record}/edit'),
        ];
    }
}
