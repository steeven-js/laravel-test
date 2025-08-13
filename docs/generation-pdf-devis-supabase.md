## Génération de PDF Devis via Supabase Edge (HTML Tailwind + Gotenberg)

### Objectif
- Générer des PDFs de devis avec un rendu identique à l’aperçu Filament/Laravel (maquette Tailwind).
- Lire les données depuis Postgres sur le schéma personnalisé `laravel` et stocker les fichiers dans Supabase Storage.

### Sommaire
- Prérequis
- Vue d’ensemble
- Configuration des secrets
- Déploiement des fonctions
- Endpoints et paramètres
- Exemples d’utilisation (cURL)
- Détails d’implémentation
- Maquette (HTML Tailwind ou fond PDF)
- Sécurité et visibilité des PDFs
- Dépannage (FAQ)

---

### Prérequis
- Compte Supabase et CLI installé (`supabase --version`).
- Projet Supabase avec Postgres accessible (variables DB déjà fournies par le projet).
- Deno/Edge runtime géré par Supabase (pas d’installation locale nécessaire pour exécuter les fonctions).
- Un service Gotenberg (Chromium) accessible via une URL publique.
  - Option rapide: Docker local + tunnel `ngrok`.

---

### Vue d’ensemble
- Fonctions Edge:
  - `schema-laravel-reader`: lecture générique de tables du schéma `laravel`.
  - `generate-devis-pdf`: génération/stockage d’un PDF de devis.
- Accès DB: driver Postgres Deno (connexion `SUPABASE_DB_URL`) pour by-passer la limitation d’API sur les schémas.
- Rendu PDF:
  - Par défaut: construction d’un HTML Tailwind équivalent à `PdfGenerator.jsx` puis conversion via Gotenberg (Chromium) → rendu fidèle.
  - Secours: `pdf-lib` si Gotenberg n’est pas disponible (rendu vectoriel simple).
- Stockage: bucket `devis-pdfs` (public ou via URL signée), mise à jour des colonnes `pdf_file` et `pdf_url` dans `laravel.devis`.

Chemins utiles:
- `supabase/functions/schema-laravel-reader/index.ts`
- `supabase/functions/generate-devis-pdf/index.ts`
- Références UI:
  - `resources/views/pdf/preview-modal.blade.php`
  - `resources/js/Pages/PdfGenerator.jsx`

---

### Configuration des secrets
Assurez-vous que les secrets suivants sont définis dans le projet Supabase:

Obligatoires (déjà présents sur le projet):
```
SUPABASE_URL
SUPABASE_SERVICE_ROLE_KEY
SUPABASE_DB_URL
```

Optionnels/recommandés pour rendu identique (HTML → PDF):
```
GOTENBERG_URL        # URL publique du service Gotenberg (ex: https://xxxx.ngrok-free.app)
GOTENBERG_API_KEY    # si vous protégez Gotenberg derrière une clé (header X-Api-Key)
```

Commande:
```bash
supabase secrets set GOTENBERG_URL=https://<PUBLIC_GOTENBERG_URL> --project-ref <PROJECT_REF>
supabase secrets set GOTENBERG_API_KEY=<OPTIONAL_KEY>             --project-ref <PROJECT_REF>
```

---

### Déploiement des fonctions
```bash
# Déploiement
supabase functions deploy schema-laravel-reader  --project-ref <PROJECT_REF> | cat
supabase functions deploy generate-devis-pdf     --project-ref <PROJECT_REF> | cat
```

Après modification de secrets, redéployez la fonction `generate-devis-pdf` pour prise en compte.

---

### Endpoints et paramètres

1) Lecture générique (schéma `laravel`):
- `GET /functions/v1/schema-laravel-reader/{table}`
- Query:
  - `limit` (1–1000, défaut 100)
  - `order_by` (défaut `id`)
  - `order_dir` = `asc|desc` (défaut `asc`)

2) Génération PDF devis:
- `POST /functions/v1/generate-devis-pdf/{id}`
- Query:
  - `public=true|false` (défaut: false → URL signée)
  - `signed_ttl` (TTL URL signée en secondes, défaut ~1 an)
  - `bucket` (défaut: `devis-pdfs`)
  - `path` (défaut: `devis/{id}.pdf`)
  - `template_bucket` + `template_path` (option: fond PDF depuis Storage)

Réponse JSON (succès):
```
{
  ok: true,
  id: <number>,
  storagePath: "devis/<id>.pdf",
  publicUrl: <string|null>,
  signedUrl: <string|null>
}
```

