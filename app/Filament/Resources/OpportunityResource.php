<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Models\Opportunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OpportunityResource extends Resource
{
    use \App\Filament\Resources\Traits\HasHistoriqueResource;

    protected static ?string $modelLabel = 'Opportunité';

    protected static ?string $pluralModelLabel = 'Opportunités';

    protected static ?string $navigationLabel = 'Opportunités';

    protected static ?string $pluralNavigationLabel = 'Opportunités';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Opportunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string
    {
        return 'Opportunité';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Opportunités';
    }

    public static function getNavigationLabel(): string
    {
        return 'Opportunités';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Opportunité')
                    ->description('Nom, description et détails de l\'opportunité')
                    ->icon('heroicon-o-light-bulb')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Suivi commercial')
                    ->description('Étape, probabilité et montant estimé')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('etape')
                                    ->label('Étape')
                                    ->required()
                                    ->options([
                                        'prospection' => 'Prospection',
                                        'qualification' => 'Qualification',
                                        'proposition' => 'Proposition',
                                        'negociation' => 'Négociation',
                                        'fermeture' => 'Fermeture',
                                        'gagnee' => 'Gagnée',
                                        'perdue' => 'Perdue',
                                    ])
                                    ->default('prospection'),
                                Forms\Components\TextInput::make('probabilite')
                                    ->label('Probabilité')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(1)
                                    ->suffix('%')
                                    ->default(0),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('montant')
                                    ->label('Montant estimé')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->prefix('€'),
                                Forms\Components\Toggle::make('active')
                                    ->required(),
                            ]),
                    ]),
                Forms\Components\Section::make('Dates et échéances')
                    ->description('Planning et clôture de l\'opportunité')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_cloture_prevue'),
                                Forms\Components\DatePicker::make('date_cloture_reelle'),
                            ]),
                    ]),
                Forms\Components\Section::make('Responsabilités')
                    ->description('Client et responsable de l\'opportunité')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('client_id')
                                    ->label('Client')
                                    ->relationship('client', 'nom')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('user_id')
                                    ->label('Responsable')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ]),
                Forms\Components\Section::make('Notes et commentaires')
                    ->description('Informations complémentaires')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('etape')
                    ->searchable(),
                Tables\Columns\TextColumn::make('probabilite')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_cloture_prevue')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_cloture_reelle')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('etape')
                    ->options([
                        'prospection' => 'Prospection',
                        'qualification' => 'Qualification',
                        'proposition' => 'Proposition',
                        'negociation' => 'Négociation',
                        'fermeture' => 'Fermeture',
                        'gagnee' => 'Gagnée',
                        'perdue' => 'Perdue',
                    ]),
                Tables\Filters\TernaryFilter::make('active')->label('Active')->boolean(),
                Tables\Filters\SelectFilter::make('client_id')
                    ->relationship('client', 'nom')
                    ->label('Client'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Responsable'),
            ])
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->emptyStateHeading('Aucune opportunité')
            ->emptyStateDescription('Ajoutez votre première opportunité pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nouvelle opportunité'),
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
            //
        ];
    }
    protected static function getDefaultRelations(): array
    {
        return [
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'edit' => Pages\EditOpportunity::route('/{record}/edit'),
        ];
    }
}
