<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Enums\DevisEnvoiStatus;
use App\Enums\DevisStatus;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DevisRelationManager extends RelationManager
{
    protected static string $relationship = 'devis';

    protected static ?string $title = 'Devis';

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
                Tables\Columns\TextColumn::make('numero_devis')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_devis')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_ttc')
                    ->label('Montant TTC')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut_envoi')
                    ->label("Statut d'envoi")
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DevisEnvoiStatus::from($state)->getLabel())
                    ->color(fn (string $state): string => match ($state) {
                        'non_envoye' => 'gray',
                        'envoye' => 'success',
                        'echec_envoi' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')->options(DevisStatus::class),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouveau devis'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->infolist([
                            Infolists\Components\Section::make('Informations générales')
                                ->description('Détails du devis')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('numero_devis')
                                                ->label('Numéro'),
                                            Infolists\Components\TextEntry::make('date_devis')
                                                ->label('Date')
                                                ->date('d/m/Y'),
                                            Infolists\Components\TextEntry::make('date_validite')
                                                ->label('Date de validité')
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
                            Infolists\Components\Section::make('Statuts')
                                ->description('État du devis')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('statut')
                                                ->label('Statut')
                                                ->formatStateUsing(fn (string $state): string => DevisStatus::from($state)->getLabel()),
                                            Infolists\Components\TextEntry::make('statut_envoi')
                                                ->label("Statut d'envoi")
                                                ->formatStateUsing(fn (string $state): string => DevisEnvoiStatus::from($state)->getLabel()),
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
                                            Infolists\Components\TextEntry::make('date_acceptation')
                                                ->label('Date d\'acceptation')
                                                ->date('d/m/Y'),
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
