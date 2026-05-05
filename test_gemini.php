<?php
/**
 * Script de test pour l'API Gemini
 */

require_once 'config/config.php';

echo "<h1>Test de l'API Gemini</h1>";

// Vérifier la configuration
echo "<h2>Configuration</h2>";
echo "Clé API Gemini : " . (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY) ? "✅ Configurée" : "❌ Non configurée") . "<br>";
echo "URL API Gemini : " . (defined('GEMINI_API_URL') ? GEMINI_API_URL : "❌ Non définie") . "<br>";

if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
    echo "<p style='color: red;'>❌ Clé API Gemini non configurée. Vérifiez le fichier config/config.php</p>";
    exit;
}

// Test simple de l'API
echo "<h2>Test de l'API</h2>";

$prompt = "Tu es un expert en génie civil. Génère un court rapport de chantier en français pour un projet de construction d'un bâtiment. Inclus les sections : Résumé, Activités, Observations. Maximum 200 mots.";

$apiKey = GEMINI_API_KEY;
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
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo "❌ Erreur cURL : $curlError<br>";
} elseif ($httpCode !== 200) {
    echo "❌ Erreur HTTP $httpCode<br>";
    echo "<pre>Réponse : " . htmlspecialchars($response) . "</pre>";
} else {
    $result = json_decode($response, true);
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];
        echo "✅ <strong>API Gemini fonctionne parfaitement !</strong><br><br>";
        echo "<h3>Rapport généré :</h3>";
        echo "<div style='border: 1px solid #ddd; padding: 15px; background: #f9f9f9; border-radius: 5px;'>";
        echo nl2br(htmlspecialchars($generatedText));
        echo "</div>";
    } else {
        echo "❌ Réponse Gemini invalide<br>";
        echo "<pre>Réponse complète : " . htmlspecialchars($response) . "</pre>";
    }
}

echo "<br><h2>Actions suivantes</h2>";
echo "<p>Si le test fonctionne :</p>";
echo "<ol>";
echo "<li>Votre clé API Gemini est valide et fonctionnelle</li>";
echo "<li>Vous pouvez maintenant utiliser le système de génération de rapports</li>";
echo "<li><a href='?action=reports/select-project'>Aller à la génération de rapports</a></li>";
echo "</ol>";

echo "<p>Si le test échoue :</p>";
echo "<ol>";
echo "<li>Vérifiez que votre clé API est correcte</li>";
echo "<li>Vérifiez votre connexion internet</li>";
echo "<li>Consultez les logs d'erreur</li>";
echo "</ol>";

echo "<br><a href='debug_reports.php'>Diagnostic complet</a> | ";
echo "<a href='?action=dashboard'>Retour au dashboard</a>";
?>