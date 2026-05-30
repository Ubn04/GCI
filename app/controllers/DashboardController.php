<?php
/**
 * Contrôleur du dashboard - Affichage des statistiques et données récentes
 */

require_once ROOT_PATH . '/app/models/Project.php';
require_once ROOT_PATH . '/app/models/Report.php';

class DashboardController {
    private $projectModel;
    private $reportModel;

    public function __construct($pdo) {
        $this->projectModel = new Project($pdo);
        $this->reportModel = new Report($pdo);
    }

    /**
     * Afficher le dashboard
     */
    public function index() {
        requireLogin();
        
        $user_id = $_SESSION['user_id'];
        $user = getCurrentUser();
        
        // Statistiques
        $totalProjects = $this->projectModel->countByUserId($user_id);
        $totalReports = $this->reportModel->countByUserId($user_id);
        $dailyReports = $this->reportModel->countByTypeByUserId($user_id, 'daily');
        $monthlyReports = $this->reportModel->countByTypeByUserId($user_id, 'monthly');
        $yearlyReports = $this->reportModel->countByTypeByUserId($user_id, 'annual');
        
        // Données récentes
        $recentProjects = $this->projectModel->getByUserId($user_id);
        $recentReports = $this->reportModel->getRecent($user_id, 5);
        
        require VIEWS_PATH . '/dashboard/index.php';
    }
}
?>
