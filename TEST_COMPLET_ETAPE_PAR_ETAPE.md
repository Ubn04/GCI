# 🎯 TEST COMPLET - ÉTAPE PAR ÉTAPE

## ⚡ ÉTAPE 0 : EXÉCUTER LE SQL (2 min)

### Ouvrir phpMyAdmin
```
http://localhost/phpmyadmin
```

### Cliquer sur "chantier_ai" → "SQL"

### Copier-coller ce code :
```sql
ALTER TABLE projects 
ADD COLUMN client VARCHAR(200) DEFAULT 'Non spécifié' AFTER description;

ALTER TABLE projects 
ADD COLUMN control_mission VARCHAR(200) DEFAULT 'Non spécifié' AFTER client;
```

### Cliquer sur "Exécuter"
✅ Tu devrais voir : "2 lignes affectées"

---

## ⚡ ÉTAPE 1 : CRÉER UN PROJET (2 min)

### Aller sur ton application
```
http://localhost/chantier-ai-php
```

### Menu : Projets → Nouveau Projet

### Copier-coller ces données :

**Nom du projet :**
```
Construction Pont Alpha
```

**Localisation :**
```
Cotonou, Bénin
```

**Type de projet :**
```
Génie Civil
```

**Date de début :**
```
01/05/2026
```

**Description :**
```
Construction d'un pont de 50m avec fondations profondes
```

**Maître d'ouvrage (Client) :**
```
Ministère des Infrastructures
```

**Mission contrôle :**
```
Bureau d'études SOGEA
```

### Cliquer sur "Créer le projet"

---

## ⚡ ÉTAPE 2 : OUVRIR LE PROJET (10 sec)

### Dans la liste des projets, cliquer sur "Ouvrir"

Tu arrives sur la page "Informations du chantier"

---

## ⚡ ÉTAPE 3 : REMPLIR LA MÉTÉO (10 sec)

### Sélectionner : ☀️ Ensoleillé

---

## ⚡ ÉTAPE 4 : AJOUTER DES ÉQUIPEMENTS (2 min)

### Équipement 1

**Désignation :**
```
Grue mobile 50T
```

**Cocher :**
- ✅ Présent : Oui
- ✅ Marche : Oui
- ❌ Immob : Non
- ❌ Panne : Non

**Cliquer sur "Créer"**

---

### Équipement 2

**Désignation :**
```
Bétonnière 500L
```

**Cocher :**
- ✅ Présent : Oui
- ✅ Marche : Oui
- ❌ Immob : Non
- ❌ Panne : Non

**Cliquer sur "Créer"**

---

### Équipement 3

**Désignation :**
```
Compacteur vibrant
```

**Cocher :**
- ✅ Présent : Oui
- ❌ Marche : Non
- ✅ Immob : Oui
- ❌ Panne : Non

**Cliquer sur "Créer"**

---

## ⚡ ÉTAPE 5 : AJOUTER DU PERSONNEL (2 min)

### Personnel 1

**Profil :**
```
Ingénieur
```

**Nombre :**
```
2
```

**Cliquer sur "Créer"**

---

### Personnel 2

**Profil :**
```
Chef de chantier
```

**Nombre :**
```
1
```

**Cliquer sur "Créer"**

---

### Personnel 3

**Profil :**
```
Ouvrier qualifié
```

**Nombre :**
```
8
```

**Cliquer sur "Créer"**

---

### Personnel 4

**Profil :**
```
Manœuvre
```

**Nombre :**
```
5
```

**Cliquer sur "Créer"**

---

## ⚡ ÉTAPE 6 : AJOUTER DES MATÉRIAUX (2 min)

### Matériau 1

**Désignation :**
```
Béton C25/30
```

**Unité :**
```
m3
```

**Quantité :**
```
15
```

**Cliquer sur "Créer"**

---

### Matériau 2

**Désignation :**
```
Acier HA
```

**Unité :**
```
T
```

**Quantité :**
```
2.5
```

**Cliquer sur "Créer"**

---

### Matériau 3

**Désignation :**
```
Coffrage métallique
```

**Unité :**
```
m2
```

**Quantité :**
```
50
```

**Cliquer sur "Créer"**

---

### Matériau 4

**Désignation :**
```
Gravier 15/25
```

**Unité :**
```
m3
```

**Quantité :**
```
8
```

**Cliquer sur "Créer"**

---

## ⚡ ÉTAPE 7 : SAUVEGARDER (5 sec)

### Cliquer sur "Enregistrer les informations"

✅ Tu devrais voir : "Informations sauvegardées avec succès"

---

## ⚡ ÉTAPE 8 : GÉNÉRER LE RAPPORT (2 min)

### Menu : Rapports → Générer un rapport

### Sélectionner le projet : "Construction Pont Alpha"

### Cliquer sur "Continuer"

---

