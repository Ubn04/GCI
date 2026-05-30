# 📋 Guide d'utilisation - Génération de rapports PDF

## ✅ Fonctionnalités implémentées

Ce document décrit la nouvelle fonctionnalité de génération de rapports au format PDF avec un layout professionnel et structuré.

### 🎯 Objectif
Permettre aux utilisateurs de générer et d'exporter des rapports de chantier au format PDF avec un format standardisé contenant:
- Informations administratives du projet
- Tableau des équipements (présent, marche, immobilisé, panne)
- Tableau du personnel (profils et nombres)
- Tableau des matériaux (désignation, unité, quantité)
- Introduction et observations additionnelles

---

## 🏗️ Architecture implémentée

### 1. **Service de génération PDF**
**Fichier:** `app/services/PDFGenerator.php`

Classe responsable de la génération des PDF formatés utilisant DomPDF.

**Méthodes principales:**
- `generateStructuredReport()` - Génère un rapport structuré avec les données du brouillon
- `generateReport()` - Génère un rapport classique
- `downloadPDF()` - Gère le téléchargement du PDF

### 2. **Routes de l'application**
**Fichier:** `index.php`

Routes ajoutées:
```php
case 'reports/export-draft-pdf':
    // Exporte le brouillon actuel en PDF formaté
    $controller->exportDraftPDF($project_id);

case 'reports/export-pdf':
    // Exporte un rapport généré en PDF
    $controller->exportReportPDF($report_id);
```

### 3. **Contrôleur des rapports**
**Fichier:** `app/controllers/ReportControllerGemini.php`

Méthodes ajoutées:
- `exportDraftPDF($project_id)` - Exporte les données du brouillon en PDF
- `exportReportPDF($report_id)` - Exporte un rapport généré en PDF

### 4. **Interface utilisateur**
**Fichiers modifiés:**
- `app/views/reports/generate.php` - Ajout du bouton "Exporter PDF" et script d'export
- `app/views/reports/show.php` - Modification du bouton d'export PDF pour utiliser le backend

---

## 📖 Guide d'utilisation

### Étape 1: Accéder à la génération de rapport
1. Aller au dashboard
2. Créer ou sélectionner un projet
3. Cliquer sur "Créer un rapport" ou "Générer un rapport"
4. Remplir les informations du chantier (équipements, personnel, matériaux, météo)

### Étape 2: Exporter le brouillon en PDF
1. Sur la page de génération, cliquer sur le bouton vert **"Exporter PDF"**
2. Le PDF sera généré automatiquement avec:
   - Les informations administratives du projet
   - Les tableaux (équipements, personnel, matériaux)
   - La date et l'heure de génération
3. Le fichier sera téléchargé avec le nom: `Rapport_[NomProjet]_[JJ-MM-YYYY_HHMMSS].pdf`

### Étape 3: Exporter un rapport généré
1. Aller dans "Mes rapports"
2. Sélectionner un rapport généré
3. Cliquer sur le bouton **"Exporter PDF"**
4. Le rapport sera téléchargé au format PDF professionnel

---

## 📊 Format du rapport PDF

