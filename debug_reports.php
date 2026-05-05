<?php
/**
 * Script de diagnostic pour identifier les problèmes de génération de rapports
 */

require_once 'config/config.php';
require_once 'config/Database.php';

echo "<h1>Diagnostic du système de génération de rapports</h1>";

// 1. Vérifier la connexion à la base de données
echo "<h2>1. Connexion à la base de données</h2>";
try {
    $db = new Database();
    $pdo = $db->connect();
    echo "✅ Connexion à la base de données réussie<br>";
} catch (Exception $e) {
    echo "❌ Erreur de connexion à la base de données: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Vérifier les constantes API
echo "<h2>2. Configuration des API</h2>";
echo "OpenAI API Key: " . (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY) ? "✅ Définie" : "❌ Non définie") . "<br>";
echo "OpenAI API URL: " . (defined('OPENAI_API_URL') ? OPENAI_API_URL : "❌ Non définie") . "<br>";
echo "OpenAI Model: " . (defined('OPENAI_MODEL') ? OPENAI_MODEL : "❌ Non défini") . "<br>";
echo "Gemini API Key: " . (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY) ? "✅ Définie" : "❌ Non définie") . "<br>";

// 3. Vérifier les tables de base de données
echo "<h2>3. Structure de la base de données</h2>";
$tables = ['users', 'projects', 'site_data', 'reports'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "✅ Table '$table': $count enregistrements<br>";
    } catch (Exception $e) {
        echo "❌ Erreur avec la table '$table': " . $e->getMessage() . "<br>";
    }
}

// 4. Vérifier les utilisateurs connectés
echo "<h2>4. Session utilisateur</h2>";
if (isset($_SESSION['user_id'])) {
    echo "✅ Utilisateur connecté (ID: " . $_SESSION['user_id'] . ")<br>";
    
    // Vérifier les projets de l'utilisateur
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $projectCount = $stmt->fetchColumn();
    echo "✅ Projets de l'utilisateur: $projectCount<br>";
    
    if ($projectCount > 0) {
        // Prendre le premier projet pour tester
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $project = $stmt->fetch();
        
        if ($project) {
            echo "✅ Projet de test: " . htmlspecialchars($project['name']) . "<br>";
            
            // Vérifier les données de site pour ce projet
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM site_data WHERE project_id = ?");
            $stmt->execute([$project['id']]);
            $siteDataCount = $stmt->fetchColumn();
            echo "✅ Données de site pour ce projet: $siteDataCount<br>";
        }
    }
} else {
    echo "❌ Aucun utilisateur connecté<br>";
}

// 5. Test de l'API OpenAI (si configurée)
echo "<h2>5. Test de l'API OpenAI</h2>";
if (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY)) {
    $testMessage = [
        ['role' => 'user', 'content' => 'Dis simplement "Test réussi" en français.']
    ];
    
    $payload = [
        'model' => 'gpt-3.5-turbo', // Utiliser un modèle moins cher pour le test
        'messages' => $testMessage,
        'max_tokens' => 10
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, OPENAI_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($response === false) {
        echo "❌ Erreur cURL: $curlError<br>";
    } elseif ($httpCode !== 200) {
        echo "❌ Erreur HTTP $httpCode: $response<br>";
    } else {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            echo "✅ API OpenAI fonctionne: " . $result['choices'][0]['message']['content'] . "<br>";
        } else {
            echo "❌ Réponse OpenAI invalide: $response<br>";
        }
    }
} else {
    echo "❌ Clé API OpenAI non configurée<br>";
}

// 6. Vérifier les permissions de fichiers
echo "<h2>6. Permissions de fichiers</h2>";
$directories = ['uploads', 'uploads/profiles'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ Répertoire '$dir': accessible en écriture<br>";
        } else {
            echo "❌ Répertoire '$dir': non accessible en écriture<br>";
        }
    } else {
        echo "❌ Répertoire '$dir': n'existe pas<br>";
    }
}

echo "<h2>Diagnostic terminé</h2>";
echo "<p><a href='?action=dashboard'>Retour au dashboard</a></p>";
?>