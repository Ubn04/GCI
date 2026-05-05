# 🏗️ ChantierAI - Système de Gestion de Rapports Intelligents

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

Application web de gestion de projets de chantier avec génération automatique de rapports par Intelligence Artificielle (Gemini AI).

## ✨ Fonctionnalités

### 🎯 Gestion de Projets
- ✅ Création et gestion de projets de chantier
- ✅ Suivi des informations détaillées (client, localisation, dates)
- ✅ Organisation par statut (en cours, terminé, en attente)
- ✅ Interface moderne et intuitive

### 📄 Génération de Rapports IA
- ✅ Rapports journaliers, mensuels et annuels
- ✅ Génération automatique via **Gemini AI**
- ✅ Formatage professionnel et structuré
- ✅ Historique complet des rapports

### 📤 Export Multi-Format
- ✅ Export PDF avec **DomPDF**
- ✅ Export Word (.doc) avec **PHPWord**
- ✅ Logo et référence unique (RAP-YEAR-XXXX)
- ✅ Mise en page professionnelle

### 👥 Gestion des Utilisateurs
- ✅ Système d'authentification sécurisé
- ✅ Profils personnalisables
- ✅ Gestion des photos de profil
- ✅ Tableau de bord personnalisé

### 🎨 Interface Moderne
- ✅ Design responsive (mobile, tablette, desktop)
- ✅ Animations fluides
- ✅ Modal professionnel pour les rapports
- ✅ Thème bleu élégant

## 🚀 Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Composer
- Serveur web (Apache/Nginx)

### Étapes d'Installation

1. **Cloner le repository**
```bash
git clone https://github.com/votre-username/chantierai.git
cd chantierai
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer la base de données**
```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE chantierai;
exit;

# Importer le schéma
mysql -u root -p chantierai < database.sql
```

4. **Configurer l'application**
```bash
# Copier le fichier de configuration
cp config/config.example.php config/config.php

# Éditer config/config.php avec vos paramètres
nano config/config.php
```

5. **Configurer les permissions**
```bash
chmod 755 uploads/
chmod 755 uploads/profiles/
```

6. **Configurer l'API Gemini**
- Obtenir une clé API sur [Google AI Studio](https://makersuite.google.com/app/apikey)
- Ajouter la clé dans votre configuration

7. **Accéder à l'application**
```
http://localhost/chantierai
```

## 📁 Structure du Projet

```
chantierai/
├── app/
│   ├── controllers/      # Contrôleurs MVC
│   ├── models/          # Modèles de données
│   └── views/           # Vues (templates)
├── assets/
│   ├── css/            # Feuilles de style
│   └── images/         # Images et logos
├── config/
│   ├── config.php      # Configuration principale
│   ├── Database.php    # Connexion base de données
│   └── helpers.php     # Fonctions utilitaires
├── uploads/
│   └── profiles/       # Photos de profil
├── vendor/             # Dépendances Composer
├── .gitignore
├── composer.json
├── database.sql        # Schéma de base de données
├── index.php           # Point d'entrée
└── README.md
```

## 🔧 Configuration

### Base de Données
Éditer `config/config.php` :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'chantierai');
define('DB_USER', 'root');
define('DB_PASS', 'votre_mot_de_passe');
```

### API Gemini
Ajouter votre clé API dans le contrôleur de rapports ou dans la configuration.

## 📖 Utilisation

### 1. Créer un Compte
- Accéder à la page d'inscription
- Remplir le formulaire (nom, profil, email, mot de passe)
- Accepter les conditions d'utilisation

### 2. Créer un Projet
- Se connecter au tableau de bord
- Cliquer sur "Nouveau Projet"
- Remplir les informations du projet
- Sauvegarder

### 3. Générer un Rapport
- Ouvrir un projet
- Cliquer sur "Générer un rapport"
- Choisir le type (journalier, mensuel, annuel)
- L'IA génère automatiquement le rapport

### 4. Exporter un Rapport
- Ouvrir un rapport
- Cliquer sur "Export PDF" ou "Export Word"
- Le fichier se télécharge automatiquement

## 🛠️ Technologies Utilisées

### Backend
- **PHP 8.0+** - Langage serveur
- **MySQL** - Base de données
- **Composer** - Gestionnaire de dépendances

### Frontend
- **Bootstrap 5.3** - Framework CSS
- **Font Awesome 6.4** - Icônes
- **JavaScript Vanilla** - Interactions

### Bibliothèques
- **DomPDF** - Génération de PDF
- **PHPWord** - Génération de documents Word
- **Gemini AI** - Génération de contenu IA

## 🎨 Captures d'Écran

### Tableau de Bord
Interface moderne avec statistiques et accès rapide aux projets.

### Génération de Rapports
Modal professionnel avec aperçu du rapport et options d'export.

### Export PDF/Word
Documents professionnels avec logo, référence et mise en page soignée.

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📝 License

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👨‍💻 Auteur

**Votre Nom**
- GitHub: [@votre-username](https://github.com/votre-username)
- Email: votre.email@exemple.com

## 🙏 Remerciements

- [Google Gemini AI](https://ai.google.dev/) pour l'API de génération de contenu
- [DomPDF](https://github.com/dompdf/dompdf) pour la génération de PDF
- [PHPWord](https://github.com/PHPOffice/PHPWord) pour la génération de documents Word
- [Bootstrap](https://getbootstrap.com/) pour le framework CSS

## 📞 Support

Pour toute question ou problème :
- Ouvrir une [issue](https://github.com/votre-username/chantierai/issues)
- Consulter la [documentation](https://github.com/votre-username/chantierai/wiki)

---

⭐ Si ce projet vous a aidé, n'hésitez pas à lui donner une étoile !
