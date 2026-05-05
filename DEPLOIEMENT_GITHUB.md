# 🚀 Guide de Déploiement sur GitHub

## 📋 Prérequis

1. **Compte GitHub** : Créer un compte sur [github.com](https://github.com)
2. **Git installé** : Vérifier avec `git --version`
3. **Projet prêt** : Tous les fichiers sont en place

## 🔐 Sécurité - IMPORTANT !

### ⚠️ Fichiers à NE JAMAIS commiter
- ❌ `config/config.php` (contient les mots de passe)
- ❌ `vendor/` (dépendances Composer)
- ❌ `uploads/*` (fichiers uploadés)
- ❌ Fichiers de backup (*_backup.php, *_old.php)

### ✅ Fichiers à commiter
- ✅ `config/config.example.php` (exemple sans données sensibles)
- ✅ `.gitignore` (liste des fichiers à ignorer)
- ✅ `README.md` (documentation)
- ✅ Tout le code source

## 📝 Étapes de Déploiement

### 1. Initialiser Git (si pas déjà fait)

```bash
# Vérifier si Git est initialisé
git status

# Si erreur "not a git repository", initialiser :
git init
```

### 2. Configurer Git

```bash
# Configurer votre nom et email
git config --global user.name "Votre Nom"
git config --global user.email "votre.email@exemple.com"
```

### 3. Vérifier les fichiers à commiter

```bash
# Voir les fichiers qui seront ajoutés
git status

# Vérifier que config.php n'apparaît PAS dans la liste
# Si config.php apparaît, vérifier votre .gitignore
```

### 4. Ajouter les fichiers

```bash
# Ajouter tous les fichiers (sauf ceux dans .gitignore)
git add .

# Vérifier ce qui a été ajouté
git status
```

### 5. Créer le premier commit

```bash
git commit -m "🎉 Initial commit - ChantierAI v1.0"
```

### 6. Créer un repository sur GitHub

1. Aller sur [github.com](https://github.com)
2. Cliquer sur le bouton **"+"** en haut à droite
3. Sélectionner **"New repository"**
4. Remplir :
   - **Repository name** : `chantierai` (ou autre nom)
   - **Description** : "Système de gestion de rapports de chantier avec IA"
   - **Visibilité** : 
     - ✅ **Public** (visible par tous)
     - ⚠️ **Private** (visible seulement par vous)
   - ❌ Ne PAS cocher "Initialize with README" (on a déjà un README)
5. Cliquer sur **"Create repository"**

### 7. Lier le repository local à GitHub

```bash
# Remplacer VOTRE-USERNAME par votre nom d'utilisateur GitHub
git remote add origin https://github.com/VOTRE-USERNAME/chantierai.git

# Vérifier la connexion
git remote -v
```

### 8. Pousser le code sur GitHub

```bash
# Première fois (créer la branche main)
git branch -M main
git push -u origin main

# Les fois suivantes (après modifications)
git push
```

## 🎉 C'est fait !

Votre projet est maintenant sur GitHub ! 🚀

Accéder à : `https://github.com/VOTRE-USERNAME/chantierai`

## 🔄 Workflow Quotidien

### Après avoir fait des modifications

```bash
# 1. Voir les fichiers modifiés
git status

# 2. Ajouter les fichiers modifiés
git add .

# 3. Créer un commit avec un message descriptif
git commit -m "✨ Ajout de la fonctionnalité X"

# 4. Pousser sur GitHub
git push
```

### Messages de commit recommandés

```bash
# Nouvelle fonctionnalité
git commit -m "✨ Ajout de l'export PDF"

# Correction de bug
git commit -m "🐛 Fix: Correction du modal rapport"

# Amélioration
git commit -m "💄 Amélioration du design du dashboard"

# Documentation
git commit -m "📝 Mise à jour du README"

# Refactoring
git commit -m "♻️ Refactoring du code de génération"

# Performance
git commit -m "⚡ Optimisation des requêtes SQL"
```

## 🌿 Branches (Optionnel)

### Créer une branche pour une nouvelle fonctionnalité

```bash
# Créer et basculer sur une nouvelle branche
git checkout -b feature/nouvelle-fonctionnalite

# Faire vos modifications...
git add .
git commit -m "✨ Nouvelle fonctionnalité"

# Pousser la branche
git push -u origin feature/nouvelle-fonctionnalite

# Retourner sur main
git checkout main

# Fusionner la branche
git merge feature/nouvelle-fonctionnalite
```

## 🔒 Sécurité - Vérifications

### Avant chaque push, vérifier :

```bash
# 1. Vérifier qu'aucun fichier sensible n'est ajouté
git status

# 2. Voir le contenu exact qui sera poussé
git diff --cached

# 3. Si config.php apparaît, l'enlever :
git reset HEAD config/config.php
```

### Si vous avez accidentellement commité config.php

```bash
# Supprimer du dernier commit (avant push)
git reset HEAD~1
git add .gitignore
git commit -m "🔒 Fix: Ajout de .gitignore"

# Si déjà poussé sur GitHub (DANGER!)
# 1. Supprimer le fichier de Git
git rm --cached config/config.php
git commit -m "🔒 Remove sensitive config file"
git push

# 2. Changer TOUS vos mots de passe immédiatement !
```

## 📊 Commandes Utiles

```bash
# Voir l'historique des commits
git log --oneline

# Voir les différences
git diff

# Annuler les modifications locales
git checkout -- fichier.php

# Voir les branches
git branch -a

# Mettre à jour depuis GitHub
git pull

# Cloner le projet ailleurs
git clone https://github.com/VOTRE-USERNAME/chantierai.git
```

## 🎨 Personnaliser le README

N'oubliez pas de modifier dans `README.md` :
- ✏️ Votre nom d'utilisateur GitHub
- ✏️ Votre email
- ✏️ Les liens vers votre repository
- ✏️ Ajouter des captures d'écran

## 📸 Ajouter des Captures d'Écran

```bash
# 1. Créer un dossier screenshots
mkdir screenshots

# 2. Ajouter vos images
# screenshots/dashboard.png
# screenshots/rapport.png
# screenshots/export.png

# 3. Référencer dans README.md
![Dashboard](screenshots/dashboard.png)
```

## 🌐 Rendre le Projet Public

Si votre repository est privé et vous voulez le rendre public :

1. Aller sur GitHub → Votre repository
2. Cliquer sur **Settings**
3. Descendre jusqu'à **Danger Zone**
4. Cliquer sur **Change visibility**
5. Sélectionner **Make public**
6. Confirmer

## 🎯 Checklist Finale

Avant de partager votre projet :

- ✅ `.gitignore` est en place
- ✅ `config.php` n'est PAS dans Git
- ✅ `config.example.php` est présent
- ✅ `README.md` est complet et personnalisé
- ✅ Pas de clés API dans le code
- ✅ Pas de mots de passe en dur
- ✅ `database.sql` est à jour
- ✅ Documentation claire
- ✅ License ajoutée (MIT recommandée)

## 🆘 Problèmes Courants

### "Permission denied (publickey)"
```bash
# Utiliser HTTPS au lieu de SSH
git remote set-url origin https://github.com/VOTRE-USERNAME/chantierai.git
```

### "Updates were rejected"
```bash
# Récupérer les changements distants d'abord
git pull origin main --rebase
git push
```

### "Large files detected"
```bash
# Supprimer les gros fichiers du commit
git rm --cached fichier-trop-gros.zip
git commit --amend
```

## 📞 Support

- 📖 [Documentation Git](https://git-scm.com/doc)
- 📖 [GitHub Guides](https://guides.github.com/)
- 💬 [GitHub Community](https://github.community/)

---

**Bon déploiement ! 🚀**
