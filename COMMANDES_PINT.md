# 🎨 Commandes Pint - Formatage du Code

## 📋 Vue d'ensemble

**Pint** est l'outil de formatage de code officiel de Laravel, basé sur PHP CS Fixer. Il assure la conformité aux standards PSR-12 et aux conventions Laravel.

## 🚀 Installation

Pint est inclus par défaut dans Laravel 10+. Si vous devez l'installer manuellement :

```bash
composer require laravel/pint --dev
```

## 📝 Commandes principales

### 1. **Formatage complet du projet**
```bash
./vendor/bin/pint
```
- Formate tous les fichiers PHP du projet
- Applique les règles PSR-12 et Laravel
- Affiche un rapport détaillé des corrections

### 2. **Formatage en mode dry-run**
```bash
./vendor/bin/pint --test
```
- Vérifie le code sans le modifier
- Affiche les problèmes détectés
- Utile pour vérifier la conformité avant commit

### 3. **Formatage d'un fichier spécifique**
```bash
./vendor/bin/pint app/Models/User.php
```
- Formate uniquement le fichier spécifié
- Utile pour corriger un fichier particulier

### 4. **Formatage d'un répertoire**
```bash
./vendor/bin/pint app/Http/Controllers/
```
- Formate tous les fichiers d'un répertoire
- Pratique pour les modules spécifiques

## ⚙️ Configuration

### Fichier `.pint.json`
```json
{
    "preset": "laravel",
    "rules": {
        "@PSR12": true,
        "array_syntax": {"syntax": "short"},
        "ordered_imports": {"sort_algorithm": "alpha"},
        "no_unused_imports": true,
        "not_operator_with_successor_space": true,
        "trailing_comma_in_multiline": true,
        "phpdoc_scalar": true,
        "unary_operator_spaces": true,
        "binary_operator_spaces": true,
        "blank_line_before_statement": {
            "statements": ["break", "continue", "declare", "return", "throw", "try"]
        },
        "phpdoc_single_line_var_spacing": true,
        "phpdoc_var_without_name": true,
        "method_argument_space": {
            "on_multiline": "ensure_fully_multiline",
            "keep_multiple_spaces_after_comma": true
        },
        "single_trait_insert_per_statement": true
    }
}
```

### Règles personnalisées courantes
- **`@PSR12`** : Conformité aux standards PSR-12
- **`array_syntax`** : Utilisation de la syntaxe courte `[]`
- **`ordered_imports`** : Tri alphabétique des imports
- **`no_unused_imports`** : Suppression des imports inutilisés
- **`unary_operator_spaces`** : Espacement des opérateurs unaires

## 🔧 Intégration dans le workflow

### 1. **Avant commit (Git hooks)**
```bash
# .git/hooks/pre-commit
#!/bin/sh
./vendor/bin/pint --test || (echo "Code non conforme aux standards. Exécutez ./vendor/bin/pint" && exit 1)
```

### 2. **Dans composer.json**
```json
{
    "scripts": {
        "format": "./vendor/bin/pint",
        "format:check": "./vendor/bin/pint --test"
    }
}
```

### 3. **Commandes disponibles**
```bash
composer format      # Formate le code
composer format:check # Vérifie la conformité
```

## 📊 Exemple de sortie

```bash
./vendor/bin/pint

  ...................................................✓✓✓.......✓✓✓..........✓...........✓✓...✓✓..........✓
  ✓...✓✓......✓.✓✓✓✓................

  ──────────────────────────────────────────────────────────────────────────────────────────────── Laravel

    FIXED   ............................................................. 138 files, 20 style issues fixed

  ✓ app/Models/User.php single_quote, unary_operator_spaces
  ✓ app/Http/Controllers/UserController.php class_attributes_separation
  ✓ app/Filament/Resources/UserResource.php no_whitespace_in_blank_line
```

## 🎯 Règles de formatage appliquées

### **Espacement et indentation**
- Indentation de 4 espaces
- Pas de tabulations
- Lignes vides entre les méthodes de classe
- Espacement autour des opérateurs

### **Guillemets et chaînes**
- Guillemets simples pour les chaînes simples
- Guillemets doubles pour les chaînes avec variables
- Concaténation avec espaces autour de l'opérateur `.`

### **Imports et namespaces**
- Tri alphabétique des imports
- Suppression des imports inutilisés
- Ligne vide entre les groupes d'imports

### **Classes et méthodes**
- Séparation des attributs de classe
- Espacement des paramètres de méthode
- Parenthèses après `new` et `clone`

## 🚨 Problèmes courants et solutions

### 1. **Imports inutilisés**
```php
// ❌ Avant
use App\Models\User;
use App\Models\Post; // Non utilisé

// ✅ Après
use App\Models\User;
```

### 2. **Espacement des opérateurs**
```php
// ❌ Avant
$result=$a+$b;

// ✅ Après
$result = $a + $b;
```

### 3. **Guillemets**
```php
// ❌ Avant
$message = "Hello World";

// ✅ Après
$message = 'Hello World';
```

### 4. **Lignes vides**
```php
// ❌ Avant
class User
{
    public function getName()
    {
        return $this->name;
    }
    public function getEmail()
    {
        return $this->email;
    }
}

// ✅ Après
class User
{
    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
```

## 🔄 Workflow recommandé

### **Développement quotidien**
1. Écrire le code
2. Exécuter `./vendor/bin/pint` avant commit
3. Vérifier les modifications
4. Commiter le code formaté

### **Intégration continue**
1. Exécuter `./vendor/bin/pint --test` dans CI/CD
2. Échouer le build si le code n'est pas conforme
3. Formater automatiquement avant déploiement

### **Équipe de développement**
1. Configurer les éditeurs pour formater automatiquement
2. Utiliser les Git hooks pour vérification
3. Documenter les conventions d'équipe

## 📚 Ressources supplémentaires

- [Documentation officielle Laravel Pint](https://laravel.com/docs/pint)
- [Standards PSR-12](https://www.php-fig.org/psr/psr-12/)
- [PHP CS Fixer](https://cs.symfony.com/)
- [Conventions Laravel](https://laravel.com/docs/contributions#coding-style)

## 🎉 Avantages

- **Cohérence** : Code uniforme dans tout le projet
- **Lisibilité** : Formatage standard et professionnel
- **Maintenance** : Code plus facile à maintenir
- **Collaboration** : Standards partagés dans l'équipe
- **Qualité** : Respect des bonnes pratiques PHP/Laravel

---

*Dernière mise à jour : 9 janvier 2025*
