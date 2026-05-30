<?php
/**
 * Contrôleur Chat IA - Gestion des messages multimodaux avec Gemini
 * Responsable du routage, validation et traitement des requêtes de chat
 */

class ChatAIController
{
    private $pdo;
    private $gemini;

    /**
     * Constructeur
     * @param PDO $pdo Connexion base de données
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        
        // Initialiser le service Gemini
        $apiKey = getenv('GEMINI_API_KEY');
        if (!$apiKey) {
            $apiKey = (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : null);
        }
        
        if (!$apiKey) {
            throw new Exception('Clé API Gemini non configurée. Ajoutez GEMINI_API_KEY dans config/config.php');
        }
        
        require_once ROOT_PATH . '/app/services/GeminiService.php';
        $this->gemini = new GeminiService($apiKey);
    }

    /**
     * Traiter un message de chat multimodal (texte + image)
     * Cette méthode :
     * 1. Valide l'authentification
     * 2. Traite l'image si présente
     * 3. Appelle Gemini avec texte + image ensemble
     * 4. Retourne la réponse en JSON
     */
    public function sendMessage()
    {
        try {
            // Augmenter le timeout PHP pour les traitements longs
            set_time_limit(300);

            // Vérifier l'authentification
            if (!isset($_SESSION['user_id'])) {
                return $this->jsonResponse(false, 'Non authentifié', null, 401);
            }

            // Vérifier la méthode de requête
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->jsonResponse(false, 'Méthode non autorisée', null, 405);
            }

            // Récupérer les données POST
            $message = trim($_POST['message'] ?? '');
            $originalMessage = $message;
            $projectId = $_POST['project_id'] ?? null;

            // Valider le projet
            if (!$projectId) {
                return $this->jsonResponse(false, 'ID projet requis', null, 400);
            }

            // Vérifier que l'utilisateur a accès au projet
            if (!$this->userHasProjectAccess($projectId)) {
                return $this->jsonResponse(false, 'Accès refusé au projet', null, 403);
            }

            $imagePath = null;
            $mimeType = 'image/jpeg';

            // Traiter l'image si elle a été envoyée
            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                $imageResult = $this->gemini->processUploadedImage($_FILES['image']);
                
                if (!$imageResult['success']) {
                    return $this->jsonResponse(false, $imageResult['message'], null, 400);
                }

                $imagePath = $imageResult['path'];
                $mimeType = $imageResult['mimeType'];
            }

