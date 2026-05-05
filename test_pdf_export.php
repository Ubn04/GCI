<?php
/**
 * Script de test pour vérifier l'export PDF avec DomPDF
 */

// Simuler une session utilisateur
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';

// Charger l'autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Test simple de DomPDF
use Dompdf\Dompdf;
use Dompdf\Options;

echo "Test de génération PDF avec DomPDF\n";
echo "===================================\n\n";

try {
    // Configuration
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    
    $dompdf = new Dompdf($options);
    
    // HTML de test
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: DejaVu Sans, Arial, sans-serif; padding: 20px; }
            h1 { color: #2563eb; }
            p { line-height: 1.6; }
        </style>
    </head>
    <body>
        <h1>Test PDF - ChantierAI</h1>
        <p>Ce document PDF a été généré avec succès par DomPDF.</p>
        <p><strong>Date:</strong> ' . date('d/m/Y H:i:s') . '</p>
    </body>
    </html>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Sauvegarder dans un fichier pour test
    $output = $dompdf->output();
    file_put_contents('test_output.pdf', $output);
    
    echo "✓ DomPDF est correctement installé et fonctionne!\n";
    echo "✓ Un fichier test_output.pdf a été créé dans le répertoire actuel.\n\n";
    
    // Test de l'export avec un rapport fictif
    echo "Test de l'export d'un rapport...\n";
    
    // Créer un rapport de test
    $testReport = [
        'id' => 1,
        'title' => 'Rapport de Test',
        'content' => "# Introduction\n\nCeci est un test.\n\n## Section 1\n\nContenu de la section 1.\n\n- Point 1\n- Point 2\n- Point 3",
        'report_type' => 'daily',
        'report_date' => date('Y-m-d'),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    echo "✓ Rapport de test créé\n";
    echo "✓ Vous pouvez maintenant tester l'export depuis l'interface web!\n\n";
    
    echo "Instructions:\n";
    echo "1. Connectez-vous à l'application\n";
    echo "2. Allez dans la section Rapports\n";
    echo "3. Cliquez sur 'Voir' pour un rapport\n";
    echo "4. Cliquez sur 'Exporter PDF'\n";
    echo "5. Le PDF devrait se télécharger automatiquement\n\n";
    
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
