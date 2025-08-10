<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Enums\DevisEnvoiStatus;
use App\Enums\DevisStatus;
use Filament\Forms;
use Filament\Forms\Form;
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
                        'transforme' => 'info',
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
                Tables\Actions\Action::make('detail')
                    ->label('Détail')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record): string => route('filament.admin.resources.devis.view', ['record' => $record]))
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


