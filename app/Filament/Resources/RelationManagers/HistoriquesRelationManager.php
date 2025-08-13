<?php

declare(strict_types=1);

namespace App\Filament\Resources\RelationManagers;

use App\Models\Historique;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriquesRelationManager extends RelationManager
{
    protected static string $relationship = 'historiques';

    protected static ?string $title = 'Historique des actions';

    protected static ?string $navigationLabel = 'Historique des actions';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'creation' => 'success',
                        'modification' => 'primary',
                        'suppression' => 'danger',
                        'changement_statut' => 'warning',
                        'envoi_email' => 'info',
                        'paiement_stripe' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'creation' => 'heroicon-o-document-plus',
                        'modification' => 'heroicon-o-pencil',
                        'suppression' => 'heroicon-o-trash',
                        'changement_statut' => 'heroicon-o-arrow-path',
                        'envoi_email' => 'heroicon-o-envelope',
                        default => 'heroicon-o-clock',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('user_nom')
                    ->label('Par')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Type d\'action')
                    ->options([
                        'creation' => 'Création',
                        'modification' => 'Modification',
                        'suppression' => 'Suppression',
                        'changement_statut' => 'Changement de statut',
                        'envoi_email' => 'Envoi d\'email',
                        'paiement_stripe' => 'Paiement Stripe',
                    ])
                    ->multiple()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Rechercher dans l\'historique...')
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateHeading('Aucun historique')
            ->emptyStateDescription("Il n'y a pas encore d'événements d'historique.")
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Voir les détails')
                        ->icon('heroicon-o-eye')
                        ->modal()
                        ->modalCancelActionLabel('Fermer')
                        ->modalHeading('Détails de l\'action')
                        ->modalDescription('Informations complètes sur cette action')
                        ->modalWidth('4xl')
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Détails de l\'action')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Infolists\Components\Grid::make(3)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('action')
                                                ->label('Action')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'creation' => 'success',
                                                    'modification' => 'primary',
                                                    'suppression' => 'danger',
                                                    'changement_statut' => 'warning',
                                                    'envoi_email' => 'info',
                                                    default => 'gray',
                                                }),
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Date d\'action')
                                                ->dateTime('d/m/Y H:i'),
                                            Infolists\Components\TextEntry::make('user_nom')
                                                ->label('Utilisateur'),
                                        ]),
                                    Infolists\Components\TextEntry::make('titre')
                                        ->label('Titre')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold'),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),

                            Infolists\Components\Section::make('Données JSON')
                                ->description('Avant, après et informations supplémentaires')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    // Données avant
                                    Infolists\Components\TextEntry::make('donnees_avant')
                                        ->label('Avant:')
                                        ->default(function (Historique $record): string {
                                            if (! $record->donnees_avant || empty($record->donnees_avant)) {
                                                return 'Aucune donnée';
                                            }

                                            return json_encode($record->donnees_avant, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                        })
                                        ->fontFamily('mono')
                                        ->size('sm')
                                        ->color('gray')
                                        ->columnSpanFull()
                                        ->visible(fn (Historique $record): bool => ! empty($record->donnees_avant)),

                                    // Données après
                                    Infolists\Components\TextEntry::make('donnees_apres')
                                        ->label('Après:')
                                        ->default(function (Historique $record): string {
                                            if (! $record->donnees_apres || empty($record->donnees_apres)) {
                                                return 'Aucune donnée';
                                            }

                                            return json_encode($record->donnees_apres, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                        })
                                        ->fontFamily('mono')
                                        ->size('sm')
                                        ->color('gray')
                                        ->columnSpanFull()
                                        ->visible(fn (Historique $record): bool => ! empty($record->donnees_apres)),

                                    // Données supplémentaires
                                    Infolists\Components\TextEntry::make('donnees_supplementaires')
                                        ->label('Informations supplémentaires:')
                                        ->default(function (Historique $record): string {
                                            if (! $record->donnees_supplementaires || empty($record->donnees_supplementaires)) {
                                                return 'Aucune donnée';
                                            }

                                            return json_encode($record->donnees_supplementaires, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                        })
                                        ->fontFamily('mono')
                                        ->size('sm')
                                        ->color('gray')
                                        ->columnSpanFull()
                                        ->visible(fn (Historique $record): bool => ! empty($record->donnees_supplementaires)),
                                ]),

                            Infolists\Components\Section::make('Contexte technique')
                                ->description('Informations complémentaires')
                                ->icon('heroicon-o-cog')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('ip_address')
                                                ->label('Adresse IP'),
                                            Infolists\Components\TextEntry::make('user_agent')
                                                ->label('User Agent'),
                                        ]),
                                ]),
                        ]),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer la sélection'),
                ]),
            ]);
    }
}
