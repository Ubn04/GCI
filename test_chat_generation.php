<?php
/**
 * Script de test pour vérifier la génération de rapports
 */

require_once 'config/config.php';
require_once 'config/Database.php';

echo "<h1>Test de génération de rapport</h1>";

// Test 1: Vérifier la clé API
echo "<h2>1. Vérification de la clé API Gemini</h2>";
if (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY)) {
    echo "✅ Clé API Gemini configurée: " . substr(GEMINI_API_KEY, 0, 10) . "...<br>";
} else {
    echo "❌ Clé API Gemini non configurée<br>";
}

// Test 2: Vérifier l'URL de l'API
echo "<h2>2. Vérification de l'URL de l'API</h2>";
if (defined('GEMINI_API_URL') && !empty(GEMINI_API_URL)) {
    echo "✅ URL API configurée: " . GEMINI_API_URL . "<br>";
} else {
    echo "❌ URL API non configurée<br>";
}

// Test 3: Test d'appel API simple
echo "<h2>3. Test d'appel API Gemini</h2>";

$apiKey = GEMINI_API_KEY;
$url = GEMINI_API_URL . '?key=' . $apiKey;

$prompt = "Écris un court paragraphe de test pour un rapport de chantier.";

$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 500,
    ]
];

echo "Envoi de la requête à Gemini...<br>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "Code HTTP: $httpCode<br>";

if ($response === false) {
    echo "❌ Erreur cURL: $curlError<br>";
} else {
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];
            echo "✅ <strong>Réponse de Gemini reçue avec succès!</strong><br><br>";
            echo "<div style='background: #f0f9ff; padding: 15px; border-left: 4px solid #2563eb; margin: 10px 0;'>";
            echo "<strong>Texte généré:</strong><br>";
            echo nl2br(htmlspecialchars($generatedText));
            echo "</div>";
        } else {
            echo "❌ Format de réponse invalide<br>";
            echo "<pre>" . htmlspecialchars(print_r($result, true)) . "</pre>";
        }
    } else {
        echo "❌ Erreur HTTP $httpCode<br>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

// Test 4: Vérifier la connexion à la base de données
echo "<h2>4. Vérification de la base de données</h2>";
try {
    $db = new Database();
    $pdo = $db->connect();
    echo "✅ Connexion à la base de données réussie<br>";
    
    // Vérifier la table reports
    $stmt = $pdo->query("SHOW TABLES LIKE 'reports'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'reports' existe<br>";
    } else {
        echo "❌ Table 'reports' n'existe pas<br>";
    }
    
    // Vérifier la table projects
    $stmt = $pdo->query("SHOW TABLES LIKE 'projects'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'projects' existe<br>";
        
        // Vérifier les nouvelles colonnes
        $stmt = $pdo->query("SHOW COLUMNS FROM projects LIKE 'maitre_ouvrage'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Colonne 'maitre_ouvrage' existe<br>";
        } else {
            echo "⚠️ Colonne 'maitre_ouvrage' n'existe pas (exécutez add_project_fields.sql)<br>";
        }
        
        $stmt = $pdo->query("SHOW COLUMNS FROM projects LIKE 'missions_controle'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Colonne 'missions_controle' existe<br>";
        } else {
            echo "⚠️ Colonne 'missions_controle' n'existe pas (exécutez add_project_fields.sql)<br>";
        }
    } else {
        echo "❌ Table 'projects' n'existe pas<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>Résumé</h2>";
echo "<p>Si tous les tests sont ✅, le système de génération de rapports devrait fonctionner correctement.</p>";
echo "<p>Si vous voyez des ❌, corrigez les problèmes indiqués.</p>";
?>
