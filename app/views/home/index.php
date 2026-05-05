<?php
/**
 * Page d'accueil publique
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChantierAI - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            color-scheme: light;
        }
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f8fbff;
            color: #1e3a8a;
        }
        .hero-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 32px 24px 72px;
        }
        .topbar {
            position: fixed;
            top: 0;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 32px;
            background: white;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
            z-index: 50;
        }
        .hero-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 112px 24px 72px;
        }
        .topbar-nav {
            display: flex;
            gap: 24px;
            margin-left: 32px;
        }
        .topbar-nav a {
            color: #1e3a8a;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .brand,
        .brand a,
        .brand-title,
        .brand-badge {
            text-decoration: none !important;
        }
        .brand-title {
            color: #1e3a8a;
        }
        .topbar-nav a:hover {
            color: #2563eb;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-badge {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .brand-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 14px;
        }
        .brand-title {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
        }
        .btn-signin {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 14px;
            padding: 14px 22px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s ease;
        }
        .btn-signin:hover {
            background: #1e40af;
        }
        .hero-content {
            text-align: center;
            padding: 64px 0 56px;
        }
        .hero-heading {
            margin: 0 auto 24px;
            max-width: 900px;
            font-size: clamp(3rem, 5vw, 5rem);
            line-height: 1.02;
            font-weight: 800;
        }
        .hero-heading .highlight {
            color: #2563eb;
        }
        .hero-copy {
            margin: 0 auto;
            max-width: 680px;
            color: #475569;
            font-size: 1.04rem;
            line-height: 1.9;
        }
        .hero-action {
            margin-top: 40px;
        }
        .animate-fade-in {
            opacity: 0;
            animation: fadeInUp 0.9s ease forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .btn-start {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 16px;
            padding: 18px 28px;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 18px 40px rgba(37, 99, 235, 0.18);
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .btn-start:hover {
            transform: translateY(-1px);
            background: #1e40af;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        .feature-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #bfdbfe;
            padding: 28px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
            min-height: 190px;
        }
        .feature-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 18px;
            margin-bottom: 18px;
        }
        .feature-title {
            margin: 0 0 10px;
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .feature-text {
            margin: 0;
            color: #64748b;
            line-height: 1.8;
        }
        @media (max-width: 992px) {
            .features-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 820px) {
            .topbar {
                flex-wrap: wrap;
                justify-content: center;
                padding: 18px 20px;
            }
            .topbar-nav {
                order: 3;
                width: 100%;
                justify-content: center;
                margin: 16px 0 0;
                gap: 18px;
            }
            .btn-signin {
                width: auto;
                padding: 12px 18px;
            }
            .hero-heading {
                font-size: clamp(2.6rem, 8vw, 4.2rem);
            }
            .hero-copy {
                font-size: 1rem;
            }
        }
        @media (max-width: 720px) {
            .topbar,
            .hero-content {
                text-align: center;
            }
            .topbar {
                flex-direction: column;
                align-items: center;
            }
            .topbar-nav {
                order: 3;
                width: 100%;
                justify-content: center;
                margin-top: 14px;
            }
            .feature-card {
                padding: 24px;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="hero-page">
        <header class="topbar">
            <a href="?action=home" class="brand">
                <span class="brand-badge"><img src="assets/images/logo.jpg" alt="GCI Logo"></span>
                <span class="brand-title">ChantierAI</span>
            </a>
            <nav class="topbar-nav">
                <a href="?action=home">Accueil</a>
                <a href="?action=auth/login">Se connecter</a>
            </nav>
            <a href="?action=auth/login" class="btn-signin">Se connecter <i class="fas fa-arrow-right-long"></i></a>
        </header>

        <section class="hero-content">
            <h1 class="hero-heading animate-fade-in" style="animation-delay: 0.1s;">Générez vos rapports de chantier avec l'<span class="highlight">Intelligence Artificielle</span></h1>
            <p class="hero-copy animate-fade-in" style="animation-delay: 0.3s;">Simplifiez votre quotidien d'ingénieur. Envoyez vos données terrain, l'IA rédige vos rapports.</p>
            <div class="hero-action animate-fade-in" style="animation-delay: 0.5s;">
                <a href="?action=auth/register" class="btn-start">Commencer gratuitement <i class="fas fa-arrow-right-long"></i></a>
            </div>
        </section>

        <section class="features-grid">
            <article class="feature-card">
                <div class="feature-icon"><i class="fas fa-file-alt"></i></div>
                <h3 class="feature-title">Collecte simplifiée</h3>
                <p class="feature-text">Envoyez texte et photos depuis le chantier</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon"><i class="fas fa-robot"></i></div>
                <h3 class="feature-title">IA intégrée</h3>
                <p class="feature-text">Rapports structurés générés automatiquement</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon"><i class="fas fa-clock"></i></div>
                <h3 class="feature-title">Gain de temps</h3>
                <p class="feature-text">Concentrez-vous sur le terrain, pas la paperasse</p>
            </article>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
