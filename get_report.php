<?php
/**
 * API pour récupérer un rapport via AJAX
 */

require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'app/models/Report.php';

header('Content-Type: application/json');

// Debug: vérifier la session
error_log("get_report.php - Session ID: " . session_id());
error_log("get_report.php - User ID: " . ($_SESSION['user_id'] ?? 'NON DÉFINI'));
error_log("get_report.php - isLoggedIn: " . (isLoggedIn() ? 'OUI' : 'NON'));

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    error_log("get_report.php - Utilisateur non connecté");
    echo json_encode(['success' => false, 'error' => 'Non autorisé - Veuillez vous reconnecter']);
    exit;
}

// Vérifier que l'ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_log("get_report.php - ID invalide: " . ($_GET['id'] ?? 'NON DÉFINI'));
    echo json_encode(['success' => false, 'error' => 'ID de rapport invalide']);
    exit;
}

$reportId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

error_log("get_report.php - Recherche rapport ID: $reportId pour user: $userId");

try {
    // Connexion à la base de données
    $db = new Database();
    $pdo = $db->connect();
    $reportModel = new Report($pdo);
    
    // Récupérer le rapport
    $report = $reportModel->getById($reportId);
    
    if (!$report) {
        error_log("get_report.php - Rapport $reportId non trouvé");
        echo json_encode(['success' => false, 'error' => 'Rapport non trouvé']);
        exit;
    }
    
    error_log("get_report.php - Rapport trouvé: " . $report['title'] . " (user_id: " . $report['user_id'] . ")");
    
    // Vérifier que le rapport appartient à l'utilisateur connecté
    if ($report['user_id'] != $userId) {
        error_log("get_report.php - Accès refusé: rapport user_id=" . $report['user_id'] . " vs session user_id=$userId");
        echo json_encode(['success' => false, 'error' => 'Accès refusé']);
        exit;
    }
    
    error_log("get_report.php - Succès: retour du rapport");
    
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
    echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>