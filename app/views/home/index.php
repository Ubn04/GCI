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
            background: url('assets/images/background-engineer.svg') no-repeat center center fixed;
            background-size: cover;
            color: #1e3a8a;
            position: relative;
        }
        
        /* Overlay pour améliorer la lisibilité */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(248, 251, 255, 0.85);
            z-index: -1;
        }
        
        .hero-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 32px 24px 72px;
        }
        /* Navbar Ultra-Professionnelle */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .topbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12);
        }
        
        .navbar-container {
            width: 100%;
            max-width: 1400px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
        }
        
        .hero-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 120px 24px 72px;
        }
        
        /* Brand Section */
        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .brand:hover {
            transform: translateY(-1px);
        }
        
        .brand-badge {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .brand-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
            border-radius: 16px;
        }
        
        .brand-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }
        
        .brand:hover .brand-badge {
            transform: scale(1.05);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.35);
        }
        
        .brand-title {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
        }
        
        /* Navigation Links */
        .topbar-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 60px;
        }
        
        .nav-link {
            position: relative;
            padding: 12px 20px;
            color: #374151;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 12px;
        }
        
        .nav-link:hover {
            color: #1e3a8a;
            transform: translateY(-2px);
        }
        
        .nav-link:hover::before {
            opacity: 1;
        }
        
        .nav-link.active {
            color: #667eea;
            background: rgba(102, 126, 234, 0.08);
        }
        
        /* CTA Button */
        .btn-signin {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            border-radius: 14px;
            border: none;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-signin::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-signin:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-signin:hover::before {
            left: 100%;
        }
        
        .btn-signin:active {
            transform: translateY(-1px);
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            gap: 4px;
            padding: 8px;
            background: none;
            border: none;
            cursor: pointer;
        }
        
        .mobile-menu-toggle span {
            width: 24px;
            height: 2px;
            background: #374151;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }
        
        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        
        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }
        .hero-content {
            text-align: center;
            padding: 64px 0 56px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.1);
            margin: 40px 0;
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
            color: white;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(191, 219, 254, 0.5);
            padding: 28px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
            min-height: 190px;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(15, 23, 42, 0.15);
            background: rgba(255, 255, 255, 1);
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
        
        @media (max-width: 768px) {
            .navbar-container {
                padding: 0 20px;
            }
            
            .topbar-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                border-top: 1px solid rgba(255, 255, 255, 0.12);
            }
            
            .topbar-nav.active {
                display: flex;
            }
            
            .mobile-menu-toggle {
                display: flex;
            }
            
            .btn-signin {
                display: none;
            }
            
            .nav-link {
                padding: 16px 20px;
                text-align: center;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }
            
            .nav-link:last-child {
                border-bottom: none;
            }
        }
        
        @media (max-width: 820px) {
            .hero-heading {
                font-size: clamp(2.6rem, 8vw, 4.2rem);
            }
            .hero-copy {
                font-size: 1rem;
            }
        }
        @media (max-width: 720px) {
            .hero-content {
                text-align: center;
                margin: 20px 0;
                padding: 40px 20px;
            }
            
            .feature-card {
                padding: 24px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .brand-title {
                font-size: 18px;
            }
            
            .navbar-container {
                padding: 0 16px;
            }
        }
    </style>
</head>
<body>
    <div class="hero-page">
        <header class="topbar" id="navbar">
            <div class="navbar-container">
                <a href="?action=home" class="brand">
                    <div class="brand-badge">
                        <img src="assets/images/logo.jpg" alt="GCI Logo">
                    </div>
                    <span class="brand-title">ChantierAI</span>
                </a>
                
                <nav class="topbar-nav">
                    <a href="?action=home" class="nav-link active">Accueil</a>
                    <a href="#features" class="nav-link">Fonctionnalités</a>
                    <a href="#about" class="nav-link">À propos</a>
                    <a href="#contact" class="nav-link">Contact</a>
                </nav>
                
                <div class="navbar-actions">
                    <a href="?action=auth/login" class="btn-signin">
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter
                    </a>
                </div>
                
                <button class="mobile-menu-toggle" id="mobileToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
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
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.querySelector('.topbar-nav');

        mobileToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                mobileToggle.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Active nav link highlighting
        const navLinks = document.querySelectorAll('.nav-link');
        const currentPath = window.location.search;
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath || 
                (currentPath === '' && link.getAttribute('href') === '?action=home')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    </script>
</body>
</html>