### Ajouter des notes :

**Notes du terrain :**
```
Travaux de fondation en cours. Coulage des pieux P1 à P4 réalisé avec succès. Ferraillage des semelles en préparation. Bonne coordination des équipes. Le compacteur vibrant est immobilisé en attente de pièce de rechange. Livraison de 8 m³ de gravier effectuée ce matin. Conditions météorologiques favorables permettant une bonne cadence de travail.
```

### Cliquer sur "Générer le rapport"

⏳ Attendre 10-15 secondes...

---

## ⚡ ÉTAPE 9 : VÉRIFIER LE RAPPORT (1 min)

### Le rapport doit contenir :

✅ **En-tête**
```
================================================================================
STRUCTURE COMPLÈTE DU RAPPORT
================================================================================

Rapport n° RAP-2026-XXX - Construction Pont Alpha - 08/05/2026
```

✅ **Informations Administratives**
```
INFORMATIONS ADMINISTRATIVES:
Projet: Construction Pont Alpha
Type: Génie Civil
Localisation: Cotonou, Bénin
Maître d'ouvrage: Ministère des Infrastructures
Mission contrôle: Bureau d'études SOGEA
Entreprise exécutante: Genie Concept Innovation
Météo: Ensoleillé
Date de génération: 08/05/2026 à XX:XX
```

✅ **Introduction**
```
INTRODUCTION:
[Une phrase brève]
```

✅ **Tableau 1 - Équipements**
```
Partie 1 - Conditions et matériel
┌─────────────────────┬─────────┬────────┬────────┬────────┐
│ Désignation         │ Présent │ Marche │ Immob  │ Panne  │
├─────────────────────┼─────────┼────────┼────────┼────────┤
│ Grue mobile 50T     │   Oui   │  Oui   │  Non   │  Non   │
│ Bétonnière 500L     │   Oui   │  Oui   │  Non   │  Non   │
│ Compacteur vibrant  │   Oui   │  Non   │  Oui   │  Non   │
└─────────────────────┴─────────┴────────┴────────┴────────┘
```

✅ **Tableau 2 - Personnel**
```
Partie 2 - Personnel
┌──────────────────┬─────────┐
│ Profil           │ Nombre  │
├──────────────────┼─────────┤
│ Ingénieur        │    2    │
│ Chef de chantier │    1    │
│ Ouvrier qualifié │    8    │
│ Manœuvre         │    5    │
└──────────────────┴─────────┘
```

✅ **Tableau 3 - Matériaux**
```
Partie 3 - Matériaux
┌──────────────────────┬─────────┬───────────┐
│ Désignation          │ Unité   │ Quantité  │
├──────────────────────┼─────────┼───────────┤
│ Béton C25/30         │   m3    │    15     │
│ Acier HA             │    T    │   2.5     │
│ Coffrage métallique  │   m2    │    50     │
│ Gravier 15/25        │   m3    │     8     │
└──────────────────────┴─────────┴───────────┘
```

✅ **Résumé des Travaux**
```
RÉSUMÉ DES TRAVAUX EXÉCUTÉS:
[Paragraphe de 4-6 phrases]
```

✅ **Recommandations**
```
RECOMMANDATIONS:
[Liste de recommandations techniques]
```

✅ **Signature**
```
Rédigé par:
[Ton nom]
[Ton profil]
Signature: _________________________________
```

---

## ⚡ ÉTAPE 10 : TESTER LES EXPORTS (1 min)

### Export PDF
1. Cliquer sur "Exporter en PDF"
2. Le fichier `rapport_RAP-2026-XXX.pdf` se télécharge
3. Ouvrir le PDF et vérifier

### Export Word
1. Cliquer sur "Exporter en Word"
2. Le fichier `rapport_RAP-2026-XXX.doc` se télécharge
3. Ouvrir le Word et vérifier

---

## ✅ CHECKLIST FINALE

- [ ] SQL exécuté (2 champs ajoutés)
- [ ] Projet créé avec client et mission contrôle
- [ ] Météo sélectionnée (Ensoleillé)
- [ ] 3 équipements ajoutés
- [ ] 4 profils de personnel ajoutés
- [ ] 4 matériaux ajoutés
- [ ] Informations sauvegardées
- [ ] Rapport généré
- [ ] Format vérifié (tableaux ASCII)
- [ ] Export PDF testé
- [ ] Export Word testé

---

## 🎉 C'EST FAIT !

Si tout fonctionne, ton système génère maintenant des rapports professionnels ! 🚀

**Temps total : ~15 minutes**

---

## 🚀 PUSH SUR GITHUB

```bash
git add .
git commit -m "✨ Version 2.0 - Nouveau format de rapport professionnel avec tableaux"
git push
```

---

**Date** : 8 mai 2026  
**Version** : 2.0  
**Statut** : ✅ PRÊT À TESTER
