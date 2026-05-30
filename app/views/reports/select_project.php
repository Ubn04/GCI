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
    <link rel="stylesheet" href="assets/css/project-modal.css">
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

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .project-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .project-card::before {
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

        .project-card:nth-child(4n+1)::before {
            background: linear-gradient(90deg, #9333ea, #c084fc);
        }

        .project-card:nth-child(4n+2)::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .project-card:nth-child(4n+3)::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .project-card:nth-child(4n+4)::before {
            background: linear-gradient(90deg, #ec4899, #f472b6);
        }

        .project-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.15);
            border-color: #93c5fd;
        }

        .project-card:hover::before {
            transform: scaleX(1);
        }

        .project-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .project-card-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #1e3a8a;
            line-height: 1.3;
        }

        .project-card-body {
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 20px;
            min-height: 60px;
        }

        .project-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 20px;
            padding: 16px 0;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .project-meta span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .project-meta i {
            color: #2563eb;
            font-size: 16px;
        }

        .project-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px 0;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 14px;
            border: 2px solid #bfdbfe;
            color: #1e3a8a;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            gap: 8px;
        }

        .project-cta:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-color: #2563eb;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        @media (max-width: 992px) {
            .projects-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include 'app/views/components/sidebar.php'; ?>
        <div class="main-content">
            <header class="page-header">
                <div>
                    <h1 class="page-title">Sélectionner un projet</h1>
                    <p class="page-subtitle">Choisissez un projet pour commencer la génération de votre rapport.</p>
                </div>
                <button type="button" class="btn btn-primary rounded-4 py-3 px-4" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                    <i class="fas fa-plus"></i> Créer un projet
                </button>
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
                <div class="projects-grid">
                    <?php foreach ($projects as $project): ?>
                        <div class="project-card">
                            <div class="project-card-header">
                                <h2 class="project-card-title"><?php echo htmlspecialchars($project['name']); ?></h2>
                            </div>
                            <div class="project-card-body">
                                <?php echo nl2br(htmlspecialchars(substr($project['description'] ?? '', 0, 140))); ?><?php echo strlen($project['description'] ?? '') > 140 ? '...' : ''; ?>
                            </div>
                            <div class="project-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['location']); ?></span>
                                <span><i class="fas fa-calendar-alt"></i> Début : <?php echo date('d/m/Y', strtotime($project['start_date'])); ?></span>
                            </div>
                            <a href="?action=reports/project-info&project_id=<?php echo $project['id']; ?>" class="project-cta">
                                <i class="fas fa-folder-open"></i> Sélectionner
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createProjectModalLabel">
                                <i class="fas fa-plus-circle me-2"></i>
                                Enregistrer un projet
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="?action=projects/handle-create">
                                <input type="hidden" name="return_to" value="reports/select-project">

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom du projet</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ex: Construction du pont de la riviere" required>
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Localisation</label>
                                    <input type="text" class="form-control" id="location" name="location" placeholder="Ex: Kinshasa, RDC" required>
                                </div>

                                <div class="mb-3">
                                    <label for="project_type" class="form-label">Type de projet</label>
                                    <select class="form-select" id="project_type" name="project_type" required>
                                        <option value="batiment">Batiment</option>
                                        <option value="route">Route</option>
                                        <option value="pont">Pont</option>
                                        <option value="château d'eau">Chateau d'eau</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="maitre_ouvrage" class="form-label">Maitre d'ouvrage</label>
                                    <input type="text" class="form-control" id="maitre_ouvrage" name="maitre_ouvrage" placeholder="Ex: Ministere des Infrastructures">
                                </div>

                                <div class="mb-3">
                                    <label for="missions_controle" class="form-label">Missions de controle</label>
                                    <textarea class="form-control" id="missions_controle" name="missions_controle" rows="3" placeholder="Decrivez les missions de controle..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Decrivez les objectifs et caracteristiques du projet..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="start_date" class="form-label">Date d'enregistrement</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Enregistrer le projet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
