<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\DevisEnvoiStatus;
use App\Enums\DevisStatus;
use App\Filament\Resources\DevisResource\Pages;
use App\Filament\Resources\Traits\HasStandardActions;
use App\Models\Devis;
use App\Models\Facture;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DevisResource extends Resource
{
    use HasStandardActions;

    protected static ?string $modelLabel = 'Devis';

    protected static ?string $pluralModelLabel = 'Devis';

    protected static ?string $navigationLabel = 'Devis';

    protected static ?string $pluralNavigationLabel = 'Devis';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Devis::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return 'Devis';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Devis';
    }

    public static function getNavigationLabel(): string
    {
        return 'Devis';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->description('Client, dates, administrateur et statut du devis')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('numero_devis')->label('Numéro de devis')->required()->unique(ignoreRecord: true)->maxLength(255),
                                Forms\Components\Select::make('client_id')->label('Client')->relationship('client', 'nom')->searchable()->preload()->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('administrateur_id')->label('Administrateur')->relationship('administrateur', 'name')->searchable()->preload(),
                                Forms\Components\ToggleButtons::make('statut')
                                    ->label('Statut')
                                    ->inline()
                                    ->options(DevisStatus::class)
                                    ->required()
                                    ->default(DevisStatus::EnAttente),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_devis'),
                                Forms\Components\DatePicker::make('date_validite'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\ToggleButtons::make('statut_envoi')
                                    ->label("Statut d'envoi")
                                    ->inline()
                                    ->options(DevisEnvoiStatus::class)
                                    ->required()
                                    ->default(DevisEnvoiStatus::NonEnvoye),
                                Forms\Components\Toggle::make('archive')->label('Archivé')->required(),
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
                Forms\Components\Section::make('Documents')
                    ->description('Pièces liées au devis')
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
                                Forms\Components\DatePicker::make('date_acceptation'),
                            ]),
                    ]),
                Forms\Components\Section::make('Contenus')
                    ->description('Description, conditions et notes internes')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                        Forms\Components\Textarea::make('conditions')->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Envois')
                    ->description('Dates d\'envoi client et admin')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('date_envoi_client'),
                                Forms\Components\DateTimePicker::make('date_envoi_admin'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery()->whereNull('deleted_at'))
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('lignes'))
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('numero_devis')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lignes_count')
                    ->label('Lignes')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('administrateur.name')
                    ->label('Administrateur')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_devis')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_validite')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DevisStatus::from($state)->getLabel())
                    ->color(fn (string $state): string => match ($state) {
                        'brouillon' => 'gray',
                        'en_attente' => 'warning',
                        'accepte' => 'success',
                        'refuse' => 'danger',
                        'expire' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'brouillon' => 'heroicon-m-document-text',
                        'en_attente' => 'heroicon-m-clock',
                        'accepte' => 'heroicon-m-check-circle',
                        'refuse' => 'heroicon-m-x-circle',
                        'expire' => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('statut_envoi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DevisEnvoiStatus::from($state)->getLabel())
                    ->color(fn (string $state): string => match ($state) {
                        'non_envoye' => 'gray',
                        'envoye' => 'success',
                        'echec_envoi' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'non_envoye' => 'heroicon-m-paper-airplane',
                        'envoye' => 'heroicon-m-check-circle',
                        'echec_envoi' => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_envoi_client')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_envoi_admin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
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
                Tables\Columns\TextColumn::make('date_acceptation')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('archive')
                    ->boolean()
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
                    ->options(DevisStatus::class),
                Tables\Filters\SelectFilter::make('statut_envoi')
                    ->label("Statut d'envoi")
                    ->options(DevisEnvoiStatus::class),
                Tables\Filters\TernaryFilter::make('archive')->label('Archivé')->boolean(),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('Aucun devis')
            ->emptyStateDescription('Ajoutez votre premier devis pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouveau devis'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('preview_pdf_modal')
                        ->label('Aperçu PDF')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading(fn ($record) => "Aperçu PDF - Devis {$record->numero_devis}")
                        ->modalContent(fn ($record) => view('pdf.preview-modal', [
                            'pdfUrl' => route('devis.pdf', $record),
                            'devis' => $record,
                        ]))
                        ->modalWidth('7xl')
                        ->modalCancelActionLabel('Fermer')
                        ->modalSubmitAction(false)
                        ->visible(fn ($record) => $record !== null),

                    Tables\Actions\Action::make('convert_to_invoice')
                        ->label('Transformer en facture')
                        ->icon('heroicon-m-arrow-right-circle')
                        ->color('success')
                        ->disabled(fn (Devis $record): bool => Facture::query()->where('devis_id', $record->id)->exists())
                        ->tooltip(fn (Devis $record): ?string => Facture::query()->where('devis_id', $record->id)->exists()
                            ? 'Une facture existe déjà pour ce devis.'
                            : 'Le statut du devis sera automatiquement passé à ‘Accepté’ lors de la transformation.')
                        ->url(fn (Devis $record): string => static::getUrl('transform', ['record' => $record]))
                        ->openUrlInNewTab(false),

                    Tables\Actions\ViewAction::make()
                        ->modal()
                        ->url(null)
                        ->modalCancelActionLabel('Fermer')
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Client, dates, administrateur et statut du devis')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('numero_devis')
                                                ->label('Numéro de devis'),
                                            Infolists\Components\TextEntry::make('client.nom')
                                                ->label('Client'),
                                            Infolists\Components\TextEntry::make('administrateur.name')
                                                ->label('Administrateur'),
                                            Infolists\Components\TextEntry::make('statut')
                                                ->label('Statut')
                                                ->badge()
                                                ->formatStateUsing(fn (string $state): string => DevisStatus::from($state)->getLabel())
                                                ->color(fn (string $state): string => match ($state) {
                                                    'brouillon' => 'gray',
                                                    'en_attente' => 'warning',
                                                    'accepte' => 'success',
                                                    'refuse' => 'danger',
                                                    'expire' => 'gray',
                                                    default => 'gray',
                                                })
                                                ->icon(fn (string $state): string => match ($state) {
                                                    'brouillon' => 'heroicon-m-document-text',
                                                    'en_attente' => 'heroicon-m-clock',
                                                    'accepte' => 'heroicon-m-check-circle',
                                                    'refuse' => 'heroicon-m-x-circle',
                                                    'expire' => 'heroicon-m-exclamation-triangle',
                                                    default => 'heroicon-m-question-mark-circle',
                                                }),
                                                                                    Infolists\Components\TextEntry::make('date_devis')
                                            ->label('Date du devis')
                                            ->date('d/m/Y'),
                                        Infolists\Components\TextEntry::make('date_validite')
                                            ->label('Date de validité')
                                            ->date('d/m/Y'),
                                            Infolists\Components\TextEntry::make('statut_envoi')
                                                ->label('Statut d\'envoi')
                                                ->badge()
                                                ->formatStateUsing(fn (string $state): string => DevisEnvoiStatus::from($state)->getLabel())
                                                ->color(fn (string $state): string => match ($state) {
                                                    'non_envoye' => 'gray',
                                                    'envoye' => 'success',
                                                    'echec_envoi' => 'danger',
                                                    default => 'gray',
                                                })
                                                ->icon(fn (string $state): string => match ($state) {
                                                    'non_envoye' => 'heroicon-m-paper-airplane',
                                                    'envoye' => 'heroicon-m-check-circle',
                                                    'echec_envoi' => 'heroicon-m-exclamation-triangle',
                                                    default => 'heroicon-m-question-mark-circle',
                                                }),
                                            Infolists\Components\IconEntry::make('archive')
                                                ->label('Archivé')
                                                ->boolean(),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Montants')
                                ->description('HT, TVA et TTC')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('montant_ht')
                                                ->label('Montant HT')
                                                ->money('EUR'),
                                            Infolists\Components\TextEntry::make('taux_tva')
                                                ->label('Taux TVA')
                                                ->suffix('%'),
                                            Infolists\Components\TextEntry::make('montant_tva')
                                                ->label('Montant TVA')
                                                ->money('EUR'),
                                            Infolists\Components\TextEntry::make('montant_ttc')
                                                ->label('Montant TTC')
                                                ->money('EUR'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Documents')
                                ->description('Pièces liées au devis')
                                ->icon('heroicon-o-paper-clip')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('pdf_file')
                                                ->label('Fichier PDF'),
                                            Infolists\Components\TextEntry::make('pdf_url')
                                                ->label('URL PDF'),
                                        ]),
                                    Infolists\Components\TextEntry::make('objet')
                                        ->label('Objet')
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Informations système')
                                ->description('Métadonnées techniques')
                                ->icon('heroicon-o-cog')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                                                                    Infolists\Components\TextEntry::make('created_at')
                                            ->label('Créé le')
                                            ->dateTime('d/m/Y H:i'),
                                        Infolists\Components\TextEntry::make('updated_at')
                                            ->label('Modifié le')
                                            ->dateTime('d/m/Y H:i'),
                                        ]),
                                ]),
                        ]),
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
            DevisResource\RelationManagers\LignesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevis::route('/'),
            'create' => Pages\CreateDevis::route('/create'),
            'view' => Pages\ViewDevis::route('/{record}'),
            'edit' => Pages\EditDevis::route('/{record}/edit'),
            'transform' => Pages\TransformDevisToFacture::route('/{record}/transform'),
        ];
    }
}
