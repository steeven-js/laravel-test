<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Enums\FactureEnvoiStatus;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FacturesRelationManager extends RelationManager
{
    protected static string $relationship = 'factures';

    protected static ?string $title = 'Factures';

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
                Tables\Columns\TextColumn::make('numero_facture')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_facture')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_ttc')
                    ->label('Montant TTC')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut_envoi')
                    ->label("Statut d'envoi")
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => FactureEnvoiStatus::from($state)->getLabel())
                    ->color(fn (string $state): string => match ($state) {
                        'non_envoye' => 'gray',
                        'envoye' => 'success',
                        'echec_envoi' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle facture'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Détails de la facture')
                                ->icon('heroicon-o-document')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('numero_facture')
                                                ->label('Numéro'),
                                            Infolists\Components\TextEntry::make('date_facture')
                                                ->label('Date')
                                                ->date('d/m/Y'),
                                            Infolists\Components\TextEntry::make('date_echeance')
                                                ->label('Date d\'échéance')
                                                ->date('d/m/Y'),
                                            Infolists\Components\TextEntry::make('objet')
                                                ->label('Objet'),
                                        ]),
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Section::make('Montants')
                                ->description('Calculs financiers')
                                ->icon('heroicon-o-currency-euro')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('montant_ht')
                                                ->label('Montant HT')
                                                ->money('EUR'),
                                            Infolists\Components\TextEntry::make('montant_tva')
                                                ->label('Montant TVA')
                                                ->money('EUR'),
                                            Infolists\Components\TextEntry::make('montant_ttc')
                                                ->label('Montant TTC')
                                                ->money('EUR'),
                                            Infolists\Components\TextEntry::make('taux_tva')
                                                ->label('Taux TVA')
                                                ->suffix('%'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Statut')
                                ->description('État de la facture')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('statut_envoi')
                                                ->label("Statut d'envoi")
                                                ->formatStateUsing(fn (string $state): string => FactureEnvoiStatus::from($state)->getLabel()),
                                            Infolists\Components\TextEntry::make('date_paiement')
                                                ->label('Date de paiement')
                                                ->date('d/m/Y'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Responsabilités')
                                ->description('Personnes impliquées')
                                ->icon('heroicon-o-user-group')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('administrateur.name')
                                                ->label('Administrateur'),
                                            Infolists\Components\TextEntry::make('devis.numero_devis')
                                                ->label('Devis associé'),
                                        ]),
                                ]),
                            Infolists\Components\Section::make('Notes et conditions')
                                ->description('Informations complémentaires')
                                ->icon('heroicon-o-pencil-square')
                                ->schema([
                                    Infolists\Components\TextEntry::make('conditions')
                                        ->label('Conditions')
                                        ->columnSpanFull(),
                                    Infolists\Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                    Tables\Actions\EditAction::make()->label('Modifier'),
                    Tables\Actions\DeleteAction::make()->label('Supprimer'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
