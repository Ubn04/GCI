<?php
/**
 * Test rapide du chat IA spécialisé en génie civil
 */
require_once 'config/config.php';
require_once 'config/Database.php';

// Initialiser la connexion
$db = new Database();
$pdo = $db->connect();

// Simuler une session utilisateur
session_start();
$_SESSION['user_id'] = 1; // ID utilisateur de test

// Créer un projet de test
$stmt = $pdo->prepare("INSERT INTO projects (user_id, name, location, project_type, description, start_date, maitre_ouvrage, missions_controle) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id=id");
$stmt->execute([1, 'Test Pont Kinshasa', 'Kinshasa, RDC', 'pont', 'Construction d\'un pont sur le fleuve Congo', '2024-01-01', 'Ministère des Infrastructures', 'Bureau d\'études XYZ']);

$projectId = $pdo->lastInsertId();

// Tester le chat
require_once 'app/controllers/ChatAIController.php';
$chatController = new ChatAIController($pdo);

// Simuler une requête POST
$_POST['message'] = 'Comment calculer la portance d\'une pile de pont?';
$_POST['project_id'] = $projectId;

// Appeler la méthode
$chatController->sendMessage();
?>