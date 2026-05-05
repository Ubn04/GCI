<?php
/**
 * Démonstration du modal stylisé professionnel
 */

require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'app/models/Report.php';

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    echo "Vous devez être connecté pour voir cette démonstration.<br>";
    echo "<a href='?action=auth/login'>Se connecter</a>";
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $db = new Database();
    $pdo = $db->connect();
    $reportModel = new Report($pdo);
    
    // Récupérer les rapports de l'utilisateur
    $reports = $reportModel->getByUserId($userId);
    
} catch (Exception $e) {
    $reports = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Démonstration Modal Stylisé - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fbff 0%, #e0f2fe 100%);
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
        }
        
        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .demo-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .demo-title {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
        }
        
        .demo-subtitle {
            font-size: 18px;
            color: #64748b;
            margin-bottom: 32px;
        }
        
        .demo-card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.2);
            margin-bottom: 32px;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        
        .feature-item {
            background: #f8fafc;
            padding: 24px;
            border-radius: 16px;
            border-left: 4px solid #2563eb;
        }
        
        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-bottom: 16px;
        }
        
        .feature-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 8px;
        }
        
        .feature-desc {
            color: #64748b;
            line-height: 1.6;
        }
        
        .demo-button {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border: none;
            border-radius: 16px;
            padding: 16px 32px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
        
        .demo-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.4);
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
        }
        
        .demo-button:active {
            transform: translateY(0);
        }
        
        .reports-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .report-demo-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .report-demo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.1);
        }
        
        .report-demo-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 8px;
        }
        
        .report-demo-meta {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 16px;
        }
        
        .btn-demo {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border: none;
            border-radius: 12px;
            padding: 8px 16px;
            color: white;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-demo:hover {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <div class="demo-header">
            <h1 class="demo-title">Modal Professionnel</h1>
            <p class="demo-subtitle">Découvrez le nouveau design moderne et responsive pour l'affichage des rapports</p>
        </div>
        
        <div class="demo-card">
            <h2 class="h3 mb-4">🎨 Caractéristiques du nouveau design</h2>
            
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3 class="feature-title">Design Cohérent</h3>
                    <p class="feature-desc">Respecte parfaitement la palette de couleurs et le style du site avec des dégradés élégants.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Responsive</h3>
                    <p class="feature-desc">S'adapte automatiquement à tous les écrans, du mobile au desktop avec une expérience optimale.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-magic"></i>
                    </div>
                    <h3 class="feature-title">Animations Fluides</h3>
                    <p class="feature-desc">Transitions et animations subtiles pour une expérience utilisateur moderne et professionnelle.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <h3 class="feature-title">Export Intégré</h3>
                    <p class="feature-desc">Boutons d'export PDF et Word stylisés avec des couleurs distinctives et des effets hover.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="feature-title">Lisibilité Optimale</h3>
                    <p class="feature-desc">Typographie soignée, espacement parfait et hiérarchie visuelle claire pour une lecture confortable.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">Performance</h3>
                    <p class="feature-desc">Chargement AJAX rapide avec états de loading élégants et gestion d'erreurs professionnelle.</p>
                </div>
            </div>
        </div>
        
        <?php if (!empty($reports)): ?>
        <div class="demo-card">
            <h2 class="h3 mb-4">📋 Testez avec vos rapports</h2>
            <p class="mb-4">Cliquez sur un rapport pour voir le nouveau modal en action :</p>
            
            <div class="reports-list">
                <?php foreach (array_slice($reports, 0, 6) as $report): ?>
                <div class="report-demo-card">
                    <h4 class="report-demo-title"><?php echo htmlspecialchars($report['title']); ?></h4>
                    <div class="report-demo-meta">
                        <i class="fas fa-calendar me-1"></i>
                        <?php echo date('d/m/Y', strtotime($report['created_at'])); ?>
                        <span class="ms-3">
                            <i class="fas fa-tag me-1"></i>
                            <?php echo ucfirst($report['report_type']); ?>
                        </span>
                    </div>
                    <button class="btn btn-demo" onclick="showReportModal(<?php echo $report['id']; ?>)">
                        <i class="fas fa-eye me-2"></i>Voir le modal
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="demo-card text-center">
            <h2 class="h3 mb-3">📝 Aucun rapport disponible</h2>
            <p class="mb-4">Générez d'abord un rapport pour tester le modal stylisé.</p>
            <a href="?action=reports/select-project" class="demo-button">
                <i class="fas fa-plus me-2"></i>Générer un rapport
            </a>
        </div>
        <?php endif; ?>
        
        <div class="demo-card">
            <h2 class="h3 mb-4">🚀 Démonstration avec contenu factice</h2>
            <p class="mb-4">Testez le modal avec un rapport d'exemple pour voir tous les éléments de design :</p>
            <button class="demo-button" onclick="showDemoModal()">
                <i class="fas fa-play me-2"></i>Lancer la démonstration
            </button>
        </div>
        
        <div class="text-center mt-5">
            <a href="?action=reports" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Retour aux rapports
            </a>
        </div>
    </div>

    <!-- Inclure le modal stylisé -->
    <?php 
    // Inclure le modal depuis la vue des rapports
    $modalContent = file_get_contents('app/views/reports/index.php');
    preg_match('/<!-- Modal de visualisation du rapport.*?<\/style>/s', $modalContent, $matches);
    if (!empty($matches[0])) {
        echo $matches[0];
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentReportData = null;

        // Fonction pour afficher le modal avec le rapport
        function showReportModal(reportId) {
            const modal = new bootstrap.Modal(document.getElementById('reportModal'));
            
            // Réinitialiser le contenu
            document.getElementById('reportTitle').textContent = 'Chargement du rapport...';
            document.getElementById('reportSubtitle').textContent = 'Préparation de l\'affichage...';
            document.getElementById('reportMeta').innerHTML = `
                <div class="report-meta-loading">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <span class="ms-2">Chargement des informations...</span>
                </div>
            `;
            document.getElementById('reportContent').innerHTML = `
                <div class="report-loading-state">
                    <div class="report-loading-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="text-muted">Chargement du contenu du rapport...</p>
                </div>
            `;
            
            modal.show();
            
            // Charger le rapport via AJAX
            fetch(`get_report.php?id=${reportId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentReportData = data.report;
                        displayReportInModal(data.report);
                    } else {
                        document.getElementById('reportContent').innerHTML = 
                            '<div class="alert alert-danger m-4">Erreur lors du chargement du rapport.</div>';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('reportContent').innerHTML = 
                        '<div class="alert alert-danger m-4">Erreur de connexion.</div>';
                });
        }

        // Fonction pour afficher le modal de démonstration
        function showDemoModal() {
            const modal = new bootstrap.Modal(document.getElementById('reportModal'));
            
            // Données factices pour la démonstration
            const demoReport = {
                id: 'demo',
                title: 'Rapport Journalier - Construction Immeuble Résidentiel',
                content: `# RAPPORT DE CHANTIER - 04/05/2026

## 1. RÉSUMÉ EXÉCUTIF

Journée productive avec l'avancement significatif des travaux de gros œuvre. Les conditions météorologiques favorables ont permis de maintenir le rythme de construction prévu.

## 2. CONDITIONS GÉNÉRALES

• **Conditions météorologiques :** Ensoleillé
• **Température :** 22°C
• **Vent :** Faible (< 10 km/h)
• **Visibilité :** Excellente

## 3. RESSOURCES MOBILISÉES

### Équipements sur le chantier :
- **Grue à tour :** 1 présent, 1 en marche, 0 immobilisé, 0 en panne
- **Bétonnière :** 2 présentes, 2 en marche, 0 immobilisée, 0 en panne
- **Pelleteuse :** 1 présente, 1 en marche, 0 immobilisée, 0 en panne

### Personnel mobilisé :
- **Chef de chantier :** 1 personne
- **Ouvriers qualifiés :** 8 personnes
- **Manœuvres :** 4 personnes
- **Conducteur d'engins :** 2 personnes
**Total personnel :** 15 personnes

### Matériaux utilisés :
- **Béton C25/30 :** 15 m³
- **Acier HA :** 2 tonnes
- **Coffrages :** 50 m²

## 4. ACTIVITÉS RÉALISÉES

- Coulage des poteaux du 3ème étage (secteur A)
- Mise en place des armatures pour les poutres
- Préparation des coffrages pour les dalles
- Contrôle qualité des bétons coulés la veille

## 5. PROBLÈMES RENCONTRÉS

Aucun problème majeur signalé. Léger retard dans la livraison des armatures compensé par une réorganisation des équipes.

## 6. SOLUTIONS APPORTÉES

Coordination renforcée avec les fournisseurs pour éviter les retards futurs. Mise en place d'un stock tampon pour les matériaux critiques.

## 7. AVANCEMENT DU PROJET

- **Avancement global :** 65%
- **Gros œuvre :** 70%
- **Planning :** Conforme aux prévisions
- **Prochaine étape :** Coulage des dalles du 3ème étage

## 8. OBSERVATIONS ET RECOMMANDATIONS

- Maintenir la cadence actuelle pour respecter les délais
- Surveiller l'approvisionnement en matériaux
- Prévoir la maintenance préventive des équipements
- Continuer les formations sécurité hebdomadaires

---
*Rapport généré par ChantierAI - Gemini AI*`,
                report_type: 'daily',
                report_date: '2026-05-04',
                created_at: new Date().toISOString()
            };
            
            currentReportData = demoReport;
            
            // Afficher le modal avec les données de démonstration
            modal.show();
            
            // Simuler un petit délai de chargement pour l'effet
            setTimeout(() => {
                displayReportInModal(demoReport);
            }, 800);
        }

        // Fonction pour afficher le rapport dans le modal (copiée et adaptée)
        function displayReportInModal(report) {
            // Mettre à jour le titre et sous-titre
            document.getElementById('reportTitle').textContent = report.title;
            document.getElementById('reportSubtitle').textContent = 
                `Rapport ${report.report_type} • Généré le ${formatDateTime(report.created_at)}`;
            
            // Masquer le loading des métadonnées et afficher le contenu
            const metaCard = document.getElementById('reportMeta');
            metaCard.innerHTML = `
                <div class="report-meta-content loaded">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="report-meta-item">
                                <i class="fas fa-tag"></i>
                                <div>
                                    <strong>Type:</strong>
                                    <span class="badge ms-2">${report.report_type.charAt(0).toUpperCase() + report.report_type.slice(1)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="report-meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <strong>Date du rapport:</strong>
                                    <span class="ms-2">${formatDate(report.report_date)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="report-meta-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Généré le:</strong>
                                    <span class="ms-2">${formatDateTime(report.created_at)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Formater et afficher le contenu
            let content = escapeHtml(report.content);
            content = content.replace(/\n/g, '<br>');
            content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            content = content.replace(/__(.*?)__/g, '<u>$1</u>');
            content = content.replace(/^# (.*$)/gm, '<h1>$1</h1>');
            content = content.replace(/^## (.*$)/gm, '<h2>$1</h2>');
            content = content.replace(/^### (.*$)/gm, '<h3>$1</h3>');
            content = content.replace(/^- (.*$)/gm, '<li>$1</li>');
            
            // Améliorer le formatage des listes
            content = content.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
            
            // Nettoyer les balises br en trop
            content = content.replace(/<br>\s*<h([1-6])>/g, '<h$1>');
            content = content.replace(/<\/h([1-6])>\s*<br>/g, '</h$1>');
            content = content.replace(/<br>\s*<ul>/g, '<ul>');
            content = content.replace(/<\/ul>\s*<br>/g, '</ul>');
            
            const contentElement = document.getElementById('reportContent');
            contentElement.innerHTML = content;
            contentElement.classList.add('loaded');
        }

        // Fonctions d'export
        function exportCurrentReportToPDF() {
            if (!currentReportData) return;
            
            if (currentReportData.id === 'demo') {
                alert('Ceci est une démonstration. L\'export n\'est pas disponible pour les rapports factices.');
                return;
            }
            
            const url = `export_report.php?id=${currentReportData.id}&format=pdf`;
            window.open(url, '_blank');
        }

        function exportCurrentReportToWord() {
            if (!currentReportData) return;
            
            if (currentReportData.id === 'demo') {
                alert('Ceci est une démonstration. L\'export n\'est pas disponible pour les rapports factices.');
                return;
            }
            
            const url = `export_report.php?id=${currentReportData.id}&format=word`;
            window.open(url, '_blank');
        }

        // Fonctions utilitaires
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }

        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR') + ' à ' + date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
        }
    </script>
</body>
</html>