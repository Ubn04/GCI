# 🔧 Mise à Jour Base de Données - Nouveau Format Rapport

## ✅ Modifications Effectuées

Le système de génération de rapports a été modifié pour suivre le nouveau format professionnel avec :
- Informations administratives complètes
- Tableaux formatés (équipements, personnel, matériaux)
- Structure professionnelle
- Annexes photographiques

## 📊 Champs à Ajouter à la Base de Données

La table `projects` a besoin de 2 nouveaux champs :
1. **client** - Maître d'ouvrage
2. **control_mission** - Mission contrôle

## 🚀 Méthode 1 : Via phpMyAdmin (Recommandé)

### Étape 1 : Ouvrir phpMyAdmin
1. Ouvrir le navigateur
2. Aller sur : `http://localhost/phpmyadmin`
3. Se connecter (user: root, pas de mot de passe par défaut)

### Étape 2 : Sélectionner la Base de Données
1. Cliquer sur `chantier_ai` dans la liste à gauche

### Étape 3 : Exécuter le SQL
1. Cliquer sur l'onglet **SQL** en haut
2. Copier-coller ce code :

```sql
-- Ajouter le champ client (Maître d'ouvrage)
ALTER TABLE projects 
ADD COLUMN client VARCHAR(200) DEFAULT 'Non spécifié' AFTER description;

-- Ajouter le champ control_mission (Mission contrôle)
ALTER TABLE projects 
ADD COLUMN control_mission VARCHAR(200) DEFAULT 'Non spécifié' AFTER client;

-- Mettre à jour les projets existants
UPDATE projects 
SET client = 'Non spécifié', control_mission = 'Non spécifié' 
WHERE client IS NULL OR control_mission IS NULL;
```

3. Cliquer sur **Exécuter**

### Étape 4 : Vérifier
1. Cliquer sur l'onglet **Structure**
2. Vérifier que les champs `client` et `control_mission` apparaissent

## 🚀 Méthode 2 : Via Ligne de Commande

```bash
# Ouvrir MySQL
mysql -u root

# Sélectionner la base
USE chantier_ai;

# Ajouter les champs
ALTER TABLE projects 
ADD COLUMN client VARCHAR(200) DEFAULT 'Non spécifié' AFTER description;

ALTER TABLE projects 
ADD COLUMN control_mission VARCHAR(200) DEFAULT 'Non spécifié' AFTER client;

# Vérifier
DESCRIBE projects;

# Quitter
EXIT;
```

## 📝 Modifications du Code

### Fichier Modifié
- ✅ **`app/controllers/ReportController.php`** - Méthode `buildOpenAIMessages()`

### Nouveau Format de Rapport

Le rapport généré suivra maintenant cette structure :

```
================================================================================
STRUCTURE COMPLÈTE DU RAPPORT
================================================================================

Rapport n° [X] - [Nom Projet] - [Date]

INFORMATIONS ADMINISTRATIVES:
Projet: [Nom]
Type: [Type de projet]
Localisation: [Lieu]
Maître d'ouvrage: [Client]
Mission contrôle: [Mission]
Entreprise exécutante: Genie Concept Innovation
Météo: [Conditions]
Date de génération: [Date]

================================================================================
INTRODUCTION:
[Une phrase introductive brève]

================================================================================
INFORMATIONS DU CHANTIER:

Partie 1 - Conditions et matériel
[Tableau des équipements]

Partie 2 - Personnel
[Tableau du personnel]

Partie 3 - Matériaux
[Tableau des matériaux]

================================================================================
RÉSUMÉ DES TRAVAUX EXÉCUTÉS:
[Paragraphe de 4-6 phrases]

================================================================================
RECOMMANDATIONS:
[Recommandations techniques et concrètes]

================================================================================
ANNEXES PHOTOGRAPHIQUES:
[Photos si fournies, sinon section vide]

================================================================================
Rédigé par:
[Nom de l'auteur]
[Profil de l'auteur]
Signature: _________________________________

================================================================================
```

## 🎯 Utilisation

### 1. Créer/Modifier un Projet
Lors de la création ou modification d'un projet, tu peux maintenant renseigner :
- **Client** (Maître d'ouvrage)
- **Mission contrôle**

### 2. Remplir les Informations Chantier
Avant de générer le rapport, remplis :
- Météo
- Équipements (Désignation, Présent, Marche, Immob, Panne)
- Personnel (Profil, Nombre)
- Matériaux (Désignation, Unité, Quantité)

### 3. Générer le Rapport
Le rapport sera automatiquement généré avec le nouveau format professionnel !

## ✅ Checklist

- [ ] Base de données mise à jour (champs `client` et `control_mission` ajoutés)
- [ ] Tester la création d'un nouveau projet
- [ ] Remplir les informations chantier
- [ ] Générer un rapport de test
- [ ] Vérifier que le format correspond au modèle

## 🆘 En Cas de Problème

### Erreur "Unknown column 'client'"
→ La base de données n'a pas été mise à jour. Exécuter le SQL via phpMyAdmin.

### Les tableaux ne s'affichent pas correctement
→ C'est normal, l'IA génère des tableaux en texte ASCII. Ils s'afficheront bien dans le PDF/Word.

### Les champs client/control_mission sont vides
→ Éditer le projet et renseigner ces informations.

## 📚 Fichiers Créés

- ✅ `add_project_info_fields.sql` - Script SQL de mise à jour
- ✅ `MISE_A_JOUR_BDD_RAPPORT.md` - Ce guide

---

**Prochaine étape** : Mettre à jour la base de données via phpMyAdmin ! 🚀
