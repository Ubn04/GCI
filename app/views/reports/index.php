<?php
/**
 * Vue de liste des rapports
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/modal-report.css">
    <style>
        /* Animations et styles pour la page rapports */
        .page-header {
            animation: fadeInUp 0.5s ease-out;
        }
        
        .search-panel {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            margin-bottom: 32px;
            animation: scaleIn 0.5s ease-out 0.1s backwards;
            transition: all 0.3s ease;
        }
        
        .search-panel:hover {
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 24px;
        }
        
        .report-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out backwards;
        }
        
        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        
        /* Variantes de couleurs pour les cards rapports */
        .report-card:nth-child(5n+1)::before {
            background: linear-gradient(90deg, #06b6d4, #22d3ee);
        }
        
        .report-card:nth-child(5n+2)::before {
            background: linear-gradient(90deg, #9333ea, #c084fc);
        }
        
        .report-card:nth-child(5n+3)::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        
        .report-card:nth-child(5n+4)::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        
        .report-card:nth-child(5n+5)::before {
            background: linear-gradient(90deg, #ec4899, #f472b6);
        }
        
        .report-card:nth-child(5n+1):hover {
            border-color: #a5f3fc;
            box-shadow: 0 20px 60px rgba(6, 182, 212, 0.15);
        }
        
        .report-card:nth-child(5n+2):hover {
            border-color: #e9d5ff;
            box-shadow: 0 20px 60px rgba(147, 51, 234, 0.15);
        }
        
        .report-card:nth-child(5n+3):hover {
            border-color: #a7f3d0;
            box-shadow: 0 20px 60px rgba(16, 185, 129, 0.15);
        }
        
        .report-card:nth-child(5n+4):hover {
            border-color: #fde68a;
            box-shadow: 0 20px 60px rgba(245, 158, 11, 0.15);
        }
        
        .report-card:nth-child(5n+5):hover {
            border-color: #fbcfe8;
            box-shadow: 0 20px 60px rgba(236, 72, 153, 0.15);
        }
        
        .report-card:nth-child(5n+1) .report-meta i {
            color: #06b6d4;
        }
        
        .report-card:nth-child(5n+2) .report-meta i {
            color: #9333ea;
        }
        
        .report-card:nth-child(5n+3) .report-meta i {
            color: #10b981;
        }
        
        .report-card:nth-child(5n+4) .report-meta i {
            color: #f59e0b;
        }
        
        .report-card:nth-child(5n+5) .report-meta i {
            color: #ec4899;
        }
        
        .report-card:nth-child(5n+1) .btn-primary {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            border: none;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }
        
        .report-card:nth-child(5n+1) .btn-primary:hover {
            background: linear-gradient(135deg, #0891b2, #0e7490);
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4);
        }
        
        .report-card:nth-child(5n+2) .btn-primary {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            border: none;
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.3);
        }
        
        .report-card:nth-child(5n+2) .btn-primary:hover {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            box-shadow: 0 6px 20px rgba(147, 51, 234, 0.4);
        }
        
        .report-card:nth-child(5n+3) .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .report-card:nth-child(5n+3) .btn-primary:hover {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        .report-card:nth-child(5n+4) .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        
        .report-card:nth-child(5n+4) .btn-primary:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }
        
        .report-card:nth-child(5n+5) .btn-primary {
            background: linear-gradient(135deg, #ec4899, #db2777);
            border: none;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
        }
        
        .report-card:nth-child(5n+5) .btn-primary:hover {
            background: linear-gradient(135deg, #db2777, #be185d);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
        }
        
        .report-card:hover::before {
            transform: scaleX(1);
        }
        
        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.15);
            border-color: #93c5fd;
        }
        
        .report-card h2 {
            font-size: 20px;
            margin: 0 0 16px;
            color: #1e3a8a;
            font-weight: 700;
        }
        
        .report-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 20px;
            padding: 16px 0;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .report-meta span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .report-meta i {
            color: #2563eb;
            font-size: 16px;
        }
        
        .report-preview {
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 20px;
            flex: 1;
        }
        
        .report-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .report-actions .btn {
            transition: all 0.3s ease;
        }
        
        .report-actions .btn:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 992px) {
            .reports-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include 'app/views/components/sidebar.php'; ?>

        <div class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-greeting">
                            <i class="fas fa-file-alt"></i>
                            <span>Gestion des rapports</span>
                        </div>
                        <div class="header-title">
                            <h1>Rapports</h1>
                            <p>Recherchez et consultez tous vos rapports générés</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="search-panel">
                <form method="GET" action="?action=reports">
                    <div class="input-group">
                        <button type="submit" class="btn btn-secondary text-body border border-200">
                            <i class="fas fa-search"></i>
                        </button>
                        <input type="text" name="q" class="form-control" placeholder="Rechercher un rapport ou un projet" value="<?php echo htmlspecialchars($search ?? $_GET['q'] ?? ''); ?>">
                    </div>
                </form>
            </div>

            <?php if (!isset($search)) { $search = trim($_GET['q'] ?? ''); } ?>
            <?php if (!empty($search)): ?>
                <div class="results-header">
                    <div>
                        <strong>Résultats pour "<?php echo htmlspecialchars($search); ?>"</strong>
                    </div>
                    <div class="results-count"><?php echo count($reports); ?> rapport<?php echo count($reports) > 1 ? 's' : ''; ?> trouvé<?php echo count($reports) > 1 ? 's' : ''; ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php
                $reportsByProject = [];
                foreach ($reports as $report) {
                    $reportsByProject[$report['project_name']][] = $report;
                }
            ?>

            <?php if (!empty($reportsByProject)): ?>
                <?php foreach ($reportsByProject as $projectName => $projectReports): ?>
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h2 class="h4 mb-1"><?php echo htmlspecialchars($projectName); ?></h2>
                                <p class="text-muted mb-0">Rapports générés pour ce projet</p>
                            </div>
                        </div>
                        <div class="reports-grid">
                            <?php foreach ($projectReports as $report): ?>
                                <div class="report-card">
                                    <div>
                                        <h2><?php echo htmlspecialchars($report['title']); ?></h2>
                                        <div class="report-meta">
                                            <span><i class="fas fa-calendar-day"></i> <?php echo date('d/m/Y', strtotime($report['created_at'])); ?></span>
                                            <span><i class="fas fa-clipboard-list"></i> <?php echo ucfirst(htmlspecialchars($report['report_type'])); ?></span>
                                        </div>
                                        <p class="report-preview"><?php echo nl2br(htmlspecialchars(substr($report['content'], 0, 110))); ?><?php echo strlen($report['content']) > 110 ? '...' : ''; ?></p>
                                    </div>
                                    <div class="report-actions">
                                        <button class="btn btn-sm btn-primary" onclick="showReportModal(<?php echo $report['id']; ?>)">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                        <a href="?action=reports/delete&id=<?php echo $report['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de supprimer ce rapport ?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <?php if (!empty($search)): ?>
                        <p><strong>Aucun rapport trouvé pour « <?php echo htmlspecialchars($search); ?> ».</strong></p>
                    <?php else: ?>
                        <p><strong>Aucun rapport généré.</strong> Commencez par générer un rapport depuis un projet.</p>
                    <?php endif; ?>
                    <a href="?action=projects" class="btn btn-primary mt-3">
                        <i class="fas fa-folder-open"></i> Aller aux projets
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════════════════ -->
    <!-- 📄 MODAL RAPPORT - DESIGN PROFESSIONNEL SENIOR DEV - CLEAN & SPACIEUX                 -->
    <!-- Architecture: Layout propre + Espacement généreux + Pas d'animations                  -->
    <!-- ═══════════════════════════════════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content pro-modal-wrapper">
                
                <!-- ═══ HEADER SECTION ═══ -->
                <div class="pro-modal-header">
                    <!-- Close Button -->
                    <button type="button" class="pro-close-btn" data-bs-dismiss="modal" aria-label="Fermer">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <!-- Top Bar avec Logo & Reference -->
                    <div class="pro-header-bar">
                        <div class="pro-brand-section">
                            <div class="pro-logo-container">
                                <img src="assets/images/logo.jpg" alt="GCI" class="pro-logo-img">
                            </div>
                            <div class="pro-brand-text">
                                <div class="pro-brand-subtitle">GÉNIE CIVIL INTELLIGENT</div>
                                <div class="pro-brand-title">GCI - ChantierAI</div>
                            </div>
                        </div>
                        
                        <div class="pro-reference-badge">
                            <div class="pro-ref-icon">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <div class="pro-ref-content">
                                <div class="pro-ref-label">RÉFÉRENCE</div>
                                <div class="pro-ref-number" id="reportReference">RAP-2026-0001</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Separator -->
                    <div class="pro-header-separator"></div>
                    
                    <!-- Title Section -->
                    <div class="pro-title-section">
                        <div class="pro-title-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div class="pro-title-content">
                            <h2 class="pro-modal-title" id="reportTitle">Chargement du rapport...</h2>
                            <p class="pro-modal-subtitle" id="reportSubtitle">Rapport de chantier généré par Intelligence Artificielle</p>
                        </div>
                    </div>
                </div>

                <!-- ═══ BODY SECTION ═══ -->
                <div class="pro-modal-body">
                    
                    <!-- Metadata Cards -->
                    <div class="pro-meta-container">
                        <div class="pro-meta-card pro-meta-type">
                            <div class="pro-meta-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="pro-meta-content">
                                <div class="pro-meta-label">TYPE DE RAPPORT</div>
                                <div class="pro-meta-value">
                                    <span class="pro-type-badge" id="reportType">Journalier</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pro-meta-card pro-meta-date">
                            <div class="pro-meta-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="pro-meta-content">
                                <div class="pro-meta-label">DATE DU RAPPORT</div>
                                <div class="pro-meta-value" id="reportDate">--/--/----</div>
                            </div>
                        </div>
                        
                        <div class="pro-meta-card pro-meta-generated">
                            <div class="pro-meta-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="pro-meta-content">
                                <div class="pro-meta-label">GÉNÉRÉ LE</div>
                                <div class="pro-meta-value" id="reportGenerated">--/--/---- --:--</div>
                            </div>
                        </div>
                        
                        <div class="pro-meta-card pro-meta-ai">
                            <div class="pro-meta-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div class="pro-meta-content">
                                <div class="pro-meta-label">SYSTÈME IA</div>
                                <div class="pro-meta-value">
                                    <span class="pro-ai-badge">
                                        <i class="fas fa-robot"></i>
                                        Gemini AI
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Area -->
                    <div class="pro-content-wrapper">
                        <div class="pro-content-paper">
                            <div id="reportContent" class="pro-content-display">
                                <!-- Loading State -->
                                <div class="pro-loading-state">
                                    <div class="pro-loading-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="pro-loading-text">Chargement du rapport en cours...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ═══ FOOTER SECTION ═══ -->
                <div class="pro-modal-footer">
                    <div class="pro-footer-info">
                        <div class="pro-ai-indicator">
                            <i class="fas fa-robot"></i>
                            <span>Généré automatiquement par <strong>Gemini AI</strong></span>
                        </div>
                    </div>
                    
                    <div class="pro-footer-actions">
                        <button type="button" class="pro-btn pro-btn-close" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            <span>Fermer</span>
                        </button>
                        <button type="button" class="pro-btn pro-btn-pdf" onclick="exportCurrentReportToPDF()">
                            <i class="fas fa-file-pdf"></i>
                            <span>Export PDF</span>
                        </button>
                        <button type="button" class="pro-btn pro-btn-word" onclick="exportCurrentReportToWord()">
                            <i class="fas fa-file-word"></i>
                            <span>Export Word</span>
                        </button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <script>
        let currentReportData = null;

        // Fonction pour afficher le modal avec le rapport
        function showReportModal(reportId) {
            console.log('Opening modal for report ID:', reportId);
            const modal = new bootstrap.Modal(document.getElementById('reportModal'));
            
            // Réinitialiser le contenu
            document.getElementById('reportTitle').textContent = 'Chargement...';
            document.getElementById('reportReference').textContent = 'RAP-0000-000';
            document.getElementById('reportType').textContent = 'Chargement...';
            document.getElementById('reportDate').textContent = '--/--/----';
            document.getElementById('reportGenerated').textContent = '--/--/---- --:--';
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
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        currentReportData = data.report;
                        displayReportInModal(data.report);
                    } else {
                        console.error('Error from server:', data.error);
                        document.getElementById('reportContent').innerHTML = 
                            `<div class="alert alert-danger m-4">Erreur: ${data.error || 'Erreur lors du chargement du rapport.'}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('reportContent').innerHTML = 
                        '<div class="alert alert-danger m-4">Erreur de connexion. Vérifiez la console pour plus de détails.</div>';
                });
        }

        // Fonction pour afficher le rapport dans le modal
        function displayReportInModal(report) {
            // Générer une référence unique
            const reportRef = `RAP-${new Date(report.created_at).getFullYear()}-${String(report.id).padStart(4, '0')}`;
            document.getElementById('reportReference').textContent = reportRef;
            
            // Mettre à jour le titre et sous-titre
            document.getElementById('reportTitle').textContent = report.title;
            document.getElementById('reportSubtitle').textContent = 
                `Projet: ${report.project_name || 'Non spécifié'}`;
            
            // Mettre à jour les métadonnées
            const reportTypeLabels = {
                'daily': 'Journalier',
                'monthly': 'Mensuel',
                'annual': 'Annuel'
            };
            
            document.getElementById('reportType').textContent = 
                reportTypeLabels[report.report_type] || report.report_type.charAt(0).toUpperCase() + report.report_type.slice(1);
            document.getElementById('reportDate').textContent = formatDate(report.report_date);
            document.getElementById('reportGenerated').textContent = formatDateTime(report.created_at);
            
            // Formater et afficher le contenu avec style professionnel
            let content = escapeHtml(report.content);
            
            // Remplacer les sauts de ligne par des <br>
            content = content.replace(/\n/g, '<br>');
            
            // Formater les titres avec style professionnel
            content = content.replace(/^# (.*$)/gm, '<h1>$1</h1>');
            content = content.replace(/^## (.*$)/gm, '<h2>$1</h2>');
            content = content.replace(/^### (.*$)/gm, '<h3>$1</h3>');
            
            // Formater le texte en gras et souligné
            content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            content = content.replace(/__(.*?)__/g, '<u>$1</u>');
            
            // Formater les listes
            content = content.replace(/^- (.*$)/gm, '<li>$1</li>');
            content = content.replace(/^• (.*$)/gm, '<li>$1</li>');
            content = content.replace(/^\* (.*$)/gm, '<li>$1</li>');
            
            // Envelopper les listes dans des balises <ul>
            content = content.replace(/(<li>.*?<\/li>(?:<br>)?)+/gs, function(match) {
                return '<ul>' + match.replace(/<br>/g, '') + '</ul>';
            });
            
            // Nettoyer les balises br en trop autour des titres et listes
            content = content.replace(/<br>\s*<h([1-6])>/g, '<h$1>');
            content = content.replace(/<\/h([1-6])>\s*<br>/g, '</h$1>');
            content = content.replace(/<br>\s*<ul>/g, '<ul>');
            content = content.replace(/<\/ul>\s*<br>/g, '</ul>');
            content = content.replace(/<br>\s*<\/li>/g, '</li>');
            
            // Remplacer les doubles <br> par des paragraphes
            content = content.replace(/(<br>\s*){2,}/g, '</p><p>');
            content = '<p>' + content + '</p>';
            
            // Nettoyer les paragraphes vides
            content = content.replace(/<p>\s*<\/p>/g, '');
            content = content.replace(/<p>\s*(<h[1-6]>)/g, '$1');
            content = content.replace(/(<\/h[1-6]>)\s*<\/p>/g, '$1');
            content = content.replace(/<p>\s*(<ul>)/g, '$1');
            content = content.replace(/(<\/ul>)\s*<\/p>/g, '$1');
            
            const contentElement = document.getElementById('reportContent');
            contentElement.innerHTML = content;
            contentElement.classList.add('loaded');
        }

        // Fonction pour exporter le rapport actuel en PDF
        function exportCurrentReportToPDF() {
            if (!currentReportData) return;
            
            // Utiliser l'endpoint serveur pour un meilleur rendu
            const url = `export_report.php?id=${currentReportData.id}&format=pdf`;
            window.open(url, '_blank');
        }

        // Fonction pour exporter le rapport actuel en Word
        function exportCurrentReportToWord() {
            if (!currentReportData) return;
            
            // Utiliser l'endpoint serveur pour un meilleur rendu
            const url = `export_report.php?id=${currentReportData.id}&format=word`;
            window.open(url, '_blank');
        }

        // Fonctions utilitaires
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function sanitizeFilename(filename) {
            return filename.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }

        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR') + ' à ' + date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
        }

        // Vérifier si on doit ouvrir le modal automatiquement
        <?php if (isset($_GET['show_modal'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            showReportModal(<?php echo intval($_GET['show_modal']); ?>);
        });
        <?php endif; ?>
    </script>
</body>
</html>
