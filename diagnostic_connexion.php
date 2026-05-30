<?php
/**
 * Diagnostic complet de la connexion internet et API Gemini
 */

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }";
echo ".test { margin: 15px 0; padding: 10px; border-left: 4px solid #ddd; background: #fafafa; }";
echo ".pass { border-left-color: #28a745; background: #d4edda; }";
echo ".fail { border-left-color: #dc3545; background: #f8d7da; }";
echo ".warning { border-left-color: #ffc107; background: #fff3cd; }";
echo "h1, h2 { color: #333; }";
echo "code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-size: 12px; }";
echo "pre { background: #f4f4f4; padding: 10px; border-radius: 3px; overflow-x: auto; }";
echo ".spinner { display: inline-block; animation: spin 1s linear infinite; }";
echo "@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>🔧 Diagnostic Connexion Internet & API Gemini</h1>";
echo "<p>Analyse de votre environnement local et des connexions API</p>";

// 1. Vérifier la version PHP
echo "<div class='test pass'>";
echo "<strong>✅ PHP Version:</strong> " . phpversion() . "<br>";
echo "</div>";

// 2. Vérifier cURL
echo "<div class='test " . (extension_loaded('curl') ? 'pass' : 'fail') . "'>";
echo "<strong>" . (extension_loaded('curl') ? "✅" : "❌") . " cURL Extension:</strong> ";
echo extension_loaded('curl') ? "Installée" : "NON INSTALLÉE";
echo "</div>";

// 3. Vérifier OpenSSL
echo "<div class='test " . (extension_loaded('openssl') ? 'pass' : 'fail') . "'>";
echo "<strong>" . (extension_loaded('openssl') ? "✅" : "❌") . " OpenSSL:</strong> ";
echo extension_loaded('openssl') ? "Installée" : "NON INSTALLÉE";
echo "</div>";

// 4. Test de connexion à Google
echo "<h2>🌐 Tests de Connectivité</h2>";

echo "<div class='test'>";
echo "<strong>Test 1: Ping DNS Google (8.8.8.8)</strong><br>";
echo "<div style='margin-top: 10px;'>";

// Utiliser gethostbyname pour vérifier la résolution DNS
$googleIP = gethostbyname('google.com');
if ($googleIP !== 'google.com') {
    echo "<span class='pass'>✅ Résolution DNS: <code>google.com</code> → <code>$googleIP</code></span>";
} else {
    echo "<span class='fail'>❌ Résolution DNS: Impossible de résoudre <code>google.com</code></span>";
}
echo "</div>";
echo "</div>";

echo "<div class='test'>";
echo "<strong>Test 2: Résolution de generativelanguage.googleapis.com</strong><br>";
echo "<div style='margin-top: 10px;'>";

$geminHost = gethostbyname('generativelanguage.googleapis.com');
if ($geminHost !== 'generativelanguage.googleapis.com') {
    echo "<span class='pass'>✅ Résolution DNS: <code>generativelanguage.googleapis.com</code> → <code>$geminHost</code></span>";
} else {
    echo "<span class='fail'>❌ Résolution DNS: Impossible de résoudre <code>generativelanguage.googleapis.com</code></span>";
}
echo "</div>";
echo "</div>";

echo "<div class='test'>";
echo "<strong>Test 3: Connexion HTTPS à Google API</strong><br>";
echo "<div style='margin-top: 10px;'>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response !== false) {
    echo "<span class='pass'>✅ Connexion réussie (HTTP $httpCode)</span>";
} else {
    echo "<span class='fail'>❌ Erreur cURL: <code>$curlError</code></span>";
}
echo "</div>";
echo "</div>";

// 5. Test API Gemini
echo "<h2>🤖 Test API Gemini</h2>";

require_once 'config/config.php';

if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
    echo "<div class='test fail'>";
    echo "<strong>❌ Clé API Gemini:</strong> Non configurée";
    echo "</div>";
} else {
    echo "<div class='test pass'>";
    echo "<strong>✅ Clé API Gemini:</strong> Configurée";
    echo "</div>";
    
    echo "<div class='test'>";
    echo "<strong>Test: Appel à l'API Gemini</strong><br>";
    echo "<div style='margin-top: 10px;'>";
    
    $apiKey = GEMINI_API_KEY;
    $url = GEMINI_API_URL . '?key=' . $apiKey;
    
    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Bonjour']
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 100,
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($response === false) {
        echo "<span class='fail'>❌ Erreur cURL: <code>$curlError</code></span>";
    } elseif ($httpCode === 200) {
        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            echo "<span class='pass'>✅ API Gemini fonctionne!</span>";
        } else {
            echo "<span class='warning'>⚠️ Réponse HTTP 200 mais format invalide</span>";
            echo "<pre>" . htmlspecialchars(substr($response, 0, 200)) . "</pre>";
        }
    } else {
        echo "<span class='fail'>❌ Erreur HTTP $httpCode</span>";
        echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
    }
    echo "</div>";
    echo "</div>";
}

echo "<h2>📋 Résumé & Solutions</h2>";

$geminIP = gethostbyname('generativelanguage.googleapis.com');
$canConnect = $geminIP !== 'generativelanguage.googleapis.com';

if (!$canConnect) {
    echo "<div class='test fail'>";
    echo "<h3>❌ Problème: Pas de connexion internet</h3>";
    echo "<p><strong>Solutions:</strong></p>";
    echo "<ol>";
    echo "<li>Vérifiez que votre ordinateur est connecté à internet</li>";
    echo "<li>Vérifiez que le firewall/antivirus ne bloque pas PHP/XAMPP</li>";
    echo "<li>Redémarrez Apache et MySQL dans XAMPP</li>";
    echo "<li>Testez la connexion avec: <code>ping 8.8.8.8</code></li>";
    echo "<li>Vérifiez les DNS de votre système (paramètres réseau)</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div class='test pass'>";
    echo "<h3>✅ Connexion internet active</h3>";
    echo "<p>Votre serveur peut accéder à internet. Si l'API Gemini ne fonctionne pas, vérifiez :</p>";
    echo "<ol>";
    echo "<li>La clé API est correcte et valide</li>";
    echo "<li>Vous n'avez pas dépassé le quota API</li>";
    echo "<li>La clé API a les permissions correctes</li>";
    echo "</ol>";
    echo "</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
?>
