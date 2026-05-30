<?php
/**
 * Test du fichier get_report.php
 */

require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'app/models/Report.php';

echo "=== TEST GET_REPORT.PHP ===\n";

// Simuler une session utilisateur
session_start();
$_SESSION['user_id'] = 1; // ID utilisateur de test

// Test avec un ID de rapport
$_GET['id'] = 1; // ID de rapport de test

echo "1. Session user_id: " . ($_SESSION['user_id'] ?? 'NON DÉFINI') . "\n";
echo "2. isLoggedIn(): " . (isLoggedIn() ? 'OUI' : 'NON') . "\n";
echo "3. Report ID: " . ($_GET['id'] ?? 'NON DÉFINI') . "\n";

try {
    // Connexion à la base de données
    $db = new Database();
    $pdo = $db->connect();
    echo "4. Connexion DB: OK\n";
    
    $reportModel = new Report($pdo);
    echo "5. Model Report: OK\n";
    
    // Vérifier s'il y a des rapports
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM reports");
    $count = $stmt->fetch()['count'];
    echo "6. Nombre de rapports en DB: $count\n";
    
    if ($count > 0) {
        // Récupérer le premier rapport
        $stmt = $pdo->query("SELECT * FROM reports LIMIT 1");
        $report = $stmt->fetch();
        echo "7. Premier rapport ID: " . $report['id'] . "\n";
        echo "8. Premier rapport titre: " . $report['title'] . "\n";
        echo "9. Premier rapport user_id: " . $report['user_id'] . "\n";
    } else {
        echo "7. Aucun rapport trouvé en base\n";
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}

echo "=== FIN TEST ===\n";
?>