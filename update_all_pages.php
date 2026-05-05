<?php
/**
 * Script pour mettre à jour toutes les pages avec le nouveau design moderne
 */

echo "🚀 Mise à jour du design de toutes les pages...\n\n";

// Liste des fichiers à mettre à jour
$files = [
    'app/views/dashboard/index.php',
    'app/views/projects/index.php',
    'app/views/projects/show.php',
    'app/views/reports/index.php',
    'app/views/reports/select_project.php',
    'app/views/reports/monthly.php',
    'app/views/reports/yearly.php',
    'app/views/auth/profile.php'
];

$updated = 0;
$errors = 0;

foreach ($files as $file) {
    echo "📄 Traitement de $file...\n";
    
    if (!file_exists($file)) {
        echo "   ❌ Fichier non trouvé\n";
        $errors++;
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Remplacer l'ancien CSS inline par le nouveau
    $content = preg_replace(
        '/<link rel="stylesheet" href="https:\/\/cdnjs\.cloudflare\.com\/ajax\/libs\/font-awesome\/6\.4\.0\/css\/all\.min\.css">/',
        '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">' . "\n" . '    <link rel="stylesheet" href="assets/css/modern-style.css">',
        $content
    );
    
    // Remplacer app-shell par app-container
    $content = str_replace('class="app-shell"', 'class="app-container"', $content);
    
    // Remplacer le sidebar par l'include
    $content = preg_replace(
        '/<aside class="sidebar">.*?<\/aside>/s',
        '<?php include \'app/views/components/sidebar.php\'; ?>',
        $content
    );
    
    // Remplacer class="content" par class="main-content"
    $content = preg_replace(
        '/<main class="content">/',
        '<div class="main-content">',
        $content
    );
    
    $content = preg_replace(
        '/<\/main>/',
        '</div>',
        $content
    );
    
    // Sauvegarder
    if (file_put_contents($file, $content)) {
        echo "   ✅ Mis à jour avec succès\n";
        $updated++;
    } else {
        echo "   ❌ Erreur lors de la sauvegarde\n";
        $errors++;
    }
}

echo "\n";
echo "═══════════════════════════════════════\n";
echo "✅ Fichiers mis à jour : $updated\n";
echo "❌ Erreurs : $errors\n";
echo "═══════════════════════════════════════\n";
echo "\n";
echo "🎉 Mise à jour terminée !\n";
echo "📋 Prochaines étapes :\n";
echo "   1. Testez les pages mises à jour\n";
echo "   2. Vérifiez que le sidebar est fixe\n";
echo "   3. Testez la responsivité\n";
?>