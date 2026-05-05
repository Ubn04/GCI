<?php
/**
 * Vue de connexion
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8fbff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1e3a8a;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 22px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.1);
            padding: 32px;
            border: 1px solid #bfdbfe;
        }
        .brand-block {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        .brand-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .brand-subtitle {
            margin: 0;
            color: #1e40af;
            font-size: 14px;
        }
        .login-title {
            margin: 0 0 6px;
            font-size: 28px;
            font-weight: 700;
            color: #1e3a8a;
            text-align: center;
        }
        .login-subtitle {
            margin: 0 0 28px;
            text-align: center;
            color: #1e40af;
            font-size: 14px;
        }
        .form-label {
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 8px;
        }
        .form-control {
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            background: #eff6ff;
            padding: 14px 16px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.18);
            background: white;
        }
        .btn-login {
            width: 100%;
            border-radius: 14px;
            background: #2563eb;
            border: none;
            color: white;
            padding: 14px;
            font-weight: 700;
            font-size: 16px;
        }
        .btn-login:hover {
            background: #1e40af;
        }
        .alert {
            border-radius: 14px;
        }
        .register-link {
            text-align: center;
            margin-top: 18px;
            color: #1e3a8a;
            font-size: 14px;
        }
        .register-link a {
            color: #1e40af;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            color: #2563eb;
        }
        .home-link {
            position: fixed;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            z-index: 10;
        }
        .home-link:hover {
            color: #2563eb;
            transform: translateX(-50%) translateY(-4px);
        }
        .home-arrow {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
            transition: all 0.3s ease;
        }
        .home-link:hover .home-arrow {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
            animation: bounce 1s ease-in-out infinite;
        }
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-block">
            <div class="brand-icon">
                <img src="assets/images/logo.jpg" alt="GCI Logo">
            </div>
            <div>
                <p class="brand-title">ChantierAI</p>
                <p class="brand-subtitle">Rapports intelligents</p>
            </div>
        </div>

        <h1 class="login-title">Connexion</h1>
        <p class="login-subtitle">Accédez à vos projets</p>

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

        <form method="POST" action="?action=auth/handle-login">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="vous@exemple.com" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-login">
                <span style="display:inline-flex;align-items:center;gap:8px;justify-content:center;">Se connecter</span>
            </button>
        </form>

        <div class="register-link">
            Pas de compte ? <a href="?action=auth/register">S'inscrire</a>
        </div>
    </div>

    <a href="index.php" class="home-link">
        <div class="home-arrow">
            <i class="fas fa-arrow-down"></i>
        </div>
        <span>Retour à l'accueil</span>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
