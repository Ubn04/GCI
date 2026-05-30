<?php
/**
 * Configuration globale de l'application ChantierAI
 * 
 * INSTRUCTIONS:
 * 1. Copier ce fichier vers config.php
 * 2. Remplacer les valeurs par vos propres paramètres
 * 3. Ne JAMAIS commiter config.php sur GitHub
 */

// Démarrer la session
session_start();

// Charger les fonctions utilitaires
require_once dirname(__FILE__) . '/helpers.php';

// Configuration de base
define('APP_NAME', 'ChantierAI');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/chantier-ai-php');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_VERSION', '1.0.0');

// SMTP email (optionnel)
define('SMTP_HOST', getenv('MAIL_HOST') ?: 'smtp.example.com');
define('SMTP_PORT', getenv('MAIL_PORT') ?: 587);
define('SMTP_USER', getenv('MAIL_USERNAME') ?: 'user@example.com');
define('SMTP_PASS', getenv('MAIL_PASSWORD') ?: 'password');
define('SMTP_SECURE', 'tls'); // tls, ssl or none
define('SMTP_FROM_EMAIL', getenv('MAIL_FROM') ?: 'no-reply@example.com');
define('SMTP_FROM_NAME', 'ChantierAI');

// Configuration base de données
// Lecture depuis les variables d'environnement
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_NAME', getenv('DB_NAME') ?: 'chantier_ai');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASSWORD') ?: ''); // Mot de passe MySQL

// Chemins
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('MODELS_PATH', ROOT_PATH . '/app/models');
define('CONTROLLERS_PATH', ROOT_PATH . '/app/controllers');
define('VIEWS_PATH', ROOT_PATH . '/app/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Configuration Gemini API
// IMPORTANT: Obtenir votre clé API sur https://makersuite.google.com/app/apikey
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'VOTRE_CLE_API_GEMINI_ICI');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent');

// Configuration OpenAI ChatGPT (optionnel)
// IMPORTANT: Remplacez par votre vraie clé API OpenAI si vous voulez l'utiliser
define('OPENAI_API_KEY', ''); // Laissez vide si vous n'utilisez pas OpenAI
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');
define('OPENAI_MODEL', 'gpt-3.5-turbo');

// Configuration Cloudinary (optionnel - pour l'upload de fichiers)
define('CLOUDINARY_CLOUD_NAME', 'VOTRE_CLOUD_NAME');
define('CLOUDINARY_API_KEY', 'VOTRE_API_KEY');
define('CLOUDINARY_API_SECRET', 'VOTRE_API_SECRET');

// Timezone
date_default_timezone_set('Africa/Kinshasa'); // Modifier selon votre timezone

// Gestion des erreurs
// En production, mettre display_errors à 0
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Les fonctions utilitaires sont chargées depuis helpers.php
?>
