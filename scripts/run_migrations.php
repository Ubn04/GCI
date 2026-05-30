<?php
require_once __DIR__ . '/../config/config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $sqlFile = __DIR__ . '/../sql/add_report_drafts.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception('Fichier SQL introuvable: ' . $sqlFile);
    }

    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    echo "Migration appliquée avec succès.\n";
} catch (Exception $e) {
    echo "Erreur lors de la migration: " . $e->getMessage() . "\n";
    exit(1);
}
?>