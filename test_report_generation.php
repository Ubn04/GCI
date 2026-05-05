<?php
/**
 * Script de test pour la génération de rapports
 */

require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'app/models/Report.php';
require_once 'app/models/Project.php';
require_once 'app/models/SiteData.php';

// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Test de génération de rapport</h1>";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "❌ Vous devez être connecté pour tester la génération de rapports.<br>";
    echo "<a href='?action=auth/login'>Se connecter</a>";
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $db = new Database();
    $pdo = $db->connect();
    
    $projectModel = new Project($pdo);
    $siteDataModel = new SiteData($pdo);
    $reportModel = new Report($pdo);
    
    // Récupérer le premier projet de l'utilisateur
    $projects = $projectModel->getByUserId($user_id);
    
    if (empty($projects)) {
        echo "❌ Aucun projet trouvé. Créez d'abord un projet.<br>";
        echo "<a href='?action=projects/create'>Créer un projet</a>";
        exit;
    }
    
    $project = $projects[0];
    echo "✅ Projet sélectionné: " . htmlspecialchars($project['name']) . "<br>";
    
    // Créer des données de test si aucune n'existe
    $siteDataList = $siteDataModel->getDataForReport($project['id']);
    
    if (empty($siteDataList)) {
        echo "⚠️ Aucune donnée de site trouvée. Création de données de test...<br>";
        
        // Créer des données de test
        $testData = [
            'project_id' => $project['id'],
            'user_id' => $user_id,
            'content' => 'Test de données de chantier - Travaux de terrassement effectués',
            'data_type' => 'text'
        ];
        
        if ($siteDataModel->create($testData)) {
            echo "✅ Données de test créées<br>";
            $siteDataList = $siteDataModel->getDataForReport($project['id']);
        } else {
            echo "❌ Impossible de créer des données de test<br>";
        }
    }
    
    echo "✅ Données de site disponibles: " . count($siteDataList) . "<br>";
    
    // Préparer les données pour le prompt
    $dataText = '';
    foreach ($siteDataList as $data) {
        $dataText .= "[{$data['data_type']}] {$data['content']}\n";
    }
    
    echo "<h2>Données à envoyer à l'API:</h2>";
    echo "<pre>" . htmlspecialchars($dataText) . "</pre>";
    
    // Test avec un prompt simple
    $messages = [
        [
            'role' => 'system',
            'content' => 'Tu es un expert en génie civil. Génère un rapport de chantier professionnel en français basé sur les données fournies.'
        ],
        [
            'role' => 'user',
            'content' => "Génère un rapport journalier pour le projet: {$project['name']}\nLocalisation: {$project['location']}\nDonnées: $dataText"
        ]
    ];
    
    echo "<h2>Test de l'API Gemini</h2>";
    
    // Fonction de test de l'API Gemini
    function testGeminiAPI($prompt) {
        $apiKey = GEMINI_API_KEY;
        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'Clé Gemini non définie'];
        }
        
        $url = GEMINI_API_URL . '?key=' . $apiKey;
        
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
                'maxOutputTokens' => 1000
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false) {
            return ['success' => false, 'error' => 'Erreur cURL: ' . $curlError];
        }
        
        if ($httpCode !== 200) {
            return ['success' => false, 'error' => "HTTP $httpCode", 'response' => $response];
        }
        
        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return ['success' => true, 'content' => $result['candidates'][0]['content']['parts'][0]['text']];
        }
        
        return ['success' => false, 'error' => 'Réponse Gemini invalide', 'response' => $response];
    }
    
    // Créer un prompt simple pour le test
    $testPrompt = "Génère un rapport de chantier professionnel en français pour le projet: {$project['name']}\nLocalisation: {$project['location']}\nDonnées: $dataText\n\nStructure le rapport avec: Résumé, Activités réalisées, Observations.";
    
    // Test avec Gemini
    echo "Test avec l'API Gemini...<br>";
    $response = testGeminiAPI($testPrompt);
    
    if ($response['success']) {
        echo "✅ Gemini fonctionne parfaitement !<br>";
        echo "<h3>Rapport généré par Gemini:</h3>";
        echo "<div style='border: 1px solid #ccc; padding: 15px; background: #f9f9f9; border-radius: 8px;'>";
        echo nl2br(htmlspecialchars($response['content']));
        echo "</div>";
        
        // Essayer de sauvegarder le rapport
        $reportData = [
            'project_id' => $project['id'],
            'user_id' => $user_id,
            'title' => "Test Rapport Gemini - {$project['name']} - " . date('d/m/Y'),
            'content' => $response['content'],
            'report_type' => 'daily',
            'report_date' => date('Y-m-d')
        ];
        
        if ($reportId = $reportModel->create($reportData)) {
            echo "<br>✅ Rapport sauvegardé avec l'ID: $reportId<br>";
            echo "<a href='?action=reports/show&id=$reportId' class='btn btn-primary'>Voir le rapport</a><br>";
        } else {
            echo "<br>❌ Erreur lors de la sauvegarde du rapport<br>";
        }
        
    } else {
        echo "❌ Erreur avec Gemini: " . $response['error'] . "<br>";
        if (isset($response['response'])) {
            echo "Réponse complète: <pre>" . htmlspecialchars($response['response']) . "</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

echo "<br><h2>Actions disponibles:</h2>";
echo "<a href='debug_reports.php'>Diagnostic complet</a><br>";
echo "<a href='?action=reports/select-project'>Génération normale</a><br>";
echo "<a href='?action=dashboard'>Retour au dashboard</a><br>";
?>