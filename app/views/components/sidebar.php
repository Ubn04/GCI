<?php
/**
 * Composant Sidebar moderne et fixe
 */
$current_page = $_GET['action'] ?? 'dashboard';
$user = $_SESSION['user'] ?? null;
?>
<aside class="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="assets/images/logo.jpg" alt="ChantierAI">
        </div>
        <div class="brand-text">
            <h1>ChantierAI</h1>
            <p>Rapports intelligents</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-section">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="?action=dashboard" class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?action=projects" class="<?php echo strpos($current_page, 'projects') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-folder-open"></i>
                        <span>Projets</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Rapports</div>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="?action=reports/select-project" class="<?php echo $current_page === 'reports/select-project' ? 'active' : ''; ?>">
                        <i class="fas fa-plus-circle"></i>
                        <span>Générer un rapport</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?action=reports" class="<?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Voir les rapports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?action=reports/monthly" class="<?php echo $current_page === 'reports/monthly' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Rapports mensuels</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?action=reports/yearly" class="<?php echo $current_page === 'reports/yearly' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i>
                        <span>Rapports annuels</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Compte</div>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="?action=auth/profile" class="<?php echo $current_page === 'auth/profile' ? 'active' : ''; ?>">
                        <i class="fas fa-user-circle"></i>
                        <span>Mon profil</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        <a href="?action=auth/logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>