<?php
/**
 * Vue de création de projet
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Projet - RapporAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 20px;
            color: white !important;
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            margin: 0 10px;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 30px auto;
        }
        .form-title {
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 15px;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
            padding: 10px 30px;
        }
        .btn-primary:hover {
            background-color: #1e40af;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="?action=dashboard">
                <img src="assets/images/logo.jpg" alt="GCI Logo" style="height:24px; width:24px; object-fit:contain; margin-right:8px; vertical-align:middle;"> RapporAI
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?action=dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="?action=projects">Projets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=reports">Rapports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Formulaire -->
    <div class="form-container">
        <h2 class="form-title"><i class="fas fa-plus-circle"></i> Créer un nouveau projet</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="?action=projects/handle-create">
            <div class="mb-3">
                <label for="name" class="form-label">Nom du projet *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Localisation *</label>
                <input type="text" class="form-control" id="location" name="location" placeholder="Ex: Kinshasa, RDC" required>
            </div>

            <div class="mb-3">
                <label for="project_type" class="form-label">Type de projet *</label>
                <select class="form-select" id="project_type" name="project_type" required>
                    <option value="batiment">Bâtiment</option>
                    <option value="route">Route</option>
                    <option value="pont">Pont</option>
                    <option value="château d'eau">Château d'eau</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Date de début *</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Décrivez votre projet..."></textarea>
            </div>

            <div class="mb-3">
                <label for="maitre_ouvrage" class="form-label">Maître d'ouvrage</label>
                <input type="text" class="form-control" id="maitre_ouvrage" name="maitre_ouvrage" placeholder="Ex: Ministère des Infrastructures">
            </div>

            <div class="mb-3">
                <label for="missions_controle" class="form-label">Missions de contrôle</label>
                <textarea class="form-control" id="missions_controle" name="missions_controle" rows="3" placeholder="Ex: Bureau d'études XYZ, Cabinet de conseil..."></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Créer le projet
                </button>
                <a href="?action=projects" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
