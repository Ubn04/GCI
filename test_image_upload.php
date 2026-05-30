<?php
/**
 * Test d'upload d'image avec compression
 * Vérifie que les images peuvent être traitées sans timeout
 */

require_once 'config/config.php';
require_once 'app/services/GeminiService.php';

// Obtenir la clé API
$apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : 'test_key';
$geminiService = new GeminiService($apiKey);

// Créer une image de test (1000x1000 pixels, 3MB)
$testImagePath = 'uploads/temp/test_large_image.jpg';
if (!file_exists('uploads/temp')) {
    mkdir('uploads/temp', 0755, true);
}

// Créer une image de test
$image = imagecreatetruecolor(2000, 2000);
$backgroundColor = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $backgroundColor);

// Ajouter du texte
$textColor = imagecolorallocate($image, 0, 0, 0);
imagestring($image, 5, 100, 100, 'Test Image for Gemini Chat', $textColor);

// Sauvegarder en JPEG
imagejpeg($image, $testImagePath, 90);
imagedestroy($image);

$fileSizeBeforeMB = filesize($testImagePath) / (1024 * 1024);

echo "<h2>Test d'upload d'image avec compression</h2>";
echo "<p>Taille de l'image avant compression: " . round($fileSizeBeforeMB, 2) . " MB</p>";

// Tester avec un message et image
echo "<h3>Envoi à Gemini...</h3>";
$startTime = microtime(true);

try {
    $result = $geminiService->sendMultimodalMessage(
        "Analyse cette image et décris ce que tu vois.",
        $testImagePath,
        'image/jpeg'
    );
    
    $endTime = microtime(true);
    $duration = $endTime - $startTime;
    
    echo "<p>Temps d'exécution: " . round($duration, 2) . " secondes</p>";
    
    if ($result['success']) {
        echo "<div class='alert alert-success'>";
        echo "<h4>✓ Succès!</h4>";
        echo "<p>Réponse Gemini:</p>";
        echo "<pre>" . htmlspecialchars($result['data']) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>";
        echo "<h4>✗ Erreur</h4>";
        echo "<p>" . htmlspecialchars($result['message']) . "</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>Exception</h4>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

// Nettoyer
if (file_exists($testImagePath)) {
    unlink($testImagePath);
}

echo "<p><a href='index.php'>Retour accueil</a></p>";
?>
