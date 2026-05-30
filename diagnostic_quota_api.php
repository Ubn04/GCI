<?php
/**
 * Diagnostic du quota API Gemini
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
echo ".info { margin: 15px 0; padding: 15px; border-left: 4px solid; border-radius: 3px; }";
echo ".info.error { border-left-color: #dc3545; background: #f8d7da; }";
echo ".info.warning { border-left-color: #ffc107; background: #fff3cd; }";
echo ".info.success { border-left-color: #28a745; background: #d4edda; }";
echo ".code { background: #f4f4f4; padding: 10px; border-radius: 3px; font-family: monospace; margin: 10px 0; overflow-x: auto; }";
echo "h1, h2 { color: #333; }";
echo "a { color: #007bff; text-decoration: none; }";
echo "a:hover { text-decoration: underline; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>🔍 Diagnostic Quota API Gemini</h1>";

echo "<div class='info warning'>";
echo "<h3>ℹ️ Comment vérifier votre quota</h3>";
echo "<ol>";
echo "<li><strong>Allez sur:</strong> <a href='https://console.cloud.google.com' target='_blank'>Google Cloud Console</a></li>";
echo "<li><strong>Connectez-vous</strong> avec votre compte Google</li>";
echo "<li><strong>Sélectionnez votre projet</strong> (en haut à gauche)</li>";
echo "<li><strong>Allez à:</strong> APIs & Services → Quotas</li>";
echo "<li><strong>Cherchez:</strong> \"Generative Language API\"</li>";
echo "<li><strong>Vérifiez:</strong> Quota limit vs Usage</li>";
echo "</ol>";
echo "</div>";

echo "<h2>📊 Codes d'erreur et significations</h2>";

echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th>Code</th>";
echo "<th>Signification</th>";
echo "<th>Action</th>";
echo "</tr>";

$errors = [
    200 => ['✅ OK', 'Requête réussie'],
    400 => ['❌ Bad Request', 'Format de requête invalide'],
    401 => ['❌ Unauthorized', 'Clé API invalide ou manquante'],
    403 => ['❌ Forbidden', 'Permission refusée ou QUOTA DÉPASSÉ'],
    429 => ['❌ Too Many Requests', 'QUOTA DÉPASSÉ - Trop de requêtes'],
    500 => ['❌ Server Error', 'Erreur serveur Google'],
    503 => ['❌ Service Unavailable', 'Serveur surchargé (temporaire)'],
];

foreach ($errors as $code => $details) {
    echo "<tr>";
    echo "<td><strong>$code</strong></td>";
    echo "<td>" . $details[0] . "<br><small>" . $details[1] . "</small></td>";
    echo "<td>";
    if ($code === 429 || $code === 403) {
        echo "Vérifiez votre quota sur Google Cloud";
    } elseif ($code === 503) {
        echo "Attendez quelques minutes";
    } elseif ($code === 401) {
        echo "Vérifiez votre clé API";
    } else {
        echo "Contactez le support";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>🧪 Test d'appel API</h2>";
echo "<p>Faire un test pour vérifier l'état actuel :</p>";

$gemini = new GeminiAPI(GEMINI_API_KEY, GEMINI_API_URL);
$result = $gemini->call("Test simple", 100, 0.5);

if ($result['success']) {
    echo "<div class='info success'>";
    echo "<strong>✅ API Fonctionnelle</strong>";
    echo "<p>Votre quota est OK, vous pouvez faire des appels.</p>";
    echo "</div>";
} else {
    echo "<div class='info " . (isset($result['quota_exceeded']) && $result['quota_exceeded'] ? 'error' : 'warning') . "'>";
    
    if (isset($result['quota_exceeded']) && $result['quota_exceeded']) {
        echo "<strong>❌ QUOTA DÉPASSÉ</strong>";
        echo "<p>" . htmlspecialchars($result['error']) . "</p>";
    } else {
        echo "<strong>⚠️ Erreur API</strong>";
        echo "<p>" . htmlspecialchars($result['error']) . "</p>";
    }
    
    if (isset($result['errorDetails'])) {
        echo "<div class='code'>";
        echo "<strong>Détails:</strong><br>";
        echo json_encode($result['errorDetails'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo "</div>";
    }
    echo "</div>";
}

echo "<h2>💡 Solutions si le quota est dépassé</h2>";

echo "<div class='info warning'>";
echo "<h3>Option 1 : Attendre</h3>";
echo "<p>Les quotas se réinitialisent généralement :</p>";
echo "<ul>";
echo "<li>Quotas par minute : réinitialisés toutes les minutes</li>";
echo "<li>Quotas par jour : réinitialisés à minuit (heure du serveur)</li>";
echo "<li>Quotas par mois : réinitialisés le 1er du mois</li>";
echo "</ul>";
echo "</div>";

echo "<div class='info warning'>";
echo "<h3>Option 2 : Augmenter le quota</h3>";
echo "<p>Sur Google Cloud Console :</p>";
echo "<ol>";
echo "<li>Allez à APIs & Services → Quotas</li>";
echo "<li>Sélectionnez \"Generative Language API\"</li>";
echo "<li>Cliquez sur \"EDIT QUOTAS\" (en haut)</li>";
echo "<li>Augmentez les limites (peut nécessiter une carte de crédit valide)</li>";
echo "<li>Cliquez \"NEXT\" et \"CREATE TICKET\"</li>";
echo "</ol>";
echo "</div>";

echo "<div class='info warning'>";
echo "<h3>Option 3 : Utiliser un autre modèle</h3>";
echo "<p>Certains modèles ont des quotas différents :</p>";
echo "<ul>";
echo "<li><code>gemini-2.5-flash-lite</code> (gratuit, basique)</li>";
echo "<li><code>gemini-2.5-flash</code> (gratuit, meilleur)</li>";
echo "<li><code>gemini-2.5-pro</code> (payant, très performant)</li>";
echo "</ul>";
echo "</div>";

echo "<h2>📝 Configuration actuelle</h2>";
echo "<div class='code'>";
echo "Clé API: " . (strlen(GEMINI_API_KEY) > 10 ? substr(GEMINI_API_KEY, 0, 10) . "..." : "***") . "<br>";
echo "URL: " . GEMINI_API_URL . "<br>";
echo "</div>";

echo "<p><a href='diagnostic_connexion.php'>← Retour au diagnostic</a></p>";
echo "</div>";
echo "</body>";
echo "</html>";
?>
