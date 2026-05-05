<?php
/**
 * Vue de liste des projets
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <style>
        /* ===== STYLES PROFESSIONNELS POUR LES MODALS DE PROJET ===== */
        
        /* Modal container */
        .modal-content {
            border: none;
            border-radius: 24px !important;
            box-shadow: 0 25px 50px rgba(15, 23, 42, 0.2);
            overflow: hidden;
        }
        
        /* Modal header */
        .modal-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 32px 32px 28px;
            border-bottom: none;
            position: relative;
        }
        
        .modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 32px;
            width: 60px;
            height: 4px;
            background: #f59e0b;
            border-radius: 2px;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 800;
            color: white;
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .modal-header .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            width: 36px;
            height: 36px;
            opacity: 1;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .modal-header .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        /* Modal body */
        .modal-body {
            padding: 32px !important;
            background: #f8fafc;
        }
        
        /* Form groups */
        .modal-body .mb-3,
        .modal-body .mb-4 {
            background: white;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .modal-body .mb-3:hover,
        .modal-body .mb-4:hover {
            border-color: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }
        
        .modal-body .mb-3:first-child {
            margin-top: 0 !important;
        }
        
        /* Labels */
        .modal-body .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .modal-body .form-label::before {
            content: '';
            width: 4px;
            height: 16px;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            border-radius: 2px;
        }
        
        /* Form controls */
        .modal-body .form-control,
        .modal-body .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px !important;
            padding: 14px 18px;
            font-size: 15px;
            font-weight: 500;
            color: #1e3a8a;
            transition: all 0.3s ease;
            background: #ffffff;
        }
        
        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: white;
        }
        
        .modal-body .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        
        .modal-body textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        
        /* Submit button */
        .modal-body .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border: none;
            border-radius: 12px !important;
            padding: 16px 32px !important;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            margin-top: 8px;
        }
        
        .modal-body .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
        }
        
        .modal-body .btn-primary:active {
            transform: translateY(0);
        }
        
        /* Icon decorations for inputs */
        .modal-body .mb-3:nth-child(1) .form-label::before {
            background: linear-gradient(135deg, #2563eb, #60a5fa);
        }
        
        .modal-body .mb-3:nth-child(2) .form-label::before {
            background: linear-gradient(135deg, #10b981, #34d399);
        }
        
        .modal-body .mb-3:nth-child(3) .form-label::before {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }
        
        .modal-body .mb-3:nth-child(4) .form-label::before {
            background: linear-gradient(135deg, #ec4899, #f472b6);
        }
        
        .modal-body .mb-3:nth-child(5) .form-label::before {
            background: linear-gradient(135deg, #06b6d4, #22d3ee);
        }
        
        .modal-body .mb-3:nth-child(6) .form-label::before {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        }
        
        .modal-body .mb-4 .form-label::before {
            background: linear-gradient(135deg, #ef4444, #f87171);
        }
        
        /* Input with edit icon */
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon .form-control {
            padding-right: 50px;
        }
        
        .input-edit-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .input-edit-icon:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-color: #2563eb;
            color: white;
            transform: translateY(-50%) scale(1.1);
        }
        
        .input-with-icon .form-control:focus ~ .input-edit-icon {
            border-color: #2563eb;
        }
        
        /* Animation */
        .modal.fade .modal-dialog {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(0.9) translateY(-20px);
        }
        
        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .modal-header {
                padding: 24px 20px 20px;
            }
            
            .modal-body {
                padding: 24px 20px !important;
            }
            
            .modal-body .mb-3,
            .modal-body .mb-4 {
                padding: 16px;
            }
            
            .modal-title {
                font-size: 20px;
            }
        }
        
        /* Animations et styles pour la page projets */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .page-header {
            animation: fadeInUp 0.5s ease-out;
        }
        
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 24px;
            animation: fadeInUp 0.6s ease-out 0.2s backwards;
        }
        
        .project-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            margin-bottom: 0;
            border: 1px solid #e2e8f0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
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
        
        /* Variantes de couleurs pour les cards projets */
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
        
        .project-card:nth-child(4n+1):hover {
            border-color: #e9d5ff;
            box-shadow: 0 20px 60px rgba(147, 51, 234, 0.15);
        }
        
        .project-card:nth-child(4n+2):hover {
            border-color: #a7f3d0;
            box-shadow: 0 20px 60px rgba(16, 185, 129, 0.15);
        }
        
        .project-card:nth-child(4n+3):hover {
            border-color: #fde68a;
            box-shadow: 0 20px 60px rgba(245, 158, 11, 0.15);
        }
        
        .project-card:nth-child(4n+4):hover {
            border-color: #fbcfe8;
            box-shadow: 0 20px 60px rgba(236, 72, 153, 0.15);
        }
        
        .project-card:nth-child(4n+1) .project-meta i {
            color: #9333ea;
        }
        
        .project-card:nth-child(4n+2) .project-meta i {
            color: #10b981;
        }
        
        .project-card:nth-child(4n+3) .project-meta i {
            color: #f59e0b;
        }
        
        .project-card:nth-child(4n+4) .project-meta i {
            color: #ec4899;
        }
        
        .project-card:nth-child(4n+1) .project-cta {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border-color: #e9d5ff;
            color: #6b21a8;
        }
        
        .project-card:nth-child(4n+1) .project-cta:hover {
            background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);
            border-color: #9333ea;
            color: white;
        }
        
        .project-card:nth-child(4n+2) .project-cta {
            background: linear-gradient(135deg, #f0fdf4 0%, #d1fae5 100%);
            border-color: #a7f3d0;
            color: #065f46;
        }
        
        .project-card:nth-child(4n+2) .project-cta:hover {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-color: #10b981;
            color: white;
        }
        
        .project-card:nth-child(4n+3) .project-cta {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-color: #fde68a;
            color: #92400e;
        }
        
        .project-card:nth-child(4n+3) .project-cta:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-color: #f59e0b;
            color: white;
        }
        
        .project-card:nth-child(4n+4) .project-cta {
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
            border-color: #fbcfe8;
            color: #9f1239;
        }
        
        .project-card:nth-child(4n+4) .project-cta:hover {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            border-color: #ec4899;
            color: white;
        }
        
        .project-card:hover::before {
            transform: scaleX(1);
        }
        
        .project-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.15);
            border-color: #93c5fd;
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
        
        .project-card-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        
        .project-card-actions .btn {
            width: 40px;
            height: 40px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .project-card-actions .btn-outline-secondary {
            border-color: #e2e8f0;
            color: #64748b;
        }
        
        .project-card-actions .btn-outline-secondary:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
            transform: rotate(5deg) scale(1.1);
        }
        
        .project-card-actions .btn-outline-danger {
            border-color: #fee2e2;
            color: #ef4444;
        }
        
        .project-card-actions .btn-outline-danger:hover {
            background: #ef4444;
            border-color: #ef4444;
            color: white;
            transform: rotate(-5deg) scale(1.1);
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
            display: flex;
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
        
        .empty-state {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px dashed #93c5fd;
            border-radius: 24px;
            padding: 60px 40px;
            text-align: center;
            color: #1e40af;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            animation: scaleIn 0.5s ease-out;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #60a5fa;
            margin-bottom: 20px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }
        
        .empty-state strong {
            display: block;
            font-size: 22px;
            margin-bottom: 12px;
            color: #1e3a8a;
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
            <div class="page-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-greeting">
                            <i class="fas fa-folder-open"></i>
                            <span>Gestion des projets</span>
                        </div>
                        <div class="header-title">
                            <h1>Projets</h1>
                            <p>Gérez et suivez tous vos chantiers en cours</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                            <i class="fas fa-plus"></i> Nouveau projet
                        </button>
                    </div>
                </div>
            </div>

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

            <div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-labelledby="deleteProjectModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 shadow-lg">
                        <div class="modal-header border-0">
                            <div>
                                <h5 class="modal-title" id="deleteProjectModalLabel">Confirmer la suppression</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body py-0">
                            <p id="deleteProjectModalText" class="mb-0">Voulez-vous vraiment supprimer ce projet ?</p>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Supprimer</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createProjectModalLabel">
                                <i class="fas fa-plus-circle me-2"></i>
                                Nouveau projet
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="?action=projects/handle-create">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom du projet</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ex: Construction du pont de la rivière" required>
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Localisation</label>
                                    <input type="text" class="form-control" id="location" name="location" placeholder="Ex: Kinshasa, RDC" required>
                                </div>
                                <div class="mb-3">
                                    <label for="project_type" class="form-label">Type de projet</label>
                                    <div class="input-with-icon">
                                        <input type="text" class="form-control" id="project_type_display" placeholder="Sélectionnez ou saisissez un type" readonly>
                                        <input type="hidden" id="project_type" name="project_type" required>
                                        <div class="input-edit-icon" id="editProjectTypeBtn" title="Modifier le type">
                                            <i class="fas fa-pencil-alt"></i>
                                        </div>
                                    </div>
                                    <div id="projectTypeOptions" style="display: none; margin-top: 12px;">
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary project-type-option" data-value="batiment">🏢 Bâtiment</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary project-type-option" data-value="route">🛣️ Route</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary project-type-option" data-value="pont">🌉 Pont</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary project-type-option" data-value="château d'eau">💧 Château d'eau</button>
                                        </div>
                                        <div class="mt-2">
                                            <input type="text" class="form-control form-control-sm" id="customProjectType" placeholder="Ou saisissez un type personnalisé...">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="maitre_ouvrage" class="form-label">Maître d'ouvrage</label>
                                    <input type="text" class="form-control" id="maitre_ouvrage" name="maitre_ouvrage" placeholder="Ex: Ministère des Infrastructures">
                                </div>
                                <div class="mb-3">
                                    <label for="missions_controle" class="form-label">Missions de contrôle</label>
                                    <textarea class="form-control" id="missions_controle" name="missions_controle" rows="3" placeholder="Décrivez les missions de contrôle..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Décrivez les objectifs et caractéristiques du projet..."></textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="start_date" class="form-label">Date d'enregistrement</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Créer le projet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProjectModalLabel">
                                <i class="fas fa-edit me-2"></i>
                                Modifier le projet
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" id="editProjectForm">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label">Nom du projet</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" placeholder="Ex: Construction du pont de la rivière" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_location" class="form-label">Localisation</label>
                                    <input type="text" class="form-control" id="edit_location" name="location" placeholder="Ex: Kinshasa, RDC" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_project_type" class="form-label">Type de projet</label>
                                    <div class="input-with-icon">
                                        <input type="text" class="form-control" id="edit_project_type_display" placeholder="Sélectionnez ou saisissez un type" readonly>
                                        <input type="hidden" id="edit_project_type" name="project_type" required>
                                        <div class="input-edit-icon" id="editProjectTypeEditBtn" title="Modifier le type">
                                            <i class="fas fa-pencil-alt"></i>
                                        </div>
                                    </div>
                                    <div id="projectTypeEditOptions" style="display: none; margin-top: 12px;">
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary project-type-edit-option" data-value="batiment">🏢 Bâtiment</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary project-type-edit-option" data-value="route">🛣️ Route</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary project-type-edit-option" data-value="pont">🌉 Pont</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary project-type-edit-option" data-value="château d'eau">💧 Château d'eau</button>
                                        </div>
                                        <div class="mt-2">
                                            <input type="text" class="form-control form-control-sm" id="customProjectTypeEdit" placeholder="Ou saisissez un type personnalisé...">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_maitre_ouvrage" class="form-label">Maître d'ouvrage</label>
                                    <input type="text" class="form-control" id="edit_maitre_ouvrage" name="maitre_ouvrage" placeholder="Ex: Ministère des Infrastructures">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_missions_controle" class="form-label">Missions de contrôle</label>
                                    <textarea class="form-control" id="edit_missions_controle" name="missions_controle" rows="3" placeholder="Décrivez les missions de contrôle..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="4" placeholder="Décrivez les objectifs et caractéristiques du projet..."></textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="edit_start_date" class="form-label">Date d'enregistrement</label>
                                    <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>
                                    Enregistrer les modifications
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($projects)): ?>
                <div class="projects-grid">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <div class="project-card-header">
                            <h2 class="project-card-title"><?php echo htmlspecialchars($project['name']); ?></h2>
                            <div class="project-card-actions">
                                <button type="button" class="btn btn-outline-secondary edit-project-btn" title="Modifier"
                                    data-bs-toggle="modal" data-bs-target="#editProjectModal"
                                    data-id="<?php echo $project['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($project['name'], ENT_QUOTES); ?>"
                                    data-location="<?php echo htmlspecialchars($project['location'], ENT_QUOTES); ?>"
                                    data-project_type="<?php echo htmlspecialchars($project['project_type'] ?? 'batiment', ENT_QUOTES); ?>"
                                    data-description="<?php echo htmlspecialchars($project['description'], ENT_QUOTES); ?>"
                                    data-start_date="<?php echo $project['start_date']; ?>"
                                    data-maitre_ouvrage="<?php echo htmlspecialchars($project['maitre_ouvrage'] ?? '', ENT_QUOTES); ?>"
                                    data-missions_controle="<?php echo htmlspecialchars($project['missions_controle'] ?? '', ENT_QUOTES); ?>">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-project-btn" title="Supprimer"
                                   data-bs-toggle="modal" data-bs-target="#deleteProjectModal"
                                   data-id="<?php echo $project['id']; ?>"
                                   data-name="<?php echo htmlspecialchars($project['name'], ENT_QUOTES); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="project-card-body">
                            <?php echo nl2br(htmlspecialchars(substr($project['description'], 0, 140))); ?><?php echo strlen($project['description']) > 140 ? '...' : ''; ?>
                        </div>
                        <div class="project-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['location']); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($project['start_date'])); ?></span>
                        </div>
                        <a href="?action=projects/open&id=<?php echo $project['id']; ?>" class="project-cta">
                            Ouvrir <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <strong>Aucun projet créé</strong>
                    <p>Commencez par créer un chantier pour gérer vos rapports !</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        <i class="fas fa-plus"></i> Nouveau projet
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ===== GESTION DU TYPE DE PROJET AVEC ICÔNE D'ÉDITION (CREATE) =====
            const editProjectTypeBtn = document.getElementById('editProjectTypeBtn');
            const projectTypeOptions = document.getElementById('projectTypeOptions');
            const projectTypeDisplay = document.getElementById('project_type_display');
            const projectTypeHidden = document.getElementById('project_type');
            const customProjectType = document.getElementById('customProjectType');
            const projectTypeOptionBtns = document.querySelectorAll('.project-type-option');
            
            const typeLabels = {
                'batiment': '🏢 Bâtiment',
                'route': '🛣️ Route',
                'pont': '🌉 Pont',
                'château d\'eau': '💧 Château d\'eau'
            };
            
            editProjectTypeBtn.addEventListener('click', function() {
                projectTypeOptions.style.display = projectTypeOptions.style.display === 'none' ? 'block' : 'none';
            });
            
            projectTypeOptionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const value = this.dataset.value;
                    projectTypeHidden.value = value;
                    projectTypeDisplay.value = typeLabels[value] || value;
                    projectTypeOptions.style.display = 'none';
                    customProjectType.value = '';
                });
            });
            
            customProjectType.addEventListener('input', function() {
                if (this.value.trim()) {
                    projectTypeHidden.value = this.value.trim();
                    projectTypeDisplay.value = this.value.trim();
                }
            });
            
            customProjectType.addEventListener('blur', function() {
                if (this.value.trim()) {
                    projectTypeOptions.style.display = 'none';
                }
            });
            
            // ===== GESTION DU TYPE DE PROJET AVEC ICÔNE D'ÉDITION (EDIT) =====
            const editProjectTypeEditBtn = document.getElementById('editProjectTypeEditBtn');
            const projectTypeEditOptions = document.getElementById('projectTypeEditOptions');
            const projectTypeEditDisplay = document.getElementById('edit_project_type_display');
            const projectTypeEditHidden = document.getElementById('edit_project_type');
            const customProjectTypeEdit = document.getElementById('customProjectTypeEdit');
            const projectTypeEditOptionBtns = document.querySelectorAll('.project-type-edit-option');
            
            editProjectTypeEditBtn.addEventListener('click', function() {
                projectTypeEditOptions.style.display = projectTypeEditOptions.style.display === 'none' ? 'block' : 'none';
            });
            
            projectTypeEditOptionBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const value = this.dataset.value;
                    projectTypeEditHidden.value = value;
                    projectTypeEditDisplay.value = typeLabels[value] || value;
                    projectTypeEditOptions.style.display = 'none';
                    customProjectTypeEdit.value = '';
                });
            });
            
            customProjectTypeEdit.addEventListener('input', function() {
                if (this.value.trim()) {
                    projectTypeEditHidden.value = this.value.trim();
                    projectTypeEditDisplay.value = this.value.trim();
                }
            });
            
            customProjectTypeEdit.addEventListener('blur', function() {
                if (this.value.trim()) {
                    projectTypeEditOptions.style.display = 'none';
                }
            });
            
            // ===== GESTION DES BOUTONS D'ÉDITION =====
            const editButtons = document.querySelectorAll('.edit-project-btn');
            const editForm = document.getElementById('editProjectForm');
            const editName = document.getElementById('edit_name');
            const editLocation = document.getElementById('edit_location');
            const editDescription = document.getElementById('edit_description');
            const editStartDate = document.getElementById('edit_start_date');
            const editMaitreOuvrage = document.getElementById('edit_maitre_ouvrage');
            const editMissionsControle = document.getElementById('edit_missions_controle');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const projectId = this.dataset.id;
                    editForm.action = `?action=projects/handle-update&id=${projectId}`;
                    editName.value = this.dataset.name;
                    editLocation.value = this.dataset.location;
                    
                    // Gérer le type de projet
                    const projectType = this.dataset.project_type;
                    projectTypeEditHidden.value = projectType;
                    projectTypeEditDisplay.value = typeLabels[projectType] || projectType;
                    
                    editDescription.value = this.dataset.description;
                    editStartDate.value = this.dataset.start_date;
                    editMaitreOuvrage.value = this.dataset.maitre_ouvrage || '';
                    editMissionsControle.value = this.dataset.missions_controle || '';
                });
            });

            // ===== GESTION DES BOUTONS DE SUPPRESSION =====
            const deleteButtons = document.querySelectorAll('.delete-project-btn');
            const deleteProjectModalText = document.getElementById('deleteProjectModalText');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const projectId = this.dataset.id;
                    const projectName = this.dataset.name;
                    confirmDeleteBtn.href = `?action=projects/delete&id=${projectId}`;
                    deleteProjectModalText.textContent = `Voulez-vous vraiment supprimer le projet « ${projectName} » ?`;
                });
            });
        });
    </script>
</body>
</html>
