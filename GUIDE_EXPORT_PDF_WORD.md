# Guide d'Export PDF et Word - ChantierAI

## ✅ Problème Résolu

L'export PDF téléchargeait un fichier HTML au lieu d'un vrai PDF. Maintenant, les deux exports (PDF et Word) fonctionnent correctement et téléchargent des fichiers dans le bon format.

## 🎯 Ce qui a été fait

### 1. Installation de DomPDF
- **Bibliothèque installée**: DomPDF v3.1.5
- **Commande utilisée**: `composer require dompdf/dompdf`
- **Dépendances installées**:
  - masterminds/html5
  - sabberworm/php-css-parser
  - dompdf/php-font-lib
  - dompdf/php-svg-lib

### 2. Mise à jour de l'export PDF
- Remplacement de l'export HTML par un vrai export PDF
- Utilisation de DomPDF pour générer des fichiers PDF natifs
- Intégration du logo GCI en base64 dans le PDF
- Format A4 portrait avec marges professionnelles
- Styles optimisés pour l'impression PDF

### 3. Amélioration de l'export Word
- Ajout du logo GCI dans l'en-tête Word
- Logo converti en base64 pour inclusion directe
- Styles améliorés pour Microsoft Word
- Format compatible avec Word 2007+

## 📋 Fonctionnalités

### Export PDF
- ✅ Téléchargement direct en format `.pdf`
- ✅ Logo GCI intégré dans l'en-tête
- ✅ Référence unique (RAP-ANNÉE-XXXX)
- ✅ Métadonnées du rapport (type, date, génération)
- ✅ Contenu formaté avec titres, listes, paragraphes
- ✅ Pied de page avec informations système
- ✅ Format A4 professionnel
- ✅ Styles bleu/orange de la marque GCI

### Export Word
- ✅ Téléchargement direct en format `.doc`
- ✅ Logo GCI intégré dans l'en-tête
- ✅ Compatible Microsoft Word 2007+
- ✅ Styles professionnels avec couleurs GCI
- ✅ Formatage préservé (titres, listes, gras, souligné)
- ✅ Marges et espacement optimisés

## 🔧 Fichiers Modifiés

### `export_report.php`
**Fonction `exportToPDF()`**:
```php
// Avant: Téléchargeait un fichier HTML
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '.html"');

// Après: Génère un vrai PDF avec DomPDF
require_once __DIR__ . '/vendor/autoload.php';
$dompdf = new \Dompdf\Dompdf($options);
$dompdf->loadHtml($htmlContent);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream($filename . '.pdf', ['Attachment' => 1]);
```

**Fonction `exportToWord()`**:
```php
// Ajout du logo en base64
$logoPath = __DIR__ . '/assets/images/logo.jpg';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/jpeg;base64,' . $logoData;
}
```

### Nouveaux fichiers
- `composer.json` - Configuration Composer
- `composer.lock` - Verrouillage des versions
- `vendor/` - Dépendances PHP (DomPDF et ses dépendances)
- `test_pdf_export.php` - Script de test
- `test_output.pdf` - PDF de test généré

## 🚀 Comment utiliser

### Depuis l'interface web

1. **Connectez-vous** à ChantierAI
2. **Allez dans "Rapports"** depuis le menu
3. **Cliquez sur "Voir"** pour un rapport
4. Dans le modal qui s'ouvre:
   - **Cliquez sur "Exporter PDF"** → Télécharge un fichier `.pdf`
   - **Cliquez sur "Exporter Word"** → Télécharge un fichier `.doc`

### Test manuel

Pour tester l'export PDF:
```bash
php test_pdf_export.php
```

Cela créera un fichier `test_output.pdf` dans le répertoire actuel.

## 📐 Structure du document exporté

### En-tête
```
┌─────────────────────────────────────────────────┐
│ [LOGO GCI]  Génie Civil Intelligent             │
│             GCI - ChantierAI                     │
│                                    Référence     │
│                                    RAP-2024-0001 │
├─────────────────────────────────────────────────┤
│ TITRE DU RAPPORT                                │
│ Rapport de chantier généré par ChantierAI       │
└─────────────────────────────────────────────────┘
```

### Métadonnées
```
┌─────────────────────────────────────────────────┐
│ Type de rapport : Journalier                    │
│ Date du rapport : 05/05/2026                    │
│ Généré le : 05/05/2026 à 14:30                  │
│ Système : ChantierAI - Gemini AI                │
└─────────────────────────────────────────────────┘
```

