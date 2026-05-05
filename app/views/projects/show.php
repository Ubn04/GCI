<?php
/**
 * Vue de détail de projet avec collecte de données
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['name']); ?> - ChantierAI</title>
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
            color: white;
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
        .badge-info {
            background: #93c5fd;
            color: #0c4a6e;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include 'app/views/components/sidebar.php'; ?>
        <div class="main-content">
            <header class="page-header">
                <div>
                    <h1 class="page-title">Projet : <?php echo htmlspecialchars($project['name']); ?></h1>
                    <p class="page-subtitle">Gérez vos données de chantier et générez un rapport conforme au design de ChantierAI.</p>
                </div>
            </header>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row gap-4">
                <div class="col-12">
                    <div class="card p-4 mb-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-start">
                            <div>
                                <h2 class="h4 mb-2"><i class="fas fa-building text-primary"></i> <?php echo htmlspecialchars($project['name']); ?></h2>
                                <p class="text-muted mb-1"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['location']); ?></p>
                                <p class="text-muted mb-0"><i class="fas fa-calendar"></i> Début : <?php echo date('d/m/Y', strtotime($project['start_date'])); ?></p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="?action=projects/edit&id=<?php echo $project['id']; ?>" class="btn btn-secondary rounded-4">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <a href="?action=reports/project-info&project_id=<?php echo $project['id']; ?>" class="btn btn-success rounded-4">
                                    <i class="fas fa-file-pdf"></i> Générer rapport
                                </a>
                                <a href="?action=projects" class="btn btn-primary rounded-4">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card p-4 mb-4">
                        <h3 class="h5 mb-3">Ajouter une donnée</h3>
                        <form method="POST" action="?action=sitedata/add&project_id=<?php echo $project['id']; ?>" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="data_type" class="form-label">Type de donnée</label>
                                <select class="form-select rounded-4" id="data_type" name="data_type" onchange="toggleFileInput()">
                                    <option value="text">Texte</option>
                                    <option value="image">Photo</option>
                                    <option value="file">Fichier</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Contenu</label>
                                <textarea class="form-control rounded-4" id="content" name="content" rows="4" placeholder="Décrivez vos observations..." required></textarea>
                            </div>
                            <div class="mb-3" id="fileInputDiv" style="display: none;">
                                <label for="file" class="form-label">Fichier</label>
                                <input type="file" class="form-control rounded-4" id="file" name="file">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-4 py-3">
                                <i class="fas fa-save"></i> Ajouter
                            </button>
                        </form>
                    </div>

                    <div class="card p-4">
                        <h3 class="h5 mb-3">Résumé du chantier</h3>
                        <p class="text-muted mb-2"><strong>Données collectées :</strong> <?php echo count($siteData); ?></p>
                        <p class="text-muted mb-2"><strong>Dernière entrée :</strong> <?php echo !empty($siteData) ? date('d/m/Y H:i', strtotime($siteData[0]['created_at'])) : 'Aucune entrée'; ?></p>
                        <p class="text-muted mb-3"><i class="fas fa-robot"></i> Ces données seront envoyées à l’IA pour produire un rapport complet.</p>
                        <a href="?action=reports/project-info&project_id=<?php echo $project['id']; ?>" class="btn btn-success w-100 rounded-4 py-3 mt-3">
                            <i class="fas fa-file-pdf"></i> Générer le rapport IA
                        </a>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
                            <div>
                                <h3 class="h5 mb-1"><i class="fas fa-list"></i> Données collectées</h3>
                                <p class="text-muted mb-0"><?php echo count($siteData); ?> éléments</p>
                            </div>
                        </div>
                        <?php if (!empty($siteData)): ?>
                            <?php foreach ($siteData as $data): ?>
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between gap-3 align-items-start">
                                            <div style="flex: 1;">
                                                <span class="badge badge-info mb-2 py-2 px-3 rounded-pill">
                                                    <i class="fas fa-tag"></i> <?php echo ucfirst($data['data_type']); ?>
                                                </span>
                                                <p class="text-secondary mb-2"><?php echo htmlspecialchars($data['content']); ?></p>
                                                <?php if (!empty($data['file_url'])): ?>
                                                    <a href="<?php echo htmlspecialchars($data['file_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-4">
                                                        <i class="fas fa-download"></i> Voir fichier
                                                    </a>
                                                <?php endif; ?>
                                                <small class="text-muted d-block mt-3">
                                                    <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?>
                                                </small>
                                            </div>
                                            <a href="?action=sitedata/delete&id=<?php echo $data['id']; ?>&project_id=<?php echo $project['id']; ?>" class="btn btn-sm btn-danger rounded-4" onclick="return confirm('Êtes-vous sûr ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> Aucune donnée collectée pour le moment. Ajoutez une observation ou un fichier, puis générez un rapport.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFileInput() {
            const dataType = document.getElementById('data_type').value;
            const fileInputDiv = document.getElementById('fileInputDiv');
            if (dataType !== 'text') {
                fileInputDiv.style.display = 'block';
                document.getElementById('file').required = true;
            } else {
                fileInputDiv.style.display = 'none';
                document.getElementById('file').required = false;
            }
        }
    </script>
</body>
</html>
