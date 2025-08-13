# Seeders pour Opportunités, Tickets et Tâches

Ce document décrit les seeders créés pour générer des données de test pour les opportunités, tickets et tâches associés aux clients.

## 📋 Vue d'ensemble

Trois nouveaux seeders ont été créés pour enrichir la base de données avec des données réalistes :

- **OpportunitySeeder** : Crée des opportunités commerciales
- **TicketSeeder** : Crée des tickets de support
- **TodoSeeder** : Crée des tâches de suivi

## 🎯 OpportunitySeeder

### Description
Génère 10 opportunités commerciales avec des données variées et réalistes.

### Données créées
- **10 opportunités** avec différents états :
  - Prospection (25% de probabilité)
  - Qualification (40-50% de probabilité)
  - Proposition (70-75% de probabilité)
  - Négociation (60-65% de probabilité)
  - Fermeture (90% de probabilité)
  - Gagnée (100% de probabilité)
  - Perdue (0% de probabilité)

### Montants
- Montants variés de 6 000€ à 35 000€
- Dates d'échéance réparties sur plusieurs mois
- Notes détaillées pour chaque opportunité

### Commandes disponibles
```bash
# Via le seeder principal
php artisan db:seed --class=OpportunitySeeder

# Via la commande dédiée
php artisan seed:opportunities
```

## 🎫 TicketSeeder

### Description
Génère 10 tickets de support avec différents types et priorités.

### Types de tickets créés
- **Incidents** : Problèmes techniques (connexion, performance, emails)
- **Demandes** : Nouvelles fonctionnalités, modifications
- **Bugs** : Corrections de problèmes
- **Questions** : Support utilisateur

### Priorités
- **Critique** : Problèmes bloquants
- **Haute** : Problèmes importants
- **Normale** : Demandes standard
- **Faible** : Questions simples

### Statuts
- **Ouvert** : Nouveaux tickets
- **En cours** : En traitement
- **Résolu** : Problème résolu
- **Fermé** : Ticket clos

### Données incluses
- Temps estimé et temps passé
- Progression en pourcentage
- Notes internes et solutions
- Visibilité client configurée

### Commandes disponibles
```bash
# Via le seeder principal
php artisan db:seed --class=TicketSeeder

# Via la commande dédiée
php artisan seed:tickets
```

## ✅ TodoSeeder

### Description
Génère 15 tâches de suivi avec différents niveaux de priorité et d'achèvement.

### Types de tâches
- **Suivi client** : Appels, réunions
- **Préparation** : Propositions, présentations
- **Administratif** : Documentation, rapports
- **Technique** : Tests, optimisations
- **Formation** : Sessions de formation

### Priorités
- **Critique** : Tâches urgentes
- **Haute** : Tâches importantes
- **Normale** : Tâches standard
- **Faible** : Tâches non urgentes

### État d'achèvement
- **Terminées** : 5 tâches
- **En cours** : 10 tâches

### Données incluses
- Ordre de priorité
- Dates d'échéance
- Descriptions détaillées

### Commandes disponibles
```bash
# Via le seeder principal
php artisan db:seed --class=TodoSeeder

# Via la commande dédiée
php artisan seed:todos
```

## 🔗 Intégration avec les clients

Tous les seeders :
- Récupèrent automatiquement les clients existants
- Associent aléatoirement les données aux clients
- Utilisent les utilisateurs existants comme responsables
- Vérifient la présence de données préalables

## 📊 Statistiques des données créées

| Type | Nombre | Description |
|------|--------|-------------|
| Opportunités | 10 | Opportunités commerciales variées |
| Tickets | 10 | Tickets de support avec différents statuts |
| Tâches | 15 | Tâches de suivi avec priorités |

## 🚀 Utilisation

### Exécution individuelle
```bash
# Opportunités uniquement
php artisan seed:opportunities

# Tickets uniquement
php artisan seed:tickets

# Tâches uniquement
php artisan seed:todos
```

### Exécution via DatabaseSeeder
```bash
# Tous les seeders (y compris les nouveaux)
php artisan db:seed
```

### Exécution sélective
```bash
# Seulement les nouveaux seeders
php artisan db:seed --class=OpportunitySeeder
php artisan db:seed --class=TicketSeeder
php artisan db:seed --class=TodoSeeder
```

## 🔧 Personnalisation

### Modification des données
Pour modifier les données générées :
1. Éditer le fichier seeder correspondant
2. Modifier le tableau de données
3. Relancer le seeder

### Ajout de nouvelles données
1. Ajouter de nouvelles entrées dans le tableau de données
2. Respecter la structure des champs
3. Tester avec la commande dédiée

## 📝 Notes importantes

- Les seeders utilisent `random()` pour associer les données aux clients
- Les dates sont calculées dynamiquement avec `now()`
- Les seeders vérifient la présence de clients et utilisateurs
- Les données sont réalistes et variées pour un bon test

## 🎯 Avantages

- **Données réalistes** : Simulation d'un environnement de production
- **Couvre tous les cas** : Différents statuts, priorités et types
- **Facile à utiliser** : Commandes dédiées disponibles
- **Intégré** : Fonctionne avec le système existant
- **Maintenable** : Code clair et documenté
