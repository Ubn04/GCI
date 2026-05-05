<?php
/**
 * Vue profil utilisateur
 */
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <style>
        /* Animations et styles pour la page profil */
        .page-header {
            animation: fadeInUp 0.5s ease-out;
        }
        
        .profile-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: scaleIn 0.5s ease-out backwards;
            position: relative;
            overflow: hidden;
        }
        
        .profile-card::before {
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
        
        .profile-card:hover::before {
            transform: scaleX(1);
        }
        
        .profile-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.15);
            border-color: #93c5fd;
        }
        
        .profile-card:nth-child(1) { animation-delay: 0.1s; }
        .profile-card:nth-child(2) { animation-delay: 0.2s; }
        
        .profile-card + .profile-card {
            margin-top: 32px;
        }
        
        .profile-avatar-container {
            position: relative;
            display: inline-block;
            margin-bottom: 24px;
        }
        
        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #2563eb;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.4);
        }
        
        .profile-avatar-placeholder {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #60a5fa;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }
        
        .profile-avatar-placeholder:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.4);
        }
        
        .profile-avatar-placeholder i {
            font-size: 48px;
            color: white;
        }
        
        .profile-card h2, .profile-card h3 {
            color: #1e3a8a;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .profile-card h2 i, .profile-card h3 i {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #1e40af;
        }
        
        .form-label {
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-label i {
            color: #2563eb;
            font-size: 14px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-size: 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        
        .form-text {
            color: #64748b;
            font-size: 13px;
            margin-top: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border: none;
            padding: 14px 32px;
            font-weight: 600;
            border-radius: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            padding: 14px 32px;
            font-weight: 600;
            border-radius: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        
        .danger-zone {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 2px solid #fecaca;
            border-radius: 20px;
            padding: 24px;
        }
        
        .danger-zone h2 {
            color: #991b1b;
        }
        
        .danger-zone p {
            color: #b91c1c;
        }
        
        .alert {
            border-radius: 16px;
            border: none;
            padding: 16px 20px;
            animation: slideInRight 0.5s ease-out;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            color: #166534;
            border-left: 4px solid #22c55e;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include 'app/views/components/sidebar.php'; ?>

        <div class="main-content">
            <header class="page-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-greeting">
                            <i class="fas fa-user-circle"></i>
                            <span>Paramètres du compte</span>
                        </div>
                        <div class="header-title">
                            <h1>Mon profil</h1>
                            <p>Gérez vos informations personnelles et paramètres de compte</p>
                        </div>
                    </div>
                </div>
            </header>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="profile-card">
                <h2>
                    <i class="fas fa-user-circle"></i>
                    Informations personnelles
                </h2>
                <form method="POST" action="?action=auth/update-profile" enctype="multipart/form-data">
                    <div class="mb-4 text-center">
                        <div class="profile-avatar-container">
                            <?php if (!empty($user['photo'])): ?>
                                <img src="<?php echo APP_URL . '/' . htmlspecialchars($user['photo']); ?>" alt="Photo de profil" class="profile-avatar">
                            <?php else: ?>
                                <div class="profile-avatar-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i>
                                Nom complet
                            </label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required placeholder="Votre nom complet">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                Adresse email
                            </label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required placeholder="votre@email.com">
                        </div>
                        <div class="col-12">
                            <label for="photo" class="form-label">
                                <i class="fas fa-camera"></i>
                                Photo de profil
                            </label>
                            <input type="file" id="photo" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif">
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i>
                                JPG, PNG ou GIF, max 2 Mo
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i>
                                Nouveau mot de passe
                            </label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Laissez vide pour conserver le mot de passe actuel">
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i>
                                Laissez ce champ vide si vous ne souhaitez pas changer votre mot de passe
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save me-2"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="profile-card danger-zone">
                <h2>
                    <i class="fas fa-exclamation-triangle"></i>
                    Zone dangereuse
                </h2>
                <p class="mb-4">Cette action est irréversible. Toutes vos données (projets, rapports, etc.) seront définitivement supprimées.</p>
                <form method="POST" action="?action=auth/delete-account" onsubmit="return confirm('⚠️ ATTENTION ⚠️\n\nÊtes-vous absolument sûr de vouloir supprimer définitivement votre compte ?\n\nCette action est IRRÉVERSIBLE et supprimera :\n- Votre profil\n- Tous vos projets\n- Tous vos rapports\n- Toutes vos données\n\nTapez OUI pour confirmer.');">
                    <button type="submit" class="btn btn-danger px-5">
                        <i class="fas fa-user-slash me-2"></i>
                        Supprimer définitivement mon compte
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
