# Import des Clients de Production

Ce document explique comment importer vos clients de production depuis l'ancien système vers la nouvelle table `clients` du Dashboard Madinia.

## 📋 Vue d'ensemble

Le système d'import comprend :
- **ClientProductionSeeder** : Seeder Laravel pour la migration des données
- **ImportClientsProduction** : Commande Artisan avec options avancées
- **Gestion automatique des entreprises** : Création d'entreprises par défaut si nécessaire
- **Détection des doublons** : Évite la création de clients en double
- **Migration des métadonnées** : Préservation des informations importantes dans les notes

## 🚀 Utilisation rapide

### 1. Préparation du fichier CSV

Placez votre fichier `clients_rows.csv` dans le répertoire racine du projet Laravel.

### 2. Import simple

```bash
# Import standard avec confirmation
php artisan clients:import-production

# Import forcé sans confirmation
php artisan clients:import-production --force
```

### 3. Validation et analyse

```bash
# Valider le fichier CSV sans import
php artisan clients:import-production --validate-only

# Mode dry-run pour voir ce qui serait importé
php artisan clients:import-production --dry-run
```

## 🔧 Options de la commande

| Option | Description | Exemple |
|--------|-------------|---------|
| `--csv` | Chemin vers le fichier CSV | `--csv=mon_fichier.csv` |
| `--force` | Forcer l'import sans confirmation | `--force` |
| `--dry-run` | Afficher ce qui serait importé | `--dry-run` |
| `--validate-only` | Valider uniquement le fichier | `--validate-only` |

## 📊 Structure des données

### Colonnes du CSV source
Le seeder gère automatiquement la migration des colonnes suivantes :

| Colonne CSV | Champ Client | Notes |
|-------------|--------------|-------|
| `nom` | `nom` | Obligatoire |
| `prenom` | `prenom` | Nullable |
| `email` | `email` | Unique si renseigné |
| `telephone` | `telephone` | Format international |
| `adresse` | `adresse` | Texte libre |
| `ville` | `ville` | Nom de la ville |
| `code_postal` | `code_postal` | Code postal |
| `pays` | `pays` | Défaut: France |
| `actif` | `actif` | Booléen (true/false) |
| `notes` | `notes` | Notes existantes |
| `entreprise_id` | `entreprise_id` | Relation avec entreprise |
| `created_at` | `created_at` | Date de création |
| `updated_at` | `updated_at` | Date de modification |

### Colonnes supplémentaires migrées dans les notes
- `type_client` → Type de client
- `assujetti_tva` → Statut TVA
- `numero_tva` → Numéro de TVA
- `siren_client` → SIREN
- `siret_client` → SIRET
- `accepte_e_facture` → Acceptation e-facture
- `preference_format` → Format préféré
- `deleted_at` → Date de suppression (si applicable)

## 🏢 Gestion des entreprises

### Création automatique
Si un client n'a pas d'`entreprise_id` valide, le seeder crée automatiquement une entreprise basée sur :
- La ville du client
- Le pays du client
- Les coordonnées du client

### Nommage des entreprises
Format : `Entreprise {Ville}` ou `Entreprise {Pays}`

Exemples :
- `Entreprise Fort-de-France`
- `Entreprise Martinique`
- `Entreprise France`

## 🔍 Détection des doublons

Le seeder vérifie l'existence des clients par :
1. **Email** (si renseigné)
2. **Nom + Prénom** (combinaison exacte)

### Comportement
- **Client existant** → Ignoré (pas de doublon créé)
- **Client nouveau** → Créé avec toutes ses données
- **Client supprimé** → Créé avec note de suppression

## 📈 Statistiques de migration

Après l'import, le seeder affiche :
- ✅ Nombre de clients créés
- ⏭️ Nombre de clients ignorés (déjà existants)
- 🏢 Nombre d'entreprises créées
- ❌ Nombre d'erreurs rencontrées

## 🛡️ Sécurité et validation

### Transactions
- Toutes les opérations sont dans une transaction
- En cas d'erreur, toutes les modifications sont annulées
- Logs détaillés des erreurs

### Validation des données
- Vérification de l'existence du fichier CSV
- Validation du format des données
- Gestion des valeurs manquantes
- Parsing intelligent des booléens et dates

## 🔧 Personnalisation

### Modifier le mapping des données
Éditez la méthode `mapClientData()` dans `ClientProductionSeeder.php` :

```php
private function mapClientData(array $row, ?Entreprise $entreprise): array
{
    return [
        'nom' => $row['nom'] ?: 'Nom par défaut',
        'prenom' => $row['prenom'] ?: null,
        // ... autres champs
    ];
}
```

### Ajouter de nouvelles colonnes
Éditez la méthode `formatNotes()` pour inclure de nouvelles informations :

```php
if (!empty($row['nouvelle_colonne'])) {
    $notes[] = "Nouvelle info: {$row['nouvelle_colonne']}";
}
```

## 🚨 Résolution des problèmes

### Erreur "Fichier CSV non trouvé"
```bash
# Vérifiez que le fichier est dans le répertoire racine
ls -la clients_rows.csv

# Ou spécifiez un chemin personnalisé
php artisan clients:import-production --csv=chemin/vers/fichier.csv
```

### Erreur de validation des données
```bash
# Validez d'abord le fichier
php artisan clients:import-production --validate-only

# Vérifiez le format CSV
head -5 clients_rows.csv
```

### Erreur de base de données
```bash
# Vérifiez la connexion à la base
php artisan tinker
DB::connection()->getPdo();

# Vérifiez les migrations
php artisan migrate:status
```

## 📝 Exemples d'utilisation

### Import de test
```bash
# Valider le fichier
php artisan clients:import-production --validate-only

# Dry-run pour voir les données
php artisan clients:import-production --dry-run

# Import réel
php artisan clients:import-production
```

### Import en production
```bash
# Import forcé sans confirmation
php artisan clients:import-production --force

# Import avec fichier personnalisé
php artisan clients:import-production --csv=clients_prod_2025.csv --force
```

### Vérification post-import
```bash
# Compter les clients
php artisan tinker
App\Models\Client::count();

# Vérifier les entreprises créées
App\Models\Entreprise::where('notes', 'like', '%automatiquement%')->count();
```

## 🔄 Relancer l'import

Si vous devez relancer l'import :

```bash
# Nettoyer les données existantes (attention !)
php artisan tinker
App\Models\Client::truncate();
App\Models\Entreprise::where('notes', 'like', '%automatiquement%')->delete();

# Relancer l'import
php artisan clients:import-production --force
```

## 📞 Support

Pour toute question ou problème :
1. Vérifiez les logs Laravel : `storage/logs/laravel.log`
2. Utilisez le mode `--validate-only` pour diagnostiquer
3. Consultez la documentation des modèles Client et Entreprise
