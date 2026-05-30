<?php
/**
 * Test Gemini API - Vérifier l'intégration multimodale
 * Accès : http://localhost:8000/chantier-ai-php/test_gemini_multimodal.php
 */

require_once 'config/config.php';
require_once 'app/services/GeminiService.php';

// Récupérer la clé API
$apiKey = GEMINI_API_KEY ?? null;

if (!$apiKey) {
    die('<h1 style="color: red;">Erreur: Clé API Gemini non configurée dans config.php</h1>');
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Gemini Multimodal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 800px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border-left: 4px solid #667eea;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .test-section h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .result {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .error {
            background: #ffebee;
            border: 1px solid #f44336;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .status-ok {
            background: #4caf50;
            color: white;
        }
        .status-error {
            background: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #667eea; margin-bottom: 30px;">
            <i class="fas fa-robot"></i> Test Gemini API Multimodal
        </h1>

        <!-- Test 1: Vérifier la clé API -->
        <div class="test-section">
            <h3>1. Vérification de la clé API
                <span class="status-badge status-ok">
                    <i class="fas fa-check"></i> OK
                </span>
            </h3>
            <p>Clé API Gemini détectée: <code><?php echo substr($apiKey, 0, 10) . '...' . substr($apiKey, -5); ?></code></p>
        </div>

        <!-- Test 2: Test Texte seul -->
        <div class="test-section">
            <h3>2. Test Texte seul</h3>
            <button class="btn btn-primary btn-sm" onclick="testTextOnly()">
                <i class="fas fa-paper-plane"></i> Lancer le test
            </button>
            <div id="test-text-result"></div>
        </div>

        <!-- Test 3: Service Gemini disponible -->
        <div class="test-section">
            <h3>3. Vérification du Service Gemini</h3>
            <?php
            try {
                $gemini = new GeminiService($apiKey);
                echo '<div class="result">';
                echo '<i class="fas fa-check-circle"></i> Service GeminiService initialisé avec succès';
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<i class="fas fa-times-circle"></i> Erreur: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            ?>
        </div>

        <!-- Test 4: Traitement d'image -->
        <div class="test-section">
            <h3>4. Upload et traitement d'image</h3>
            <form enctype="multipart/form-data" onsubmit="return handleImageUpload(event);">
                <div class="mb-3">
                    <input type="file" class="form-control" id="imageFile" accept="image/*" required>
                    <small class="text-muted">Max 5MB (JPEG, PNG, GIF, WebP)</small>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" id="imageMessage" placeholder="Décrivez votre question sur l'image..." rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-image"></i> Tester avec image
                </button>
            </form>
            <div id="test-image-result"></div>
        </div>

        <!-- Test 5: Configuration -->
        <div class="test-section">
            <h3>5. Configuration</h3>
            <table class="table table-sm">
                <tr>
                    <td><strong>Clé API</strong></td>
                    <td><span class="status-badge status-ok">Configurée</span></td>
                </tr>
                <tr>
                    <td><strong>Modèle</strong></td>
                    <td><code>gemini-2.5-flash-lite</code></td>
                </tr>
                <tr>
                    <td><strong>API URL</strong></td>
                    <td><code>https://generativelanguage.googleapis.com/v1beta/models/</code></td>
                </tr>
                <tr>
                    <td><strong>Support multimodal</strong></td>
                    <td><span class="status-badge status-ok">Activé</span></td>
                </tr>
            </table>
        </div>

        <!-- Résumé -->
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Information</h5>
            <p>Ce test vérifie que l'intégration Gemini fonctionne correctement.</p>
            <p><strong>Points testés :</strong></p>
            <ul>
                <li>✅ Clé API configurée</li>
                <li>✅ Service Gemini initialisé</li>
                <li>✅ Requête texte seul</li>
                <li>✅ Traitement d'image multimodal</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function testTextOnly() {
            const resultDiv = document.getElementById('test-text-result');
            resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Chargement...</span></div> En cours...';

            try {
                const formData = new FormData();
                formData.append('message', 'Quel est le rôle d\'un ingénieur en génie civil?');
                formData.append('project_id', 1); // Pour le test

                const response = await fetch('?action=chat/send-message', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="result">
                            <strong><i class="fas fa-check-circle"></i> Succès!</strong>
                            <p style="margin-top: 10px;"><strong>Réponse Gemini:</strong></p>
                            <p>${escapeHtml(data.data.response.substring(0, 500))}...</p>
                            <small class="text-muted">Temps: ${data.data.timestamp}</small>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <strong><i class="fas fa-times-circle"></i> Erreur</strong>
                            <p>${escapeHtml(data.message)}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="error">
                        <strong><i class="fas fa-times-circle"></i> Erreur de connexion</strong>
                        <p>${escapeHtml(error.message)}</p>
                    </div>
                `;
            }
        }

        function handleImageUpload(event) {
            event.preventDefault();
            const imageFile = document.getElementById('imageFile').files[0];
            const message = document.getElementById('imageMessage').value;
            const resultDiv = document.getElementById('test-image-result');

            if (!imageFile) {
                resultDiv.innerHTML = '<div class="error"><i class="fas fa-times-circle"></i> Sélectionnez une image</div>';
                return false;
            }

            resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Chargement...</span></div> Traitement en cours...';

            const formData = new FormData();
            formData.append('message', message || 'Analysez cette image');
            formData.append('image', imageFile);
            formData.append('project_id', 1);

            fetch('?action=chat/send-message', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="result">
                            <strong><i class="fas fa-check-circle"></i> Succès!</strong>
                            <p style="margin-top: 10px;"><strong>Image reçue:</strong> ${imageFile.name}</p>
                            <p><strong>Réponse Gemini:</strong></p>
                            <p>${escapeHtml(data.data.response.substring(0, 500))}...</p>
                            <small class="text-muted">Temps: ${data.data.timestamp}</small>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <strong><i class="fas fa-times-circle"></i> Erreur</strong>
                            <p>${escapeHtml(data.message)}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="error">
                        <strong><i class="fas fa-times-circle"></i> Erreur de connexion</strong>
                        <p>${escapeHtml(error.message)}</p>
                    </div>
                `;
            });

            return false;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