            // Traiter l'audio si elle a été envoyée
            if (isset($_FILES['audio']) && $_FILES['audio']['size'] > 0) {
                // Valider le fichier audio
                $audioFile = $_FILES['audio'];
                
                if ($audioFile['size'] > 25 * 1024 * 1024) {
                    return $this->jsonResponse(false, 'Fichier audio trop volumineux (max 25MB)', null, 400);
                }

                $audioMimeType = $audioFile['type'];
                if (!in_array($audioMimeType, ['audio/webm', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mpeg', 'audio/m4a'])) {
                    return $this->jsonResponse(false, 'Format audio non supporté', null, 400);
                }

                // Déplacer le fichier vers uploads/temp
                $uploadsDir = ROOT_PATH . '/uploads/temp/';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }

                $audioFilename = 'audio_' . uniqid() . '_' . basename($audioFile['name']);
                $audioPath = $uploadsDir . $audioFilename;

                if (!move_uploaded_file($audioFile['tmp_name'], $audioPath)) {
                    return $this->jsonResponse(false, 'Erreur lors du téléchargement du fichier audio', null, 500);
                }

                // Ajouter une note au message
                $message = ($message ? $message . ' ' : '') . '[Message audio envoyé]';
            }

            // Récupérer les informations du projet pour le contexte
            $projectInfo = $this->getProjectInfo($projectId);
            $projectContext = "";
            if ($projectInfo) {
                $projectContext = "Contexte du projet : {$projectInfo['name']} - {$projectInfo['location']} - Type : {$projectInfo['project_type']} - Description : {$projectInfo['description']} - Maître d'ouvrage : {$projectInfo['maitre_ouvrage']} - Missions contrôle : {$projectInfo['missions_controle']}. ";
            }
            $chatContext = '';
            if (empty($imagePath) && !empty($message)) {
                $chatContext = $this->getRecentChatContext($projectId);
            }

            // Ajouter un contexte genie civil au message utilisateur.
            // Une image envoyee seule doit etre analysee sans description obligatoire.
            if (!empty($imagePath) && empty($message)) {
                $message = $projectContext
                    . "Analyse rapidement l'image fournie pour un rapport journalier de chantier. "
                    . "Reponds en 3 points courts : 1) elements visibles, 2) etat/avancement probable, 3) risque ou recommandation. "
                    . "Ne demande pas de description et distingue les faits visibles des hypotheses.";
            } elseif (!empty($message)) {
                $message = $projectContext . $chatContext . "Dans le contexte du génie civil (bâtiment, routes, ponts, infrastructures) : " . $message;
            }

            // Envoyer la requête multimodale à Gemini
            // Texte et image sont envoyés ENSEMBLE dans une seule requête
            $geminiOptions = !empty($imagePath)
                ? ['maxOutputTokens' => 420, 'temperature' => 0.1]
                : [];
            $geminiResponse = $this->gemini->sendMultimodalMessage($message, $imagePath, $mimeType, $geminiOptions);

            // Nettoyer le fichier temporaire
            if ($imagePath) {
                $this->gemini->deleteTemporaryFile($imagePath);
            }

            // Nettoyer le fichier audio temporaire
            if (isset($audioPath) && file_exists($audioPath)) {
                unlink($audioPath);
            }

            // Vérifier le résultat de Gemini
            if (!$geminiResponse['success']) {
                error_log('DEBUG: Gemini error in chat: ' . print_r($geminiResponse, true));
                return $this->jsonResponse(false, 'Erreur API : ' . $geminiResponse['message'], null, 500);
            }

            // Vérifier que la réponse contient les données attendues
            if (!isset($geminiResponse['data']) || !is_array($geminiResponse['data'])) {
                error_log('DEBUG: Gemini response malformed: ' . print_r($geminiResponse, true));
                return $this->jsonResponse(false, 'Format de réponse invalide', null, 500);
            }

            // Extraire la réponse texte
            $responseText = $geminiResponse['data']['response'] ?? null;
            if (empty($responseText)) {
                error_log('DEBUG: Gemini returned empty response: ' . print_r($geminiResponse, true));
                return $this->jsonResponse(false, 'Gemini a retourné une réponse vide', null, 500);
            }

            // Sauvegarder une trace lisible pour le rapport, sans le prompt technique interne.
            $savedUserMessage = $originalMessage;
            if (!empty($imagePath) && empty($originalMessage)) {
                $savedUserMessage = '[Image envoyee pour analyse]';
            } elseif (!empty($imagePath)) {
                $savedUserMessage = '[Image envoyee pour analyse] ' . $originalMessage;
            }

            $this->saveChatMessage($projectId, $savedUserMessage, $responseText);

            // Retourner la réponse
            return $this->jsonResponse(
                true,
                'Message traité avec succès',
                [
                    'response' => $responseText,
                    'finishReason' => $geminiResponse['data']['finishReason'] ?? null,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            );

        } catch (Exception $e) {
            error_log('Erreur ChatAI : ' . $e->getMessage());
            return $this->jsonResponse(false, 'Erreur serveur : ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Vérifier si l'utilisateur a accès au projet
     * @param int $projectId ID du projet
     * @return bool
     */
    private function userHasProjectAccess($projectId)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT id FROM projects 
                WHERE id = ? AND user_id = ?
                LIMIT 1
            ');
            
            $stmt->execute([$projectId, $_SESSION['user_id']]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Sauvegarder le message de chat dans la base de données
     * @param int $projectId ID du projet
     * @param string $userMessage Message utilisateur
     * @param string $aiResponse Réponse IA
     */
    private function saveChatMessage($projectId, $userMessage, $aiResponse)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO chat_messages (project_id, user_id, user_message, ai_response, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ');
            
            // Créer la table si elle n'existe pas (migration)
            $this->ensureChatMessagesTable();
            
            $stmt->execute([
                $projectId,
                $_SESSION['user_id'],
                $userMessage,
                $aiResponse
            ]);
        } catch (Exception $e) {
            // Log mais ne pas bloquer la réponse
            error_log('Erreur sauvegarde chat : ' . $e->getMessage());
        }
    }

    /**
     * S'assurer que la table chat_messages existe
     */
    private function ensureChatMessagesTable()
    {
        try {
            $this->pdo->exec('
                CREATE TABLE IF NOT EXISTS chat_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    project_id INT NOT NULL,
                    user_id INT NOT NULL,
                    user_message LONGTEXT,
                    ai_response LONGTEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (project_id) REFERENCES projects(id),
                    FOREIGN KEY (user_id) REFERENCES users(id),
                    INDEX (project_id, created_at)
                )
            ');
        } catch (Exception $e) {
            // Table existe déjà ou erreur ignorée
        }
    }

    /**
     * Récupérer les informations d'un projet
     * @param int $projectId ID du projet
     * @return array|null Informations du projet
     */
    private function getProjectInfo($projectId)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT name, location, project_type, description, maitre_ouvrage, missions_controle
                FROM projects
                WHERE id = ? AND user_id = ?
                LIMIT 1
            ');

            $stmt->execute([$projectId, $_SESSION['user_id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Recuperer un court contexte de conversation pour les messages texte.
     */
    private function getRecentChatContext($projectId)
    {
        try {
            $this->ensureChatMessagesTable();

            $stmt = $this->pdo->prepare('
                SELECT user_message, ai_response
                FROM chat_messages
                WHERE project_id = ? AND user_id = ?
                ORDER BY created_at DESC
                LIMIT 6
            ');

            $stmt->execute([$projectId, $_SESSION['user_id']]);
            $messages = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
            if (empty($messages)) {
                return '';
            }

            $context = "Conversation recente utile :\n";
            foreach ($messages as $message) {
                $userMessage = $this->summarizeChatContextLine($message['user_message'] ?? '');
                $aiResponse = $this->summarizeChatContextLine($message['ai_response'] ?? '');

                if ($userMessage !== '') {
                    $context .= "Utilisateur : {$userMessage}\n";
                }
                if ($aiResponse !== '') {
                    $context .= "Assistant : {$aiResponse}\n";
                }
            }

            return $context . "\n";
        } catch (Exception $e) {
            error_log('Erreur contexte chat : ' . $e->getMessage());
            return '';
        }
    }

    private function summarizeChatContextLine($text)
    {
        $text = trim(strip_tags((string) $text));
        $text = preg_replace('/\s+/', ' ', $text);

        if (strlen($text) > 600) {
            $text = substr($text, 0, 600) . '...';
        }

        return $text;
    }

    /**
     * Obtenir l'historique des messages d'un projet
     * Retourne les 50 derniers messages du chat
     */
    public function getHistory()
    {
        try {
            // Vérifier l'authentification
            if (!isset($_SESSION['user_id'])) {
                return $this->jsonResponse(false, 'Non authentifié', null, 401);
            }

            $projectId = $_GET['project_id'] ?? null;

            if (!$projectId || !$this->userHasProjectAccess($projectId)) {
                return $this->jsonResponse(false, 'Accès refusé', null, 403);
            }

            // S'assurer que la table existe
            $this->ensureChatMessagesTable();

            $stmt = $this->pdo->prepare('
                SELECT id, user_message, ai_response, created_at
                FROM chat_messages
                WHERE project_id = ?
                ORDER BY created_at DESC
                LIMIT 50
            ');
            
            $stmt->execute([$projectId]);
            $messages = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

            return $this->jsonResponse(
                true,
                'Historique récupéré',
                $messages
            );

        } catch (Exception $e) {
            error_log('Erreur historique chat : ' . $e->getMessage());
            return $this->jsonResponse(false, 'Erreur serveur', null, 500);
        }
    }

    /**
     * Répondre en JSON et terminer l'exécution
     */
    private function jsonResponse($success, $message, $data = null, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
