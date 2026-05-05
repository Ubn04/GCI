<?php
/**
 * API pour récupérer un rapport via AJAX
 */

require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'app/models/Report.php';

header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Vérifier que l'ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de rapport invalide']);
    exit;
}

$reportId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

try {
    // Connexion à la base de données
    $db = new Database();
    $pdo = $db->connect();
    $reportModel = new Report($pdo);
    
    // Récupérer le rapport
    $report = $reportModel->getById($reportId);
    
    if (!$report) {
        echo json_encode(['success' => false, 'error' => 'Rapport non trouvé']);
        exit;
    }
    
    // Vérifier que le rapport appartient à l'utilisateur connecté
    if ($report['user_id'] != $userId) {
        echo json_encode(['success' => false, 'error' => 'Accès refusé']);
        exit;
    }
    
    // Retourner le rapport
    echo json_encode([
        'success' => true,
        'report' => [
            'id' => $report['id'],
            'title' => $report['title'],
            'content' => $report['content'],
            'report_type' => $report['report_type'],
            'report_date' => $report['report_date'],
            'created_at' => $report['created_at'],
            'project_name' => $report['project_name'] ?? 'Non spécifié'
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_report.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
}
?>