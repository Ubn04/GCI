# 🚀 GUIDE TEST RAPIDE - Nouveau Format Rapport

## ⚡ ÉTAPE 1 : BASE DE DONNÉES (2 minutes)

### Ouvrir phpMyAdmin
1. Ouvrir le navigateur
2. Aller sur : **http://localhost/phpmyadmin**
3. Cliquer sur **chantier_ai** (à gauche)
4. Cliquer sur l'onglet **SQL** (en haut)

### Copier-Coller ce Code
```sql
ALTER TABLE projects 
ADD COLUMN client VARCHAR(200) DEFAULT 'Non spécifié' AFTER description;

ALTER TABLE projects 
ADD COLUMN control_mission VARCHAR(200) DEFAULT 'Non spécifié' AFTER client;
```

### Cliquer sur "Exécuter"
✅ Tu devrais voir : "2 lignes affectées"

---

## ⚡ ÉTAPE 2 : CRÉER UN PROJET TEST (3 minutes)

### Aller sur ton application
**http://localhost/chantier-ai-php**

### Créer un Nouveau Projet
1. Cliquer sur **"Projets"** dans le menu
2. Cliquer sur **"Nouveau Projet"**
3. Remplir le formulaire :

```
Nom du projet : Construction Pont Alpha
Localisation : Cotonou, Bénin
Type de projet : Génie Civil
Date de début : 01/05/2026
Description : Construction d'un pont de 50m
Maître d'ouvrage : Ministère des Infrastructures
Mission contrôle : Bureau d'études SOGEA
```

4. Cliquer sur **"Créer le projet"**

---

## ⚡ ÉTAPE 3 : REMPLIR INFOS CHANTIER (5 minutes)

### Ouvrir le Projet
1. Cliquer sur **"Ouvrir"** sur le projet "Construction Pont Alpha"

### Remplir la Météo
- Sélectionner : **Ensoleillé**

### Ajouter des Équipements
Cliquer sur **"Ajouter un équipement"** et remplir :

**Équipement 1 :**
- Désignation : Grue mobile 50T
- Présent : ✅ Oui
- Marche : ✅ Oui
- Immob : ❌ Non
- Panne : ❌ Non

**Équipement 2 :**
- Désignation : Bétonnière 500L
- Présent : ✅ Oui
- Marche : ✅ Oui
- Immob : ❌ Non
- Panne : ❌ Non

### Ajouter du Personnel
Cliquer sur **"Ajouter du personnel"** et remplir :

**Personnel 1 :**
- Profil : Ingénieur
- Nombre : 2

**Personnel 2 :**
- Profil : Ouvrier
- Nombre : 8

### Ajouter des Matériaux
Cliquer sur **"Ajouter un matériau"** et remplir :

**Matériau 1 :**
- Désignation : Béton C25/30
- Unité : m³
- Quantité : 15

**Matériau 2 :**
- Désignation : Acier HA
- Unité : T
- Quantité : 2.5

### Sauvegarder
Cliquer sur **"Enregistrer les informations"**

---

## ⚡ ÉTAPE 4 : GÉNÉRER LE RAPPORT (2 minutes)

### Aller dans Rapports
1. Cliquer sur **"Rapports"** dans le menu
2. Cliquer sur **"Générer un rapport"**

### Sélectionner le Projet
- Sélectionner : **Construction Pont Alpha**
- Cliquer sur **"Continuer"**

### Remplir les Notes
```
Travaux de fondation en cours. Coulage des pieux P1 à P4 réalisé. 
Ferraillage des semelles en préparation. Bonne coordination des équipes.
```

### Générer
- Cliquer sur **"Générer le rapport"**
- Attendre 10-15 secondes

---

## ⚡ ÉTAPE 5 : VÉRIFIER LE RÉSULTAT (1 minute)

### Le Rapport Doit Contenir :

✅ **En-tête**
```
Rapport n° [X] - Construction Pont Alpha - [Date]
```

✅ **Informations Administratives**
```
Projet: Construction Pont Alpha
Type: Génie Civil
Localisation: Cotonou, Bénin
Maître d'ouvrage: Ministère des Infrastructures
Mission contrôle: Bureau d'études SOGEA
Entreprise exécutante: Genie Concept Innovation
Météo: Ensoleillé
Date de génération: [Date et heure]
```

✅ **Introduction**
- Une seule phrase brève

✅ **Informations du Chantier**
- Tableau des équipements (Grue, Bétonnière)
- Tableau du personnel (Ingénieur, Ouvrier)
- Tableau des matériaux (Béton, Acier)

✅ **Résumé des Travaux**
- Paragraphe de 4-6 phrases

✅ **Recommandations**
- Recommandations techniques

✅ **Signature**
```
Rédigé par:
[Ton nom]
[Ton profil]
Signature: _________________________________
```

---

## ⚡ ÉTAPE 6 : TESTER L'EXPORT (1 minute)

### Export PDF
1. Cliquer sur **"Exporter en PDF"**
2. Le fichier doit se télécharger : `rapport_[X].pdf`
3. Ouvrir le PDF et vérifier le contenu

### Export Word
1. Cliquer sur **"Exporter en Word"**
2. Le fichier doit se télécharger : `rapport_[X].doc`
3. Ouvrir le Word et vérifier le contenu

---

## ✅ CHECKLIST FINALE

- [ ] Base de données mise à jour (2 champs ajoutés)
- [ ] Projet test créé avec client et mission contrôle
- [ ] Informations chantier remplies (météo, équipements, personnel, matériaux)
- [ ] Rapport généré avec succès
- [ ] Format correspond au modèle (tableaux, sections, signature)
- [ ] Export PDF fonctionne
- [ ] Export Word fonctionne

---

## 🎉 C'EST PRÊT !

Si tout fonctionne, ton système génère maintenant des rapports professionnels ! 🚀

**Temps total** : ~15 minutes

---

## 🐛 Problèmes Courants

### Erreur "Unknown column 'client'"
→ La base de données n'a pas été mise à jour. Retourne à l'ÉTAPE 1.

### Les champs client/control_mission sont vides
→ Édite le projet et remplis ces champs.

### Le rapport n'a pas le bon format
→ Vérifie que tu as bien rempli les informations chantier (ÉTAPE 3).

### Les tableaux sont bizarres
→ C'est normal, l'IA génère des tableaux ASCII. Ils s'affichent bien dans le PDF/Word.

---

**Date** : 8 mai 2026  
**Statut** : ✅ PRÊT À TESTER  
**Version** : 2.0 - Format Professionnel
