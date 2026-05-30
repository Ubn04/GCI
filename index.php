<?php
/**
 * Point d'entrée principal de l'application RapporAI
 * Routeur simple pour diriger les requêtes vers les contrôleurs appropriés
 */

// Définir un gestionnaire d'erreurs global pour capturer les erreurs PHP
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Si c'est une requête AJAX, retourner du JSON
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '?action=') !== false) {
        header('Content-Type: application/json; charset=utf-8', true, 500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur PHP : ' . htmlspecialchars($errstr),
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    return false;
});

// Définir un gestionnaire pour les exceptions non capturées
set_exception_handler(function($exception) {
    // Si c'est une requête AJAX, retourner du JSON
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '?action=') !== false) {
        header('Content-Type: application/json; charset=utf-8', true, 500);
        echo json_encode([
            'success' => false,
            'message' => 'Exception : ' . htmlspecialchars($exception->getMessage()),
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    throw $exception;
});

require_once 'config/config.php';
require_once 'config/Database.php';

// Initialiser la connexion à la base de données
$db = new Database();
$pdo = $db->connect();

// Récupérer l'action demandée
$action = $_GET['action'] ?? 'home';
$id = $_GET['id'] ?? null;
$project_id = $_GET['project_id'] ?? null;

// Routeur simple
switch ($action) {
    // Routes d'authentification
    case 'auth/login':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->login();
        break;

    case 'auth/handle-login':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->handleLogin();
        break;

    case 'auth/register':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->register();
        break;

    case 'auth/handle-register':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->handleRegister();
        break;

    case 'auth/profile':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->profile();
        break;

    case 'auth/update-profile':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->handleUpdateProfile();
        break;

    case 'auth/delete-account':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->deleteAccount();
        break;

    case 'auth/verify':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->verifyEmail();
        break;

    case 'auth/logout':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->logout();
        break;

    // Route page d'accueil publique
    case 'home':
        require_once VIEWS_PATH . '/home/index.php';
        break;

    // Routes du dashboard
    case 'dashboard':
        require_once CONTROLLERS_PATH . '/DashboardController.php';
        $controller = new DashboardController($pdo);
        $controller->index();
        break;

    // Routes des projets
    case 'projects':
        require_once CONTROLLERS_PATH . '/ProjectController.php';
        $controller = new ProjectController($pdo);
        $controller->index();
        break;

    case 'projects/create':
        require_once CONTROLLERS_PATH . '/ProjectController.php';
        $controller = new ProjectController($pdo);
        $controller->create();
        break;

    case 'projects/handle-create':
        require_once CONTROLLERS_PATH . '/ProjectController.php';
        $controller = new ProjectController($pdo);
        $controller->handleCreate();
        break;

    case 'projects/show':
        require_once CONTROLLERS_PATH . '/ProjectController.php';
        $controller = new ProjectController($pdo);
        $controller->show($id);
        break;

    case 'projects/open':
        require_once CONTROLLERS_PATH . '/ProjectController.php';
        $controller = new ProjectController($pdo);
        $controller->open($id);
        break;

    case 'projects/edit':
        require_once CONTROLLERS_PATH . '/ProjectController.php';
        $controller = new ProjectController($pdo);
        $controller->edit($id);
        break;

    case 'projects/handle-update':
        require_once CONTROLLERS_PATH . '/ProjectController.php';
        $controller = new ProjectController($pdo);
        $controller->handleUpdate($id);
        break;

    case 'projects/delete':
        require_once CONTROLLERS_PATH . '/ProjectController.php';
        $controller = new ProjectController($pdo);
        $controller->delete($id);
        break;

    // Routes des données terrain
    case 'sitedata/add':
        require_once CONTROLLERS_PATH . '/SiteDataController.php';
        $controller = new SiteDataController($pdo);
        $controller->add($project_id);
        break;

    case 'sitedata/delete':
        require_once CONTROLLERS_PATH . '/SiteDataController.php';
        $controller = new SiteDataController($pdo);
        $controller->delete($id, $project_id);
        break;

    // Routes des rapports (utilise le contrôleur Gemini optimisé)
    case 'reports':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->index();
        break;

    case 'reports/select-project':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->selectProject();
        break;

    case 'reports/project-info':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->projectInfo($project_id);
        break;

    case 'reports/save-draft':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->saveDraft($project_id);
        break;

    case 'reports/generate':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->generate($project_id);
        break;

    case 'reports/handle-generate':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->handleGenerate($project_id);
        break;

    case 'reports/generate-ajax':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->generateReportAjax($project_id);
        break;

    case 'reports/export-pdf':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->exportReportAjaxPDF();
        break;

    case 'reports/export-word':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->exportReportAjaxWord();
        break;

    case 'reports/monthly':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->monthly();
        break;

    case 'reports/generate-monthly':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->generateMonthly();
        break;

    case 'reports/yearly':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->yearly();
        break;

    case 'reports/generate-yearly':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->generateYearly();
        break;

    case 'reports/show':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->show($id);
        break;

    case 'reports/delete':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->delete($id);
        break;

    case 'reports/export-draft-pdf':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->exportDraftPDF($project_id);
        break;

    case 'reports/export-pdf':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->exportReportPDF($id);
        break;

    // Routes Chat IA avec Gemini (multimodal : texte + image)
    case 'chat':
    case 'chat/index':
        requireLogin();
        if (!$project_id) {
            redirect('dashboard');
        }
        
        // Vérifier que l'utilisateur a accès au projet
        $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$project_id, $_SESSION['user_id']]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$project) {
            redirect('dashboard');
        }
        
        include VIEWS_PATH . '/chat/index.php';
        break;

    case 'chat/send-message':
        require_once CONTROLLERS_PATH . '/ChatAIController.php';
        try {
            $controller = new ChatAIController($pdo);
            $controller->sendMessage();
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage(),
                'data' => null
            ]);
        }
        break;

    case 'chat/history':
        require_once CONTROLLERS_PATH . '/ChatAIController.php';
        try {
            $controller = new ChatAIController($pdo);
            $controller->getHistory();
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage(),
                'data' => null
            ]);
        }
        break;

    // Route par défaut
    default:
        if (isLoggedIn()) {
            redirect('dashboard');
        } else {
            redirect('home');
        }
        break;
}
?>
