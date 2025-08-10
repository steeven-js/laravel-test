<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Enums\FactureEnvoiStatus;
use App\Enums\FactureStatus;
use Filament\Forms\Form;
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
                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut_envoi')
                    ->label("Statut d'envoi")
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => FactureEnvoiStatus::from($state)->getLabel())
                    ->color(fn (string $state): string => match ($state) {
                        'non_envoyee' => 'gray',
                        'envoyee' => 'success',
                        'echec_envoi' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')->options(FactureStatus::class),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle facture'),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Détail')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record): string => route('filament.admin.resources.factures.view', ['record' => $record]))
                    ->openUrlInNewTab(false),
                Tables\Actions\EditAction::make()->label('Éditer'),
                Tables\Actions\DeleteAction::make()->label('Supprimer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
