<?php
/**
 * Fichier de diagnostic pour déboguer la connexion
 * À accéder via: https://votre-app.onrender.com/diagnostic.php
 */

echo "<h1>Diagnostic ChantierAI</h1>";

// 1. Vérifier les variables d'environnement
echo "<h2>Variables d'environnement:</h2>";
echo "<pre>";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'NON DÉFINI') . "\n";
echo "DB_PORT: " . (getenv('DB_PORT') ?: 'NON DÉFINI') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'NON DÉFINI') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ?: 'NON DÉFINI') . "\n";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '***' : 'NON DÉFINI') . "\n";
echo "APP_ENV: " . (getenv('APP_ENV') ?: 'NON DÉFINI') . "\n";
echo "</pre>";

// 2. Charger le .env et config
echo "<h2>Chargement de la configuration:</h2>";
echo "<pre>";
if (file_exists('config/load-env.php')) {
    echo "✓ config/load-env.php existe\n";
    require_once 'config/load-env.php';
    echo "✓ config/load-env.php chargé\n";
} else {
    echo "✗ config/load-env.php N'EXISTE PAS\n";
}

if (file_exists('config/config.php')) {
    echo "✓ config/config.php existe\n";
    require_once 'config/config.php';
    echo "✓ config/config.php chargé\n";
    echo "  DB_HOST constant: " . (defined('DB_HOST') ? DB_HOST : 'NON DÉFINI') . "\n";
    echo "  DB_PORT constant: " . (defined('DB_PORT') ? DB_PORT : 'NON DÉFINI') . "\n";
} else {
    echo "✗ config/config.php N'EXISTE PAS\n";
}
echo "</pre>";

// 3. Tester la connexion
echo "<h2>Test de connexion MySQL:</h2>";
echo "<pre>";
if (file_exists('config/Database.php')) {
    require_once 'config/Database.php';
    $db = new Database();
    try {
        $pdo = $db->connect();
        echo "✓ CONNEXION RÉUSSIE!\n";
        // Tester une requête simple
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        echo "  MySQL Version: " . $result['version'] . "\n";
    } catch (PDOException $e) {
        echo "✗ ERREUR DE CONNEXION:\n";
        echo "  " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ config/Database.php N'EXISTE PAS\n";
}
echo "</pre>";

// 4. Vérifier les fichiers importants
echo "<h2>Fichiers du projet:</h2>";
echo "<pre>";
$files = [
    'index.php',
    'config/config.php',
    'config/config.example.php',
    'config/Database.php',
    'config/load-env.php',
    'config/helpers.php',
    'app/controllers/AuthController.php'
];
foreach ($files as $file) {
    echo ($file_exists($file) ? "✓" : "✗") . " $file\n";
}
echo "</pre>";

// 5. Infos système
echo "<h2>Infos système:</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "OS: " . php_uname() . "\n";
echo "Extensions MySQL: " . (extension_loaded('pdo_mysql') ? "✓ PDO MySQL chargée" : "✗ PDO MySQL NON chargée") . "\n";
echo "Extensions MySQLi: " . (extension_loaded('mysqli') ? "✓ MySQLi chargée" : "✗ MySQLi NON chargée") . "\n";
echo "</pre>";
?>