### Structure complète:
```
╔════════════════════════════════════════════════════════════════════╗
║         [LOGO/EN-TÊTE]  Rapport de Chantier                       ║
╠════════════════════════════════════════════════════════════════════╣
║  INFORMATIONS ADMINISTRATIVES                                     ║
├─────────────────────────────────────────────────────────────────────┤
│ • Projet: Construction Pont Alpha                                 │
│ • Type: Génie Civil                                               │
│ • Localisation: Cotonou, Bénin                                   │
│ • Maître d'ouvrage: Ministère des Infrastructures               │
│ • Mission contrôle: Bureau d'études SOGEA                        │
│ • Entreprise: Genie Concept Innovation                           │
│ • Météo: Ensoleillé                                             │
│ • Date: 08/05/2026 à XX:XX                                      │
├─────────────────────────────────────────────────────────────────────┤
║  TABLEAU 1 - ÉQUIPEMENTS                                         ║
├──────────────────┬─────────┬────────┬────────┬────────┤
│ Désignation      │ Présent │ Marche │ Immob  │ Panne  │
├──────────────────┼─────────┼────────┼────────┼────────┤
│ Grue mobile 50T  │   Oui   │  Oui   │  Non   │  Non   │
│ Bétonnière 500L  │   Oui   │  Oui   │  Non   │  Non   │
└──────────────────┴─────────┴────────┴────────┴────────┘
├─────────────────────────────────────────────────────────────────────┤
║  TABLEAU 2 - PERSONNEL                                           ║
├──────────────────┬─────────┤
│ Profil           │ Nombre  │
├──────────────────┼─────────┤
│ Ingénieur        │    2    │
│ Chef de chantier │    1    │
└──────────────────┴─────────┘
├─────────────────────────────────────────────────────────────────────┤
║  TABLEAU 3 - MATÉRIAUX                                           ║
├──────────────────────────┬─────────┬───────────┤
│ Désignation              │ Unité   │ Quantité  │
├──────────────────────────┼─────────┼───────────┤
│ Béton C25/30             │   m3    │    15     │
│ Acier HA                 │    T    │   2.5     │
└──────────────────────────┴─────────┴───────────┘
╚════════════════════════════════════════════════════════════════════╝
```

---

## 🔧 Configuration technique

### Dépendances
- DomPDF v3.1 (déjà installé dans `vendor/dompdf/`)

### Classe PDFGenerator
La classe génère du HTML et le convertit en PDF avec:
- Police: DejaVu Sans
- Format: A4 Portrait
- Styles: Couleurs professionnelles (#1e3a8a, #2563eb)

---

## 🐛 Dépannage

### Le PDF ne se télécharge pas
1. Vérifier que DomPDF est installé: `vendor/dompdf/dompdf/`
2. Vérifier les permissions d'écriture du serveur
3. Vérifier les logs: `error_log` dans la configuration PHP

### Le PDF est vide ou mal formaté
1. Vérifier que les données du brouillon sont correctement remplies
2. Vérifier que le projet a un nom et des informations de localisation
3. Vérifier la console du navigateur pour les erreurs JavaScript

### Erreur "Accès refusé"
1. Vérifier que l'utilisateur est connecté
2. Vérifier que l'utilisateur est propriétaire du projet/rapport
3. Vérifier que la session est valide

---

## 📝 Notes d'implémentation

### Fichiers créés:
- `app/services/PDFGenerator.php` - Service de génération PDF

### Fichiers modifiés:
- `index.php` - Routes d'export PDF
- `app/controllers/ReportControllerGemini.php` - Méthodes d'export
- `app/views/reports/generate.php` - Bouton d'export PDF (brouillon)
- `app/views/reports/show.php` - Export PDF pour rapports générés

### Points clés:
1. Utilisation de DomPDF pour la génération PDF côté serveur
2. Validation de l'accès (vérification du propriétaire du projet)
3. Gestion d'erreur avec logs
4. Support du fallback à html2pdf en cas d'erreur du backend

---

## 🚀 Prochaines étapes possibles

1. **Ajout d'un logo** - Incorporer le logo GCI au PDF
2. **Signatures numériques** - Ajouter des zones de signature
3. **Métadonnées PDF** - Ajouter auteur, sujet, mots-clés
4. **Modèles personnalisés** - Permettre aux utilisateurs de choisir le format
5. **Export multi-format** - Ajouter Excel, Word avec formatage identique
6. **Watermark** - Ajouter "Brouillon" ou "Signé" selon le statut
7. **Compression** - Optimiser la taille des fichiers PDF

---

## 📞 Support

Pour toute question ou problème:
1. Vérifier les logs de l'application
2. Consulter la documentation DomPDF
3. Vérifier la configuration du serveur PHP

---

**Dernière mise à jour:** 11/05/2026
**Version:** 1.0