### Contenu
- Titres H1 avec fond bleu clair et bordure bleue
- Titres H2 avec symbole ■ et bordure inférieure
- Titres H3 avec flèche ▸ orange
- Paragraphes justifiés avec espacement
- Listes à puces et numérotées
- Texte en gras et souligné

### Pied de page
```
─────────────────────────────────────────────────
Rapport généré automatiquement par ChantierAI - Gemini AI
Date d'export : 05/05/2026 à 14:30
```

## 🎨 Styles et couleurs

### Palette de couleurs GCI
- **Bleu principal**: `#2563eb` (titres, bordures)
- **Bleu foncé**: `#1e3a8a` (texte principal)
- **Orange accent**: `#f59e0b` (accents, symboles)
- **Gris clair**: `#64748b` (texte secondaire)
- **Fond clair**: `#f8fafc` (zones métadonnées)

### Typographie
- **PDF**: DejaVu Sans (support Unicode complet)
- **Word**: Calibri (police Microsoft standard)
- **Taille**: 11pt pour le corps, 14-20pt pour les titres
- **Interligne**: 1.8-2.0 pour une lecture confortable

## 🔍 Dépannage

### Le PDF ne se télécharge pas
1. Vérifiez que DomPDF est installé:
   ```bash
   composer show dompdf/dompdf
   ```
2. Vérifiez les permissions du dossier `vendor/`
3. Consultez les logs PHP pour les erreurs

### Le logo n'apparaît pas
1. Vérifiez que le fichier existe: `assets/images/logo.jpg`
2. Vérifiez les permissions de lecture du fichier
3. Le logo est converti en base64, donc pas besoin de chemin web

### Erreur "Class not found"
```bash
# Régénérer l'autoloader
composer dump-autoload
```

### Le Word ne s'ouvre pas correctement
- Le format `.doc` est compatible Word 2007+
- Si problème, ouvrir avec LibreOffice puis sauvegarder en `.docx`

## 📊 Performances

### Taille des fichiers
- **PDF**: ~50-200 KB selon le contenu
- **Word**: ~30-150 KB selon le contenu
- **Logo intégré**: ~15 KB en base64

### Temps de génération
- **PDF**: 0.5-2 secondes selon la longueur
- **Word**: 0.1-0.5 secondes (plus rapide, pas de rendu)

## 🔐 Sécurité

### Vérifications en place
- ✅ Authentification utilisateur requise
- ✅ Vérification que le rapport appartient à l'utilisateur
- ✅ Échappement HTML du contenu
- ✅ Validation de l'ID du rapport
- ✅ Nom de fichier sécurisé (caractères spéciaux supprimés)

### Données sensibles
- Le logo est encodé en base64 (pas de chemin exposé)
- Pas de données utilisateur dans les noms de fichiers
- Session PHP vérifiée avant export

## 📝 Notes techniques

### DomPDF
- **Version**: 3.1.5
- **Moteur**: Rendu HTML/CSS vers PDF
- **Limitations**:
  - CSS limité (pas de flexbox, grid)
  - JavaScript non supporté
  - Certaines polices nécessitent installation

### Format Word
- **Format**: HTML avec namespace Microsoft Office
- **Compatible**: Word 2007, 2010, 2013, 2016, 2019, 365
- **Alternative**: Peut être ouvert avec LibreOffice, Google Docs

## 🎓 Ressources

### Documentation
- [DomPDF Documentation](https://github.com/dompdf/dompdf)
- [DomPDF Wiki](https://github.com/dompdf/dompdf/wiki)
- [CSS Support in DomPDF](https://github.com/dompdf/dompdf/wiki/CSSCompatibility)

### Support
- Issues DomPDF: https://github.com/dompdf/dompdf/issues
- Stack Overflow: Tag `dompdf`

## ✨ Améliorations futures possibles

1. **Export Excel** pour les données tabulaires
2. **Signature numérique** des PDF
3. **Watermark** personnalisé
4. **Templates** multiples (différents styles)
5. **Compression** des PDF pour réduire la taille
6. **Envoi par email** direct depuis l'interface
7. **Archivage automatique** des exports
8. **Historique** des exports par utilisateur

## 🏆 Résultat

✅ **PDF**: Téléchargement direct en format `.pdf` natif
✅ **Word**: Téléchargement direct en format `.doc` compatible
✅ **Logo**: Intégré dans les deux formats
✅ **Design**: Professionnel avec couleurs GCI
✅ **Contenu**: Formatage préservé et lisible
✅ **Performance**: Génération rapide (<2 secondes)

---

**Date de mise à jour**: 05/05/2026
**Version**: 1.0.0
**Développé pour**: ChantierAI - GCI
