# Guide de résolution des problèmes de génération de rapports

## Problèmes identifiés et solutions

### 1. **Clé API OpenAI invalide ou manquante**

**Problème :** La clé API dans `config/config.php` est invalide ou vide.

**Solution :**
1. Obtenez une clé API valide sur https://platform.openai.com/api-keys
2. Modifiez le fichier `config/config.php` :
```php
define('OPENAI_API_KEY', 'votre-vraie-cle-api-ici');
```

**Alternative :** Si vous n'avez pas de clé OpenAI, laissez vide et le système utilisera un rapport de base.

### 2. **Données de chantier manquantes**

**Problème :** Aucune donnée de site n'est disponible pour générer le rapport.

**Solution :**
1. Allez dans "Projets" → Ouvrir un projet
2. Ajoutez des données de terrain (texte, images, etc.)
3. Ou ajoutez des notes lors de la génération du rapport

### 3. **Erreurs de base de données**

**Problème :** Tables manquantes ou connexion échouée.

**Solution :**
1. Vérifiez que la base de données `chantier_ai` existe
2. Importez le fichier `database.sql` :
```sql
mysql -u root -p chantier_ai < database.sql
```
3. Vérifiez les paramètres dans `config/config.php`

### 4. **Permissions de fichiers**

**Problème :** Impossible d'uploader des fichiers.

**Solution :**
```bash
chmod 755 uploads/
chmod 755 uploads/profiles/
```

## Scripts de diagnostic

### 1. Diagnostic complet
Accédez à : `http://votre-site/debug_reports.php`

### 2. Test de génération
Accédez à : `http://votre-site/test_report_generation.php`

## Solutions rapides

### Solution 1: Utiliser le contrôleur corrigé
Remplacez le contenu de `app/controllers/ReportController.php` par celui de `app/controllers/ReportControllerFixed.php`

### Solution 2: Configuration minimale
Si vous n'avez pas d'API, modifiez `config/config.php` :
```php
define('OPENAI_API_KEY', ''); // Vide
define('GEMINI_API_KEY', ''); // Vide
```
Le système générera des rapports de base.

### Solution 3: Données de test
Ajoutez des données de test dans un projet :
1. Créez un projet
2. Ouvrez-le
3. Ajoutez du texte comme "Travaux de terrassement effectués"
4. Essayez de générer un rapport

## Vérifications étape par étape

1. **Base de données** ✓
   - Tables créées
   - Connexion fonctionnelle
   - Utilisateur connecté

2. **Configuration** ✓
   - Clés API définies (ou vides)
   - Constantes correctes

3. **Données** ✓
   - Projet créé
   - Données de site ajoutées
   - Ou notes fournies

4. **Permissions** ✓
   - Répertoires accessibles
   - PHP peut écrire

5. **API** ✓
   - Clé valide
   - Ou fallback activé

## Messages d'erreur courants

### "Ajoutez des données de chantier ou des notes"
- Ajoutez du contenu dans le projet ou des notes lors de la génération

### "Clé OpenAI non définie"
- Configurez une clé API ou laissez vide pour le mode fallback

### "Erreur de connexion à la base de données"
- Vérifiez les paramètres DB dans `config/config.php`

### "Accès refusé à ce projet"
- Connectez-vous avec le bon utilisateur propriétaire du projet

## Contact et support

Si les problèmes persistent :
1. Vérifiez les logs PHP
2. Utilisez les scripts de diagnostic
3. Consultez ce guide
4. Vérifiez la configuration étape par étape