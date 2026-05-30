# 📦 Déploiement sur Render

Ce guide explique comment déployer le projet ChantierAI sur Render.

## Prérequis

1. **Compte Render** - Créer un compte sur https://render.com
2. **GitHub Repository** - Le projet doit être pushé sur GitHub
3. **Variables d'environnement** - Configurer le fichier `.env` localement

## Configuration avant le déploiement

### 1. Créer un fichier `.env` à la racine du projet

```bash
# Copier le fichier de configuration
cp config/config.example.php config/config.php
```

Puis adapter `config.php` avec vos paramètres:

```php
define('APP_URL', 'https://votre-app.onrender.com');
define('DB_HOST', 'mysql-db.render.internal');
define('DB_NAME', 'chantier_ai');
define('DB_USER', 'root');
define('DB_PASS', ''); // Sera défini dans Render
define('GEMINI_API_KEY', 'votre-clé-api');
```

### 2. Variables d'environnement obligatoires

Les variables suivantes doivent être définies dans Render:

- `MYSQL_ROOT_PASSWORD` - Mot de passe MySQL (généré automatiquement)
- `GEMINI_API_KEY` - Votre clé API Google Gemini
- `APP_URL` - URL de votre application

## Déploiement pas à pas

### Option 1: Via interface Render

1. Aller sur https://dashboard.render.com
2. Cliquer sur "New +" → "Web Service"
3. Connecter votre repository GitHub
4. Remplir les infos:
   - **Name:** `chantier-ai`
   - **Runtime:** Docker
   - **Build Command:** Auto-detecté
   - **Start Command:** Auto-detecté

5. Dans "Environment", ajouter vos variables:
   - `MYSQL_ROOT_PASSWORD` (généré)
   - `GEMINI_API_KEY` (votre clé)

6. Cliquer sur "Create Web Service"

### Option 2: Via render.yaml

Le projet inclut un fichier `render.yaml` qui configure automatiquement:
- ✅ Service Web PHP
- ✅ Base de données MySQL
- ✅ PhpMyAdmin pour la gestion BDD

**Pour utiliser render.yaml:**

1. Aller sur https://dashboard.render.com/new
2. Sélectionner "Blueprint"
3. Connecter votre repository GitHub
4. Render détecte automatiquement `render.yaml`
5. Configurer les variables d'environnement
6. Déployer

## Structure des services

### Web Service (PHP/Apache)
- **Port:** 3000 (automatique)
- **Langage:** PHP 8.2
- **Serveur:** Apache avec mod_rewrite

### Base de données MySQL
- **Accès interne:** `mysql-db.render.internal:3306`
- **Volume persistant:** 10GB pour les données
- **Créé automatiquement** avec la base `chantier_ai`

### PhpMyAdmin (optionnel)
- **Port:** 8081
- **Accès:** Pour gérer votre base de données

## Initialiser la base de données

Après le premier déploiement:

1. Accéder à PhpMyAdmin: `https://votre-app.onrender.com:8081`
2. Se connecter avec root + le mot de passe MySQL
3. Importer `database.sql`:
   - Sélectionner l'onglet "Import"
   - Charger `database.sql`
   - Cliquer "Import"

Ou exécuter via ligne de commande:

```bash
mysql -h mysql-db.render.internal -u root -p chantier_ai < database.sql
```

## Variables d'environnement à définir

| Variable | Description | Exemple |
|----------|-------------|---------|
| `MYSQL_ROOT_PASSWORD` | Mot de passe MySQL | `generé automatiquement` |
| `GEMINI_API_KEY` | Clé API Google Gemini | `AIzaSy...` |
| `APP_URL` | URL de l'application | `https://chantier-ai.onrender.com` |

## Fichiers Docker inclus

- **`Dockerfile`** - Image PHP 8.2 avec Apache
- **`apache-config.conf`** - Configuration Apache optimisée
- **`.dockerignore`** - Fichiers à exclure de l'image
- **`render.yaml`** - Configuration des services Render

## Configuration Apache

Le fichier `apache-config.conf` inclut:
- ✅ Mod_rewrite activé (pour les routes PHP)
- ✅ Headers et compression gzip
- ✅ Cache pour fichiers statiques
- ✅ Sécurité (masquer version Apache)
- ✅ Protection des fichiers sensibles

## Troubleshooting

### Erreur: "Connection refused MySQL"
- Vérifier que MySQL est complètement initialisé (peut prendre 5 min)
- Vérifier le nom du host: `mysql-db.render.internal`

### Erreur: "Permission denied uploads"
- Les permissions sont déjà configurées dans le Dockerfile
- Vérifier les logs: Dashboard → Logs

### Erreur: 502 Bad Gateway
- Vérifier les logs Apache: Dashboard → Logs
- S'assurer que le port 3000 est bien exposé

### Logs et débogage
```bash
# Depuis votre machine locale avec Render CLI
render logs chantier-ai --follow
```

## Mise à jour du site

1. Faire vos modifications localement
2. Pousser sur GitHub:
   ```bash
   git add -A
   git commit -m "Vos changements"
   git push origin main
   ```
3. Render redéploie automatiquement

## Backup de la base de données

Pour sauvegarder votre base de données:

```bash
# Exporter
mysqldump -h mysql-db.render.internal -u root -p chantier_ai > backup.sql

# Restaurer
mysql -h mysql-db.render.internal -u root -p chantier_ai < backup.sql
```

## Ressources utiles

- [Documentation Render](https://render.com/docs)
- [Render CLI](https://render.com/docs/cli)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [PHP Docker Hub](https://hub.docker.com/_/php)

---

**Questions?** Consultez les logs ou créez une issue sur GitHub.
