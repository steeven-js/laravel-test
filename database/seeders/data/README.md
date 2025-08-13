# Fichiers de données pour les seeders

Ce dossier contient les fichiers CSV utilisés par les seeders pour importer des données dans la base de données.

## Structure des fichiers

### 📁 `secteurs_activite.csv`
**Utilisé par :** `SecteurActiviteSeeder`

**Colonnes :**
- `id` - Identifiant unique
- `code` - Code NAF/APE (ex: 62.01Z)
- `libelle` - Libellé du secteur d'activité
- `division` - Division NAF (2 premiers chiffres)
- `section` - Section NAF (lettre)
- `actif` - Secteur actif (true/false)
- `created_at` - Date de création
- `updated_at` - Date de modification

### 📁 `services.csv`
**Utilisé par :** `ServiceSeeder`

**Colonnes :**
- `id` - Identifiant unique
- `nom` - Nom du service
- `code` - Code du service
- `description` - Description du service
- `prix_ht` - Prix HT
- `qte_defaut` - Quantité par défaut
- `unite` - Unité de mesure
- `actif` - Service actif (true/false)
- `created_at` - Date de création
- `updated_at` - Date de modification

### 📁 `madinia.csv`
**Utilisé par :** `MadiniaSeeder`

**Colonnes :**
- `id` - Identifiant unique
- `name` - Nom de l'entreprise
- `nom_commercial` - Nom commercial
- `siret` - Numéro SIRET
- `siren` - Numéro SIREN
- `secteur_activite` - Secteur d'activité
- `adresse` - Adresse complète
- `ville` - Ville
- `code_postal` - Code postal
- `pays` - Pays
- `telephone` - Téléphone
- `email` - Email
- `site_web` - Site web
- `actif` - Entreprise active (true/false)
- `notes` - Notes
- `reseaux_sociaux` - Réseaux sociaux (JSON)
- `created_at` - Date de création
- `updated_at` - Date de modification

### 📁 `email_templates.csv`
**Utilisé par :** `EmailTemplateSeeder`

**Colonnes :**
- `id` - Identifiant unique
- `name` - Nom du template
- `category` - Catégorie (envoi_initial, rappel, relance, confirmation)
- `sub_category` - Sous-catégorie spécifique
- `subject` - Sujet de l'email
- `body` - Corps de l'email (avec variables)
- `is_default` - Template par défaut (true/false)
- `is_active` - Template actif (true/false)
- `variables` - Variables disponibles (JSON)
- `description` - Description du template
- `created_at` - Date de création
- `updated_at` - Date de modification

## Utilisation

### Import automatique
Les seeders utilisent automatiquement ces fichiers lors de l'exécution :

```bash
# Exécuter tous les seeders
php artisan db:seed

# Exécuter un seeder spécifique
php artisan db:seed --class=SecteurActiviteSeeder
```

### Fallback
Si un fichier CSV n'existe pas, certains seeders (comme `EmailTemplateSeeder`) créent des données par défaut.

## Format des fichiers

### Encodage
- **UTF-8** pour supporter les caractères spéciaux français

### Séparateurs
- **Virgule** (,) comme séparateur de champs
- **Guillemets doubles** (") pour les champs contenant des virgules

### Types de données
- **Booléens** : `true` ou `false`
- **JSON** : Chaînes JSON valides pour les champs complexes
- **Dates** : Format ISO 8601 (`YYYY-MM-DD HH:MM:SS`)

## Maintenance

### Ajout de nouveaux fichiers
1. Placer le fichier CSV dans ce dossier
2. Mettre à jour le seeder correspondant
3. Documenter la structure dans ce README

### Mise à jour des données
1. Modifier le fichier CSV
2. Réexécuter le seeder concerné
3. Vérifier l'intégrité des données

### Sauvegarde
Il est recommandé de sauvegarder ces fichiers avant toute modification importante.

## Commandes utiles

```bash
# Vérifier la structure d'un fichier CSV
head -5 database/seeders/data/secteurs_activite.csv

# Compter les lignes d'un fichier CSV
wc -l database/seeders/data/services.csv

# Vérifier l'encodage d'un fichier
file -I database/seeders/data/email_templates.csv
```
