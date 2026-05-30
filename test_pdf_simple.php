<?php
/**
 * Test de génération PDF simple
 */

require_once 'config/config.php';
require_once 'app/services/PDFGenerator.php';

// Test simple
$pdfGenerator = new PDFGenerator();

$projectData = [
    'name' => 'Test Projet',
    'location' => 'Cotonou, Bénin',
    'owner' => 'Ministère des Infrastructures',
    'control' => 'Bureau d\'études',
    'company' => 'Genie Concept Innovation',
    'type' => 'Génie Civil'
];

$equipments = [
    ['designation' => 'Grue mobile 50T', 'present' => 'Oui', 'marche' => 'Oui', 'immob' => 'Non', 'panne' => 'Non'],
];

$personnels = [
    ['profile' => 'Ingénieur', 'nbr' => '2'],
];

$materials = [
    ['designation' => 'Béton C25/30', 'unite' => 'm3', 'quantite' => '15'],
];

try {
    $pdfContent = $pdfGenerator->generateStructuredReport(
        $projectData,
        $equipments,
        $personnels,
        $materials,
        'Ensoleillé',
        ''
    );
    
    if (empty($pdfContent)) {
        echo "❌ ERREUR: Le contenu PDF est vide\n";
        echo "Vérifiez que DomPDF est correctement configuré\n";
        exit;
    }
    
    // Nettoyer les buffers
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Envoyer les headers
    header('Content-Type: application/pdf', true);
    header('Content-Disposition: inline; filename="test_pdf.pdf"', true);
    header('Cache-Control: private, no-cache, no-store, must-revalidate', true);
    header('Expires: 0', true);
    
    echo $pdfContent;
    exit;
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString();
    exit;
}
?>
