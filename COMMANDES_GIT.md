# 🚀 Commandes Git - Guide Rapide

## 📦 Première Fois (Setup Initial)

```bash
# 1. Initialiser Git
git init

# 2. Configurer votre identité
git config --global user.name "Votre Nom"
git config --global user.email "votre.email@exemple.com"

# 3. Ajouter tous les fichiers
git add .

# 4. Premier commit
git commit -m "🎉 Initial commit"

# 5. Créer le repository sur GitHub (via le site web)
# Puis lier le repository local :
git remote add origin https://github.com/VOTRE-USERNAME/chantierai.git

# 6. Pousser sur GitHub
git branch -M main
git push -u origin main
```

## 🔄 Workflow Quotidien

```bash
# 1. Voir les modifications
git status

# 2. Ajouter les fichiers modifiés
git add .
# OU ajouter un fichier spécifique
git add fichier.php

# 3. Créer un commit
git commit -m "✨ Description de la modification"

# 4. Pousser sur GitHub
git push
```

## 📝 Messages de Commit (Emojis)

```bash
✨ Nouvelle fonctionnalité
git commit -m "✨ Ajout de l'export PDF"

🐛 Correction de bug
git commit -m "🐛 Fix: Correction du modal"

💄 Design/UI
git commit -m "💄 Amélioration du design"

📝 Documentation
git commit -m "📝 Mise à jour du README"

♻️ Refactoring
git commit -m "♻️ Refactoring du code"

⚡ Performance
git commit -m "⚡ Optimisation des requêtes"

🔒 Sécurité
git commit -m "🔒 Amélioration de la sécurité"

🚀 Déploiement
git commit -m "🚀 Préparation pour production"

🔧 Configuration
git commit -m "🔧 Mise à jour de la config"

🗃️ Base de données
git commit -m "🗃️ Ajout de nouvelles tables"
```

## 🌿 Branches

```bash
# Créer une nouvelle branche
git checkout -b feature/nom-fonctionnalite

# Voir toutes les branches
git branch -a

# Changer de branche
git checkout main

# Fusionner une branche
git merge feature/nom-fonctionnalite

# Supprimer une branche locale
git branch -d feature/nom-fonctionnalite

# Supprimer une branche distante
git push origin --delete feature/nom-fonctionnalite
```

## 📥 Récupérer les Changements

```bash
# Récupérer depuis GitHub
git pull

# Récupérer sans fusionner
git fetch

# Voir les différences avec GitHub
git diff origin/main
```

## ↩️ Annuler des Modifications

```bash
# Annuler les modifications d'un fichier (non commité)
git checkout -- fichier.php

# Annuler tous les changements non commités
git reset --hard

# Annuler le dernier commit (garder les modifications)
git reset --soft HEAD~1

# Annuler le dernier commit (supprimer les modifications)
git reset --hard HEAD~1

# Modifier le dernier commit
git commit --amend -m "Nouveau message"
```

## 🔍 Inspection

```bash
# Voir l'historique
git log

# Historique compact
git log --oneline

# Historique avec graphique
git log --graph --oneline --all

# Voir les différences
git diff

# Voir les différences d'un fichier
git diff fichier.php

# Voir qui a modifié chaque ligne
git blame fichier.php
```

## 🗑️ Supprimer des Fichiers

```bash
# Supprimer un fichier de Git ET du disque
git rm fichier.php
git commit -m "🗑️ Suppression de fichier.php"

# Supprimer un fichier de Git MAIS garder sur le disque
git rm --cached fichier.php
git commit -m "🗑️ Retrait de fichier.php du versioning"
```

## 🔧 Configuration

```bash
# Voir la configuration
git config --list

# Configurer l'éditeur
git config --global core.editor "code --wait"

# Configurer les couleurs
git config --global color.ui auto

# Voir la configuration d'un paramètre
git config user.name
```

## 🆘 Urgences

### J'ai commité un fichier sensible !

```bash
# 1. Supprimer du dernier commit (AVANT push)
git reset HEAD~1
git add .gitignore
git commit -m "🔒 Fix: Ajout de .gitignore"

# 2. Si déjà poussé (DANGER!)
git rm --cached config/config.php
git commit -m "🔒 Remove sensitive file"
git push
# PUIS changer TOUS vos mots de passe !
```

### J'ai fait un mauvais commit

```bash
# Annuler le dernier commit (garder les modifications)
git reset --soft HEAD~1

# Refaire le commit correctement
git add .
git commit -m "✨ Bon message"
```

### Conflit lors du merge

```bash
# 1. Voir les fichiers en conflit
git status

# 2. Éditer les fichiers pour résoudre les conflits
# Chercher les marqueurs <<<<<<< ======= >>>>>>>

# 3. Marquer comme résolu
git add fichier-resolu.php

# 4. Finaliser le merge
git commit -m "🔀 Merge: Résolution des conflits"
```

## 📊 Statistiques

```bash
# Nombre de commits par auteur
git shortlog -sn

# Statistiques du repository
git log --stat

# Voir les contributeurs
git log --format='%aN' | sort -u
```

## 🔗 Remote (GitHub)

```bash
# Voir les remotes
git remote -v

# Ajouter un remote
git remote add origin https://github.com/USER/repo.git

# Changer l'URL du remote
git remote set-url origin https://github.com/USER/nouveau-repo.git

# Supprimer un remote
git remote remove origin
```

## 🏷️ Tags (Versions)

```bash
# Créer un tag
git tag v1.0.0

# Créer un tag avec message
git tag -a v1.0.0 -m "Version 1.0.0"

# Voir tous les tags
git tag

# Pousser un tag
git push origin v1.0.0

# Pousser tous les tags
git push --tags

# Supprimer un tag local
git tag -d v1.0.0

# Supprimer un tag distant
git push origin --delete v1.0.0
```

## 🧹 Nettoyage

```bash
# Nettoyer les fichiers non suivis
git clean -n  # Voir ce qui sera supprimé
git clean -f  # Supprimer

# Nettoyer les branches fusionnées
git branch --merged | grep -v "\*" | xargs -n 1 git branch -d
```

## 📦 Stash (Mettre de côté)

```bash
# Mettre de côté les modifications
git stash

# Voir les stash
git stash list

# Récupérer le dernier stash
git stash pop

# Récupérer un stash spécifique
git stash apply stash@{0}

# Supprimer un stash
git stash drop stash@{0}

# Supprimer tous les stash
git stash clear
```

## 🔍 Recherche

```bash
# Rechercher dans les commits
git log --grep="mot-clé"

# Rechercher dans le code
git grep "fonction"

# Rechercher qui a introduit un mot
git log -S "mot-clé"
```

## 💡 Astuces

```bash
# Créer un alias
git config --global alias.st status
git config --global alias.co checkout
git config --global alias.br branch
git config --global alias.ci commit

# Utiliser les alias
git st  # au lieu de git status
git co main  # au lieu de git checkout main

# Voir les fichiers ignorés
git status --ignored

# Compter les lignes de code
git ls-files | xargs wc -l
```

## 📚 Ressources

- [Documentation Git](https://git-scm.com/doc)
- [GitHub Guides](https://guides.github.com/)
- [Git Cheat Sheet](https://education.github.com/git-cheat-sheet-education.pdf)

---

**Bon codage ! 💻**
