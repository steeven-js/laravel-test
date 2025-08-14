# 🐳 Configuration Gotenberg pour Supabase Edge Functions

## ✅ Installation Gotenberg terminée

**URL Gotenberg** : `http://69.62.71.69:3000`

## 🔧 Mise à jour des Secrets Supabase

### Option 1 : Dashboard Supabase (Recommandé)

1. **Accédez au dashboard** : https://supabase.com/dashboard/project/uemmyrtikobkczqmnpbi
2. **Navigation** : Settings → Edge Functions → Secrets
3. **Mettez à jour** :

```
GOTENBERG_URL=http://69.62.71.69:3000
GOTENBERG_API_KEY=your_api_key_here
```

### Option 2 : Script automatisé

1. **Récupérez votre SERVICE_ROLE_KEY** depuis le dashboard Supabase
2. **Modifiez le script** `update_supabase_secrets.sh` :
   ```bash
   SERVICE_ROLE_KEY="votre_clé_service_role_ici"
   ```
3. **Exécutez le script** :
   ```bash
   ./update_supabase_secrets.sh
   ```

## 🔍 Vérification

### Test Gotenberg local
```bash
curl -I http://69.62.71.69:3000/health
```

### Test Edge Function
```bash
curl -H "Authorization: Bearer YOUR_ANON_KEY" \
  https://uemmyrtikobkczqmnpbi.supabase.co/functions/v1/generate-devis-pdf/1
```

## 📝 Configuration Edge Function

Dans votre edge function `generate-devis-pdf`, utilisez :

```javascript
const GOTENBERG_URL = Deno.env.get('GOTENBERG_URL') || 'http://69.62.71.69:3000';
const GOTENBERG_API_KEY = Deno.env.get('GOTENBERG_API_KEY');

// Exemple d'utilisation
const response = await fetch(`${GOTENBERG_URL}/forms/chromium/convert/html`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${GOTENBERG_API_KEY}`,
    // autres headers...
  },
  body: formData
});
```

## 🚀 Déploiement

Après mise à jour des secrets :

1. **Redéployez l'edge function** :
   ```bash
   supabase functions deploy generate-devis-pdf
   ```

2. **Testez la génération PDF** depuis votre application

## 🔒 Sécurité

- **Gotenberg** : Accessible uniquement depuis votre VPS
- **Secrets** : Stockés de manière sécurisée dans Supabase
- **API Key** : Optionnel pour Gotenberg (peut être vide)

## 📊 Monitoring

- **Conteneur Gotenberg** : `docker ps | grep gotenberg`
- **Logs Gotenberg** : `docker logs gotenberg`
- **Health Check** : `curl http://69.62.71.69:3000/health`
