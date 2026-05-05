<?php
/**
 * Vue d'inscription
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - RapporAI</title>
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
        .register-card {
            width: 100%;
            max-width: 430px;
            background: white;
            border-radius: 24px;
            border: 1px solid #bfdbfe;
            box-shadow: 0 20px 50px rgba(37, 99, 235, 0.12);
            padding: 34px;
        }
        .brand-badge {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            overflow: hidden;
        }
        .brand-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 18px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: #1e3a8a;
        }
        .brand-subtitle {
            color: #1e40af;
            margin-top: 6px;
            font-size: 14px;
        }
        .form-title {
            margin-top: 28px;
            margin-bottom: 8px;
            font-size: 28px;
            font-weight: 700;
        }
        .form-subtitle {
            margin-bottom: 28px;
            color: #475569;
        }
        .form-control {
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            padding: 14px 16px;
            background: #eff6ff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.18);
            background: white;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #1e3a8a;
        }
        .btn-register {
            background: #2563eb;
            border: none;
            color: white;
            border-radius: 14px;
            padding: 14px;
            width: 100%;
            font-weight: 700;
            margin-top: 8px;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.28);
        }
        .btn-register:hover {
            background: #1e40af;
        }
        .login-link {
            text-align: center;
            margin-top: 22px;
            color: #475569;
        }
        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            color: #1e40af;
        }
        .alert {
            border-radius: 16px;
        }
        .form-check {
            padding-left: 1.8em;
        }
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-top: 0.15em;
            border: 2px solid #bfdbfe;
            border-radius: 6px;
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .form-check-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.18);
        }
        .form-check-label {
            color: #475569;
            font-size: 14px;
            cursor: pointer;
        }
        .form-check-label a {
            color: #2563eb;
            font-weight: 600;
        }
        .form-check-label a:hover {
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="text-center">
            <div class="brand-badge">
                <img src="assets/images/logo.jpg" alt="GCI Logo">
            </div>
            <p class="brand-title">RapporAI</p>
            <p class="brand-subtitle">Rapports intelligents</p>
        </div>

        <h2 class="form-title">Inscription</h2>
        <p class="form-subtitle">Créez votre compte</p>

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

        <form method="POST" action="?action=auth/handle-register">
            <div class="mb-3">
                <label for="name" class="form-label">Nom complet</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Profil</label>
                <input type="text" class="form-control" id="role" name="role" placeholder="Ex: Ingénieur, Chef de chantier, Technicien..." required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="vous@exemple.com" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-register">
                <i class="fas fa-user-plus me-2"></i> S'inscrire
            </button>
        </form>

        <div class="login-link">
            <p>Déjà un compte ? <a href="?action=auth/login">Se connecter</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
