<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Notifications\DatabaseNotification as NotificationModel;

class NotificationResource extends Resource
{
    protected static ?string $modelLabel = 'Notification';

    protected static ?string $pluralModelLabel = 'Notifications';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $pluralNavigationLabel = 'Notifications';

    protected static bool $hasTitleCaseModelLabel = false;

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
            ->recordUrl(null)
            ->recordAction('view')
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
                Tables\Actions\ViewAction::make()
                    ->name('view')
                    ->label('Aperçu')
                    ->icon('heroicon-o-eye')
                    ->modal()
                    ->url(null)
                    ->modalCancelActionLabel('Fermer')
                    ->modalHeading('Aperçu de la notification')
                    ->modalDescription('Détails complets de la notification sélectionnée')
                    ->modalWidth('4xl')
                    ->infolist([
                        Infolists\Components\Section::make('Informations de la notification')
                            ->description('Type, destinataire et données de la notification')
                            ->icon('heroicon-o-bell-alert')
                            ->schema([
                                Infolists\Components\TextEntry::make('type')
                                    ->label('Type')
                                    ->badge()
                                    ->color('primary'),
                                Infolists\Components\TextEntry::make('notifiable_type')
                                    ->label('Type de destinataire')
                                    ->badge()
                                    ->color('info'),
                                Infolists\Components\TextEntry::make('notifiable_id')
                                    ->label('ID du destinataire')
                                    ->badge()
                                    ->color('warning'),
                                Infolists\Components\IconEntry::make('read_at')
                                    ->label('Statut de lecture')
                                    ->boolean()
                                    ->trueIcon('heroicon-m-check-circle')
                                    ->falseIcon('heroicon-m-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->getStateUsing(fn ($record) => ! is_null($record->read_at)),
                            ]),
                        Infolists\Components\Section::make('Données de la notification')
                            ->description('Contenu et métadonnées de la notification')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Infolists\Components\TextEntry::make('data')
                                    ->label('Données (JSON)')
                                    ->markdown()
                                    ->columnSpanFull()
                                    ->getStateUsing(fn ($record) => json_encode($record->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
                            ]),
                        Infolists\Components\Section::make('Informations système')
                            ->description('Métadonnées techniques')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('created_at')
                                            ->label('Créée le')
                                            ->dateTime()
                                            ->icon('heroicon-o-calendar'),
                                        Infolists\Components\TextEntry::make('read_at')
                                            ->label('Lue le')
                                            ->dateTime()
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('Non lue'),
                                    ]),
                            ]),
                    ]),
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