---

### Exemples d’utilisation (cURL)

Lecture d’un devis (dernier ID):
```bash
curl -s -X GET "https://<PROJECT>.supabase.co/functions/v1/schema-laravel-reader/devis?limit=1&order_by=id&order_dir=DESC" \
  -H "Authorization: Bearer <ANON_KEY>"
```

Génération PDF (public):
```bash
curl -s -X POST "https://<PROJECT>.supabase.co/functions/v1/generate-devis-pdf/10?public=true" \
  -H "Authorization: Bearer <ANON_KEY>"
```

Génération PDF (fond PDF depuis Storage):
```bash
curl -s -X POST "https://<PROJECT>.supabase.co/functions/v1/generate-devis-pdf/10?public=true&template_bucket=devis-pdfs&template_path=templates/devis.pdf" \
  -H "Authorization: Bearer <ANON_KEY>"
```

Génération PDF (privé, URL signée d’1 jour):
```bash
curl -s -X POST "https://<PROJECT>.supabase.co/functions/v1/generate-devis-pdf/10?signed_ttl=86400" \
  -H "Authorization: Bearer <ANON_KEY>"
```

---

### Détails d’implémentation

Lecture DB (schéma `laravel`):
- Connexion via `SUPABASE_DB_URL`.
- Requêtes utilisées:
  - `devis` (id)
  - `madinia` (meta entreprise)
  - `clients` (+ `entreprises`)
  - `lignes_devis` (+ `services`) avec ordre logique

Rendu PDF (devis):
- Mode principal: HTML Tailwind (CDN) → Gotenberg (Chromium) → PDF
- Mode secours: `pdf-lib` (en-tête, blocs émetteur/client, tableau, totaux, footer)

Stockage:
- Bucket par défaut: `devis-pdfs`
- Création idempotente et réglage `public`/`privé`
- Mise à jour en base: colonnes `pdf_file`, `pdf_url` dans `laravel.devis`

Sérialisation:
- Conversion des `bigint` → `string` pour éviter les erreurs JSON.

---

### Maquette

Option 1 — HTML Tailwind (recommandé)
- Reproduit la structure de `resources/js/Pages/PdfGenerator.jsx`.
- S’assure du chargement de Tailwind via `waitForExpression` côté Gotenberg.
- Possibilité d’embarquer une CSS Tailwind compilée personnalisée au lieu du CDN.

Option 2 — Fond PDF
- Fournir un PDF modèle (base64 dans `EMBEDDED_TEMPLATE_B64` ou depuis Storage via `template_bucket`/`template_path`).
- Positionnement des champs par-dessus; pratique pour un rendu “identique imprimé”.

---

### Sécurité & visibilité des PDFs
- Public: `?public=true` → URL publique immédiate depuis le bucket.
- Privé: par défaut → URL signée (TTL configurable via `signed_ttl`).
- Recommandation: ajouter des contrôles JWT/claims si besoin (accès API restreint).

---

### Dépannage (FAQ)
- Erreur schéma `public`/`graphql_public`: résolue en usage direct du driver Postgres et schéma `laravel`.
- Erreur BigInt JSON: sérialisation → chaîne.
- BOOT_ERROR: fonctions manquantes ou variables d’env; redéployer après configuration.
- Gotenberg inaccessible: l’URL doit être publique (ex: tunnel `ngrok`). Mettre à jour le secret `GOTENBERG_URL` et redéployer la fonction.
- Différences visuelles: utiliser Gotenberg + Tailwind (ou embarquer votre CSS compilée) pour un rendu fidèle.

---

### Commandes utiles
```bash
# Déployer
supabase functions deploy schema-laravel-reader  --project-ref <PROJECT_REF> | cat
supabase functions deploy generate-devis-pdf     --project-ref <PROJECT_REF> | cat

# Secrets
supabase secrets list | cat
supabase secrets set GOTENBERG_URL=https://<PUBLIC_GOTENBERG_URL> --project-ref <PROJECT_REF>

# Gotenberg local
docker run --rm -p 3000:3000 gotenberg/gotenberg:8

# Tunnel (exemple ngrok)
ngrok http 3000
```

---

### Notes
- Les fonctions utilisent des CORS permissifs pour simplifier les tests.
- Les dates et montants sont formatés en style FR dans le rendu.
- Pour un “pixel-perfect” absolu, privilégier soit la CSS Tailwind compilée du projet, soit un fond PDF fourni par le design existant.


