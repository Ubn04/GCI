# 🔧 Dépannage - PDF n'arrive pas à ouvrir

## 🆘 Problème rapporté
Le PDF n'arrive pas à s'ouvrir correctement après le téléchargement.

## ✅ Solutions appliquées

### 1. **Nettoyage des buffers avant l'envoi**
```php
// Nettoyer les buffers
if (ob_get_level()) {
    ob_end_clean();
}
```
Cela élimine tout contenu accidentellement envoyé avant les headers.

### 2. **Optimisation des en-têtes HTTP**
**Avant:**
```php
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="test.pdf"');
header('Content-Length: ' . strlen($content));
header('Pragma: no-cache');
header('Expires: 0');
```

**Après:**
```php
header('Content-Type: application/pdf', true);
header('Content-Disposition: inline; filename="test.pdf"', true);
header('Cache-Control: private, no-cache, no-store, must-revalidate', true);
header('Expires: 0', true);
```

**Changements:**
- ✅ Ajout du paramètre `true` pour remplacer les en-têtes existants
- ✅ Utilisation de `inline` au lieu de `attachment` (certains navigateurs supportent mieux)
- ✅ Utilisation de `Cache-Control` au lieu de `Pragma`
- ❌ Suppression de `Content-Length` (peut causer des problèmes)

### 3. **Fichiers modifiés**
- `app/controllers/ReportControllerGemini.php` - Fonctions exportDraftPDF et exportReportPDF
- `app/controllers/ReportController.php` - Mêmes fonctions
- `app/services/PDFGenerator.php` - (pas modifié, structure OK)

---

## 🧪 Test de dépannage

### Étape 1: Tester la génération PDF basique
1. Ouvrez: `http://localhost/chantier-ai-php/test_pdf_simple.php`
2. Un PDF test doit se générer ou s'ouvrir
3. Si le PDF s'ouvre: ✅ DomPDF fonctionne
4. Si le PDF ne s'ouvre pas: ❌ Problème avec DomPDF

### Étape 2: Si le test PDF échoue
```bash
# Vérifier que DomPDF est installé
ls -la vendor/dompdf/

# Vérifier les permissions
chmod -R 755 vendor/dompdf/
```

### Étape 3: Vérifier les logs d'erreur
```bash
# Vérifier error_log du serveur PHP
tail -f /path/to/php/error.log

# Ou via un fichier de log personnalisé
# Les erreurs sont enregistrées dans les logs PHP
```

---

## 🔍 Causes courantes et solutions

| Problème | Cause | Solution |
|----------|-------|----------|
| PDF vide | DomPDF ne génère rien | Vérifier DomPDF installation |
| Erreur "Impossible d'ouvrir" | En-têtes mal ordonnés | ✅ Corrigé |
| "Fichier corrompu" | Content-Length incorrect | ✅ Supprimé |
| Pas de téléchargement | Buffer pas nettoyé | ✅ Nettoyage ajouté |
| Erreur 500 | Exception dans la génération | Vérifier les logs |

---

## 📋 Checklist de vérification

- [ ] `test_pdf_simple.php` génère un PDF valide
- [ ] Les en-têtes HTTP sont corrects (Content-Type: application/pdf)
- [ ] Pas d'erreur PHP 500
- [ ] Le navigateur peut ouvrir le PDF
- [ ] Le nom du fichier est correct
- [ ] Les données (tableaux) sont visibles dans le PDF

---

## 💡 Si ça ne marche toujours pas

### Option 1: Utiliser le mode "attachment" au lieu de "inline"
```php
header('Content-Disposition: attachment; filename="rapport.pdf"', true);
```

### Option 2: Vérifier la version de DomPDF
```php
echo DOMPDF_VERSION; // Doit afficher 3.1 ou plus
```

### Option 3: Forcer le download avec JavaScript
```javascript
const a = document.createElement('a');
a.href = url; // URL du PDF
a.download = 'rapport.pdf';
a.click();
```

### Option 4: Utiliser une URL externe pour télécharger le PDF
```php
// Au lieu d'envoyer directement
// Créer le fichier dans un dossier temporaire
$filepath = 'uploads/pdf/' . $filename;
file_put_contents($filepath, $pdfContent);

// Puis rediriger vers le fichier
header('Location: ' . APP_URL . '/uploads/pdf/' . $filename);
```

---

## 📞 Commandes de test

### Test DomPDF installation
```php
<?php
use Dompdf\Dompdf;
$dompdf = new Dompdf();
echo "✅ DomPDF est correctement installé";
?>
```

### Test de génération PDF simple
```php
<?php
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml('<html><body>Test</body></html>');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$output = $dompdf->output();
if (strlen($output) > 1000) {
    echo "✅ PDF généré correctement (" . strlen($output) . " bytes)";
} else {
    echo "❌ PDF trop petit";
}
?>
```

---

## 🎯 Prochaines étapes

1. **Testez** `test_pdf_simple.php` pour valider la correction
2. **Testez** l'export depuis la page de génération de rapport
3. **Testez** l'export depuis la page d'affichage des rapports
4. **Vérifiez** que le PDF s'ouvre correctement dans Adobe Reader

---

## 📖 Références

- [Documentation DomPDF](https://github.com/dompdf/dompdf)
- [Headers HTTP pour PDF](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition)
- [RFC 2183 - Content-Disposition](https://tools.ietf.org/html/rfc2183)

---

**Date de mise à jour:** 11/05/2026
**Status:** ✅ Corrections appliquées
