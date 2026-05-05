<?php
/**
 * Point d'entrée principal de l'application RapporAI
 * Routeur simple pour diriger les requêtes vers les contrôleurs appropriés
 */

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

    case 'reports/monthly':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->monthly();
        break;

    case 'reports/yearly':
        require_once CONTROLLERS_PATH . '/ReportControllerGemini.php';
        $controller = new ReportControllerGemini($pdo);
        $controller->yearly();
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
