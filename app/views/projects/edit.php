<?php
/**
 * Vue d'édition de projet
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Projet - RapporAI</title>
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
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
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
        </div>
    </nav>

    <!-- Formulaire -->
    <div class="form-container">
        <h2 class="form-title"><i class="fas fa-edit"></i> Modifier le projet</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="?action=projects/handle-update&id=<?php echo $project['id']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Nom du projet *</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($project['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Localisation *</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($project['location']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="project_type" class="form-label">Type de projet *</label>
                <select class="form-select" id="project_type" name="project_type" required>
                    <option value="batiment" <?php echo ($project['project_type'] ?? '') === 'batiment' ? 'selected' : ''; ?>>Bâtiment</option>
                    <option value="route" <?php echo ($project['project_type'] ?? '') === 'route' ? 'selected' : ''; ?>>Route</option>
                    <option value="pont" <?php echo ($project['project_type'] ?? '') === 'pont' ? 'selected' : ''; ?>>Pont</option>
                    <option value="château d'eau" <?php echo ($project['project_type'] ?? '') === "château d'eau" ? 'selected' : ''; ?>>Château d'eau</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Date de début *</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $project['start_date']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($project['description']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="client" class="form-label">Maître d'ouvrage (Client)</label>
                <input type="text" class="form-control" id="client" name="client" value="<?php echo htmlspecialchars($project['client'] ?? 'Non spécifié'); ?>" placeholder="Ex: Ministère des Infrastructures">
            </div>

            <div class="mb-3">
                <label for="control_mission" class="form-label">Mission contrôle</label>
                <input type="text" class="form-control" id="control_mission" name="control_mission" value="<?php echo htmlspecialchars($project['control_mission'] ?? 'Non spécifié'); ?>" placeholder="Ex: Bureau d'études XYZ">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <a href="?action=projects/show&id=<?php echo $project['id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
