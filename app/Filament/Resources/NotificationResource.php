<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Notifications\DatabaseNotification as NotificationModel;

class NotificationResource extends Resource
{
    protected static ?string $model = NotificationModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string
    {
        return 'Notification';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Notifications';
    }

    public static function getNavigationLabel(): string
    {
        return 'Notifications';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('notifiable_type')
                    ->label('Notifiable type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('notifiable_id')
                    ->label('Notifiable ID')
                    ->numeric()
                    ->required(),
                Forms\Components\KeyValue::make('data')
                    ->label('Données (JSON)')
                    ->keyLabel('Clé')
                    ->valueLabel('Valeur')
                    ->addButtonLabel('Ajouter une donnée')
                    ->reorderable()
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('read_at')
                    ->label('Lu le'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->copyable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('notifiable_type')
                    ->label('Notifiable type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('notifiable_id')
                    ->label('Notifiable ID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Lu')
                    ->boolean()
                    ->trueIcon('heroicon-m-check')
                    ->falseIcon('heroicon-m-x-mark')
                    ->getStateUsing(fn ($record) => ! is_null($record->read_at))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('read')
                    ->label('Lu')
                    ->nullable()
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('read_at'),
                        false: fn ($query) => $query->whereNull('read_at'),
                        blank: fn ($query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('type')
                    ->options(fn () => NotificationModel::query()
                        ->select('type')
                        ->distinct()
                        ->pluck('type', 'type')
                        ->toArray()),
            ])
            ->searchPlaceholder('Rechercher...')
            ->emptyStateIcon('heroicon-o-bell-alert')
            ->emptyStateHeading('Aucune notification')
            ->emptyStateDescription("Il n'y a pas encore de notifications à afficher.")
            ->emptyStateActions([
                // pas d'action de création pour les notifications système
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markAsRead')
                    ->label('Marquer comme lu')
                    ->visible(fn ($record) => is_null($record->read_at))
                    ->action(fn (NotificationModel $record) => $record->forceFill(['read_at' => now()])->save()),
                Tables\Actions\Action::make('markAsUnread')
                    ->label('Marquer comme non lu')
                    ->visible(fn ($record) => ! is_null($record->read_at))
                    ->action(fn (NotificationModel $record) => $record->forceFill(['read_at' => null])->save()),
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
