<?php
/**
 * Vue des rapports annuels
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports annuels - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <style>
        body {
            background: #f8fbff;
            min-height: 100vh;
            color: #1e3a8a;
        }
        .app-shell {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: #1e3a8a;
            color: #dbeafe;
            display: flex;
            flex-direction: column;
            padding: 28px 20px;
        }
        .sidebar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 36px;
        }
        .sidebar .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .sidebar .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        .sidebar .brand-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: white;
        }
        .sidebar .brand-subtitle {
            margin: 2px 0 0;
            font-size: 12px;
            color: #dbeafe;
        }
        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0 0 24px;
            flex: 1;
        }
        .nav-item {
            margin-bottom: 10px;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #dbeafe;
            padding: 12px 14px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
        }
        .nav-link.active,
        .nav-link:hover {
            background: rgba(37, 99, 235, 0.16);
            color: white;
        }
        .logout-link {
            margin-top: auto;
        }
        .logout-link a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #dbeafe;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(37, 99, 235, 0.12);
        }
        .content {
            padding: 32px;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 28px;
        }
        .page-title {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
        }
        .page-subtitle {
            margin: 6px 0 0;
            color: #1e40af;
            font-size: 15px;
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
        
        /* Variantes de couleurs pour les années */
        .report-card:nth-child(3n+1)::before {
            background: linear-gradient(90deg, #9333ea, #c084fc);
        }
        
        .report-card:nth-child(3n+2)::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        
        .report-card:nth-child(3n+3)::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        
        .report-card:hover::before {
            transform: scaleX(1);
        }
        
        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.15);
        }
        
        .report-card:nth-child(3n+1):hover {
            border-color: #e9d5ff;
            box-shadow: 0 20px 60px rgba(147, 51, 234, 0.15);
        }
        
        .report-card:nth-child(3n+2):hover {
            border-color: #a7f3d0;
            box-shadow: 0 20px 60px rgba(16, 185, 129, 0.15);
        }
        
        .report-card:nth-child(3n+3):hover {
            border-color: #fde68a;
            box-shadow: 0 20px 60px rgba(245, 158, 11, 0.15);
        }
        
        .report-card:nth-child(3n+1) .report-meta i {
            color: #9333ea;
        }
        
        .report-card:nth-child(3n+2) .report-meta i {
            color: #10b981;
        }
        
        .report-card:nth-child(3n+3) .report-meta i {
            color: #f59e0b;
        }
        
        .report-card:nth-child(3n+1) .btn-primary {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            border: none;
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.3);
        }
        
        .report-card:nth-child(3n+1) .btn-primary:hover {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            box-shadow: 0 6px 20px rgba(147, 51, 234, 0.4);
        }
        
        .report-card:nth-child(3n+2) .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .report-card:nth-child(3n+2) .btn-primary:hover {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        .report-card:nth-child(3n+3) .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        
        .report-card:nth-child(3n+3) .btn-primary:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }
        .report-card h2 {
            font-size: 18px;
            margin: 0 0 12px;
            color: #1e3a8a;
        }
        .report-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 18px;
        }
        .report-meta span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .report-meta i {
            color: #2563eb;
        }
        .report-summary {
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 22px;
            flex: 1;
        }
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
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
                            <i class="fas fa-calendar-check"></i>
                            <span>Rapports annuels</span>
                        </div>
                        <div class="header-title">
                            <h1>Rapports annuels</h1>
                            <p>Vue d'ensemble de toutes les années où vous avez généré des rapports</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($yearlyReports)): ?>
                <div class="reports-grid">
                    <?php foreach ($yearlyReports as $report): ?>
                        <div class="report-card">
                            <div>
                                <h2>Année <?php echo htmlspecialchars($report['year']); ?></h2>
                                <div class="report-meta">
                                    <span><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($report['count']); ?> rapport<?php echo $report['count'] > 1 ? 's' : ''; ?></span>
                                    <span><i class="fas fa-clock"></i> Dernière génération le <?php echo date('d/m/Y', strtotime($report['last_generated_at'])); ?></span>
                                </div>
                                <p class="report-summary">Cette carte résume le nombre total de rapports générés au cours de l'année sélectionnée.</p>
                            </div>
                            <div>
                                <a href="?action=reports" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Voir tous
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Aucun rapport annuel trouvé pour le moment. Commencez par générer un rapport depuis un projet.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
