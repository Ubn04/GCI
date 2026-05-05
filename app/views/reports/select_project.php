<?php
/**
 * Vue de sélection de projet pour la génération de rapport
 */
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélectionner un projet - ChantierAI</title>
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
            margin-bottom: 12px;
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
            align-items: flex-start;
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
            margin: 8px 0 0;
            color: #1e40af;
            font-size: 15px;
        }
        .card {
            border-radius: 24px;
            border: 1px solid #bfdbfe;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }
        .btn-primary, .btn-success, .btn-secondary {
            border: none;
        }
        .btn-primary {
            background: #2563eb;
        }
        .btn-primary:hover {
            background: #1e40af;
        }
        .btn-success {
            background: #2563eb;
            color: white;
        }
        .btn-success:hover {
            background: #1e40af;
        }
        .btn-secondary {
            background: #1e40af;
            color: white;
        }
        .btn-secondary:hover {
            background: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include 'app/views/components/sidebar.php'; ?>
        <div class="main-content">
            <header class="page-header">
                <div>
                    <h1 class="page-title">Générer un rapport</h1>
                    <p class="page-subtitle">Sélectionnez un projet pour commencer la création de votre rapport.</p>
                </div>
                <a href="?action=projects/create" class="btn btn-primary rounded-4 py-3 px-4">
                    <i class="fas fa-plus"></i> Créer un projet
                </a>
            </header>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success mb-4"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if (empty($projects)): ?>
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle"></i> Aucun projet trouvé. Veuillez créer un projet avant de générer un rapport.
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php foreach ($projects as $project): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($project['name']); ?></h5>
                                            <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($project['location']); ?></p>
                                            <p class="text-muted mb-0"><i class="fas fa-calendar-alt me-2"></i>Début : <?php echo date('d/m/Y', strtotime($project['start_date'])); ?></p>
                                        </div>
                                        <span class="badge bg-primary align-self-start">Projet</span>
                                    </div>
                                    <p class="card-text text-secondary">Utilisez ce projet pour rédiger un rapport détaillé avec les informations chantier.</p>
                                </div>
                                <div class="card-footer bg-white border-0 pt-0">
                                    <a href="?action=reports/project-info&project_id=<?php echo $project['id']; ?>" class="btn btn-success w-100 rounded-4 py-2">
                                        <i class="fas fa-folder-open me-2"></i> Ouvrir
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
