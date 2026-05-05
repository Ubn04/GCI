# Guide de dépannage - Chat IA

## 🔍 Vérifications à effectuer

### 1. Tester le système
Ouvrez dans votre navigateur : `http://localhost:8000/chantier-ai-php/test_chat_generation.php`

Ce script va vérifier :
- ✅ La clé API Gemini est configurée
- ✅ L'URL de l'API est correcte
- ✅ L'API Gemini répond correctement
- ✅ La base de données est accessible
- ✅ Les tables nécessaires existent

### 2. Vérifier la base de données

Exécutez le script SQL pour ajouter les nouvelles colonnes :

```bash
mysql -u root -p chantier_ai < add_project_fields.sql
```

Ou dans phpMyAdmin, exécutez :

```sql
ALTER TABLE projects ADD COLUMN IF NOT EXISTS maitre_ouvrage VARCHAR(255) DEFAULT NULL;
ALTER TABLE projects ADD COLUMN IF NOT EXISTS missions_controle TEXT DEFAULT NULL;
```

### 3. Vérifier les logs d'erreur

Regardez les logs PHP pour voir les erreurs :
- Windows : `C:\xampp\apache\logs\error.log`
- Linux/Mac : `/var/log/apache2/error.log`

### 4. Activer le mode debug

Dans `config/config.php`, vérifiez que ces lignes sont présentes :

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 🐛 Problèmes courants

### Problème 1 : "Rien ne se passe quand je clique sur envoyer"

**Solution :**
1. Ouvrez la console du navigateur (F12)
2. Regardez s'il y a des erreurs JavaScript
3. Vérifiez que vous avez saisi un message OU ajouté des fichiers OU des notes

### Problème 2 : "Erreur 500 après l'envoi"

**Solution :**
1. Vérifiez les logs Apache/PHP
2. Vérifiez que la clé API Gemini est valide
3. Testez avec `test_chat_generation.php`

### Problème 3 : "Le rapport n'est pas généré"

**Solution :**
1. Vérifiez que vous êtes connecté
2. Vérifiez que le projet existe
3. Vérifiez les logs pour voir les erreurs
4. Testez l'API Gemini avec le script de test

### Problème 4 : "Gemini API error"

**Solutions possibles :**
- La clé API est invalide ou expirée
- Quota API dépassé
- Problème de connexion internet
- Le système utilisera alors le générateur de base

## 📝 Comment utiliser le chat

1. **Connectez-vous** à votre compte
2. **Allez dans "Projets"** et cliquez sur un projet
3. **Cliquez sur "Ouvrir"** pour accéder au chat IA
4. **Saisissez un message** dans la zone de texte
   - Exemple : "Aujourd'hui nous avons coulé 50m³ de béton"
5. **OU ajoutez des fichiers** (photos, vidéos, documents)
6. **OU ajoutez des notes** dans le panneau de droite
7. **Sélectionnez le type de rapport** (Journalier, Mensuel, Annuel)
8. **Cliquez sur la flèche** pour envoyer

Le système va :
- Combiner votre message + notes + fichiers
- Envoyer à Gemini AI pour génération
- Créer un rapport professionnel
- Vous rediriger vers la liste des rapports

## 🔧 Commandes utiles

### Vérifier que PHP fonctionne
```bash
php -v
```

### Vérifier que MySQL fonctionne
```bash
mysql -u root -p -e "SELECT 1"
```

### Vérifier les extensions PHP nécessaires
```bash
php -m | grep -E "curl|pdo|json"
```

### Redémarrer Apache (si nécessaire)
```bash
# Windows (XAMPP)
xampp-control.exe

# Linux
sudo service apache2 restart

# Mac
sudo apachectl restart
```

## 📞 Support

Si le problème persiste après avoir suivi ce guide :

1. Exécutez `test_chat_generation.php`
2. Copiez tous les résultats
3. Vérifiez les logs d'erreur
4. Notez exactement ce qui se passe quand vous essayez d'utiliser le chat

## ✅ Checklist finale

- [ ] La clé API Gemini est configurée dans `config/config.php`
- [ ] Les colonnes `maitre_ouvrage` et `missions_controle` existent dans la table `projects`
- [ ] Le script `test_chat_generation.php` affiche tous les ✅
- [ ] Vous êtes connecté avec un compte utilisateur
- [ ] Vous avez au moins un projet créé
- [ ] Vous saisissez un message OU ajoutez des fichiers avant d'envoyer
- [ ] La console du navigateur (F12) ne montre pas d'erreurs JavaScript
