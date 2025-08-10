# Actions Standardisées Filament

Ce document explique comment utiliser le système d'actions standardisées pour avoir à la fois un aperçu modal et un bouton détail dans vos ressources Filament.

## Vue d'ensemble

Le système permet d'avoir deux types d'actions pour chaque enregistrement :
1. **Aperçu** : Affichage modal rapide des informations principales
2. **Détail** : Redirection vers la page de visualisation complète

## Utilisation de base

### 1. Importer le trait

```php
use App\Filament\Resources\Traits\HasStandardActions;
```

### 2. Ajouter le trait à votre ressource

```php
class VotreResource extends Resource
{
    use HasStandardActions;
    
    // ... reste de votre code
}
```

### 3. Configurer les actions dans la table

```php
public static function table(Table $table): Table
{
    return $table
        // ... autres configurations
        ->actions([
            Tables\Actions\ViewAction::make()
                ->label('Aperçu')
                ->modal()
                ->url(null)
                ->modalCancelActionLabel('Fermer')
                ->infolist([
                    // Configuration de votre infolist pour l'aperçu
                ]),
            Tables\Actions\Action::make('detail')
                ->label('Détail')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->url(fn (VotreModel $record): string => static::getUrl('view', ['record' => $record]))
                ->openUrlInNewTab(false),
            Tables\Actions\EditAction::make(),
        ]);
}
```

## Utilisation avancée avec le trait

### Méthode 1 : Actions standardisées complètes

```php
public static function table(Table $table): Table
{
    return static::configureStandardActions($table, static::class);
}
```

### Méthode 2 : Actions standardisées avec actions personnalisées

```php
public static function table(Table $table): Table
{
    $customActions = [
        Tables\Actions\Action::make('export')
            ->label('Exporter')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success'),
    ];
    
    return static::configureStandardActionsWithCustom($table, static::class, $customActions);
}
```

### Méthode 3 : Actions standardisées avec actions conditionnelles

```php
public static function table(Table $table): Table
{
    $conditionalActions = [
        Tables\Actions\Action::make('archive')
            ->label('Archiver')
            ->icon('heroicon-o-archive-box')
            ->color('warning')
            ->visible(fn (VotreModel $record): bool => $record->can_be_archived),
    ];
    
    return static::configureStandardActionsWithConditions($table, static::class, $conditionalActions);
}
```

## Configuration des pages

Assurez-vous d'avoir la page `view` configurée dans votre ressource :

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListVotreResource::route('/'),
        'create' => Pages\CreateVotreResource::route('/create'),
        'view' => Pages\ViewVotreResource::route('/{record}'),
        'edit' => Pages\EditVotreResource::route('/{record}/edit'),
    ];
}
```

## Personnalisation des icônes et couleurs

Vous pouvez personnaliser l'apparence des boutons :

```php
Tables\Actions\Action::make('detail')
    ->label('Détail')
    ->icon('heroicon-o-arrow-top-right-on-square') // Icône personnalisée
    ->color('info') // Couleur personnalisée
    ->size('sm') // Taille personnalisée
    ->badge() // Style badge
    ->url(fn (VotreModel $record): string => static::getUrl('view', ['record' => $record]));
```

## Exemples d'icônes disponibles

- `heroicon-o-eye` : Œil (pour l'aperçu)
- `heroicon-o-arrow-top-right-on-square` : Flèche vers la page (pour le détail)
- `heroicon-o-pencil` : Crayon (pour l'édition)
- `heroicon-o-trash` : Poubelle (pour la suppression)
- `heroicon-o-archive-box` : Archive
- `heroicon-o-arrow-down-tray` : Téléchargement

## Bonnes pratiques

1. **Cohérence** : Utilisez les mêmes icônes et couleurs dans toutes vos ressources
2. **Accessibilité** : Assurez-vous que les labels sont clairs et descriptifs
3. **Performance** : L'aperçu modal est plus rapide que la navigation vers une nouvelle page
4. **UX** : L'aperçu modal est idéal pour une consultation rapide, le détail pour une analyse complète

## Résolution des problèmes

### Le bouton détail ne fonctionne pas
- Vérifiez que la page `view` est bien configurée dans `getPages()`
- Assurez-vous que la route est accessible
- Vérifiez les permissions d'accès

### L'aperçu modal ne s'affiche pas
- Vérifiez que `->modal()` est bien appelé
- Assurez-vous que l'infolist est correctement configuré
- Vérifiez qu'il n'y a pas de conflit avec d'autres configurations

### Erreur de type dans l'URL
- Assurez-vous que le type du modèle est correct dans la closure
- Vérifiez que le modèle est bien importé

