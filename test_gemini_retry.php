<?php
/**
 * Test de l'API Gemini avec retry automatique
 */

require_once 'config/config.php';
require_once 'app/services/GeminiAPI.php';

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }";
echo ".success { border: 2px solid #28a745; padding: 15px; background: #d4edda; border-radius: 5px; margin: 15px 0; }";
echo ".error { border: 2px solid #dc3545; padding: 15px; background: #f8d7da; border-radius: 5px; margin: 15px 0; }";
echo ".quota-error { border: 2px solid #ff6b6b; padding: 15px; background: #ffe0e0; border-radius: 5px; margin: 15px 0; }";
echo ".code { background: #f4f4f4; padding: 10px; border-radius: 3px; font-family: monospace; overflow-x: auto; }";
echo "h3 { margin-top: 0; }";
echo "a { color: #007bff; text-decoration: none; }";
echo "a:hover { text-decoration: underline; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>Test de l'API Gemini avec Retry Automatique</h1>";

$gemini = new GeminiAPI(GEMINI_API_KEY, GEMINI_API_URL);

$prompt = "Tu es un expert en génie civil. Génère un court rapport de chantier en français. "
        . "Inclus : Résumé, Activités, Observations. Maximum 150 mots.";

echo "<p>🚀 Appel à l'API Gemini (max 3 tentatives)...</p>";
echo "<hr>";

$result = $gemini->call($prompt, 500, 0.7);

echo "<h2>Résultat</h2>";

if ($result['success']) {
    echo "<div class='success'>";
    echo "<h3>✅ Succès après " . $result['attempts'] . " tentative(s)</h3>";
    echo "<pre class='code'>";
    echo htmlspecialchars($result['data']);
    echo "</pre>";
    echo "</div>";
} else {
    // Vérifier si c'est une erreur de quota
    if (isset($result['quota_exceeded']) && $result['quota_exceeded']) {
        echo "<div class='quota-error'>";
        echo "<h3>❌ ERREUR DE QUOTA DÉPASSÉ</h3>";
        echo "<p><strong>" . htmlspecialchars($result['error']) . "</strong></p>";
        echo "<p>Votre quota API Gemini est dépassé. Consultez : <a href='diagnostic_quota_api.php'>Diagnostic Quota</a></p>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Erreur après " . $result['attempts'] . " tentative(s)</h3>";
        echo "<p><strong>" . htmlspecialchars($result['error']) . "</strong></p>";
    }
    
    if (isset($result['errorDetails']) && is_array($result['errorDetails'])) {
        echo "<div class='code'>";
        echo "<strong>Détails techniques :</strong><br>";
        echo json_encode($result['errorDetails'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo "</div>";
    }
    
    echo "</div>";
}

echo "<hr>";
echo "<h3>Navigation</h3>";
echo "<ul>";
echo "<li><a href='diagnostic_connexion.php'>← Diagnostic Connexion</a></li>";
echo "<li><a href='diagnostic_quota_api.php'>🔍 Diagnostic Quota API</a></li>";
echo "</ul>";
echo "</div>";
echo "</body>";
echo "</html>";
?>

