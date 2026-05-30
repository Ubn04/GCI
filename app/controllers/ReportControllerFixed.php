<?php
/**
 * Version corrigée du contrôleur des rapports avec meilleure gestion des erreurs
 */

require_once ROOT_PATH . '/app/models/Report.php';
require_once ROOT_PATH . '/app/models/Project.php';
require_once ROOT_PATH . '/app/models/SiteData.php';

class ReportControllerFixed {
    private $reportModel;
    private $projectModel;
    private $siteDataModel;

    public function __construct($pdo) {
        $this->reportModel = new Report($pdo);
        $this->projectModel = new Project($pdo);
        $this->siteDataModel = new SiteData($pdo);
    }

    /**
     * Traiter la génération d'un rapport avec gestion d'erreurs améliorée
     */
    public function handleGenerate($project_id) {
        error_log("DEBUG: handleGenerate called with project_id: $project_id");
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("DEBUG: Not a POST request, redirecting");
            redirect("reports/generate&project_id=$project_id");
        }

        $user_id = $_SESSION['user_id'];
        $report_type = $_POST['report_type'] ?? 'daily';
        $additionalNotes = trim($_POST['notes'] ?? '');

        error_log("DEBUG: User ID: $user_id, Report type: $report_type");

        // Vérifier que le projet appartient à l'utilisateur
        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            error_log("DEBUG: User is not owner of project $project_id");
            setFlash('error', 'Accès refusé à ce projet.');
            redirect('projects');
        }

        $project = $this->projectModel->getById($project_id);
        if (!$project) {
            error_log("DEBUG: Project not found: $project_id");
            setFlash('error', 'Projet non trouvé.');
            redirect('projects');
        }

        error_log("DEBUG: Project loaded: " . $project['name']);

        // Récupérer les données de site
        $siteDataList = $this->siteDataModel->getDataForReport($project_id);
        error_log("DEBUG: Site data count: " . count($siteDataList));

        // Vérifier qu'il y a des données à traiter
        if (empty($siteDataList) && empty($additionalNotes)) {
            setFlash('error', 'Ajoutez des données de chantier ou des notes avant de générer le rapport.');
            redirect("reports/generate&project_id=$project_id");
        }

        // Préparer les données pour le prompt
        $dataText = '';
        foreach ($siteDataList as $data) {
            $dataText .= "[{$data['data_type']}] {$data['content']}\n";
        }

        // Construire le prompt
        $prompt = $this->buildSimplePrompt($project, $dataText, $report_type, $additionalNotes);
        error_log("DEBUG: Prompt built, length: " . strlen($prompt));

        // Essayer de générer le rapport
        $reportContent = $this->generateReportContent($prompt);

        if ($reportContent === false) {
            setFlash('error', 'Impossible de générer le rapport. Vérifiez la configuration de l\'API ou essayez plus tard.');
            redirect("reports/generate&project_id=$project_id");
        }

        // Sauvegarder le rapport
        $reportData = [
            'project_id' => $project_id,
            'user_id' => $user_id,
            'title' => "Rapport " . ucfirst($report_type) . " - {$project['name']} - " . date('d/m/Y'),
            'content' => $reportContent,
            'report_type' => $report_type,
            'report_date' => date('Y-m-d')
        ];

        if ($reportId = $this->reportModel->create($reportData)) {
            error_log("DEBUG: Report created successfully with ID: $reportId");
            setFlash('success', 'Rapport généré avec succès !');
            redirect("reports/show&id=$reportId");
        } else {
            error_log("DEBUG: Failed to save report to database");
            setFlash('error', 'Erreur lors de la sauvegarde du rapport');
            redirect("reports/generate&project_id=$project_id");
        }
    }

    /**
     * Construire un prompt simple et efficace incluant les messages du chat IA
     */
    private function buildSimplePrompt($project, $dataText, $report_type, $notes) {
        $date = date('d/m/Y');

        // Récupérer les messages du chat IA pour ce projet
        $chatMessages = $this->getChatMessagesForProject($project['id']);

        $prompt = "Tu es un expert en génie civil spécialisé dans la rédaction de rapports de chantier.\n\n";
        $prompt .= "Génère un rapport journalier professionnel en français basé sur les informations suivantes :\n\n";
        $prompt .= "**INFORMATIONS DU PROJET :**\n";
        $prompt .= "- Nom : {$project['name']}\n";
        $prompt .= "- Localisation : {$project['location']}\n";
        $prompt .= "- Description : {$project['description']}\n";
        $prompt .= "- Date du rapport : $date\n\n";

        if (!empty($dataText)) {
            $prompt .= "**DONNÉES DE TERRAIN :**\n$dataText\n";
        }

        if (!empty($notes)) {
            $prompt .= "**NOTES ADDITIONNELLES :**\n$notes\n\n";
        }

        // Inclure les messages du chat IA si disponibles
        if (!empty($chatMessages)) {
            $prompt .= "**CONVERSATION AVEC L'ASSISTANT IA :**\n";
            $prompt .= "Voici les échanges récents avec l'assistant IA spécialisé en génie civil :\n\n";

            foreach ($chatMessages as $message) {
                $timestamp = date('H:i', strtotime($message['created_at']));
                $prompt .= "[$timestamp] Utilisateur : {$message['user_message']}\n";
                $prompt .= "[$timestamp] IA : {$message['ai_response']}\n\n";
            }

            $prompt .= "Utilise ces échanges pour enrichir le rapport avec des informations techniques, des observations ou des recommandations issues de la conversation.\n\n";
        }

        $prompt .= "**INSTRUCTIONS :**\n";
        $prompt .= "Structure le rapport avec les sections suivantes :\n";
        $prompt .= "1. RÉSUMÉ DE LA JOURNÉE\n";
        $prompt .= "2. ACTIVITÉS RÉALISÉES\n";
        $prompt .= "3. PROBLÈMES RENCONTRÉS (si applicable)\n";
        $prompt .= "4. SOLUTIONS APPORTÉES (si applicable)\n";
        $prompt .= "5. AVANCEMENT DU PROJET\n";
        $prompt .= "6. OBSERVATIONS ET RECOMMANDATIONS\n\n";
        $prompt .= "Sois professionnel, précis et structuré. Utilise les données fournies pour créer un rapport détaillé et utile.";

        return $prompt;
    }

    /**
     * Récupérer les messages du chat IA pour un projet (derniers 24h)
     */
    private function getChatMessagesForProject($projectId) {
        try {
            $stmt = $this->pdo->prepare('
                SELECT user_message, ai_response, created_at
                FROM chat_messages
                WHERE project_id = ?
                AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                ORDER BY created_at ASC
                LIMIT 20
            ');

            $stmt->execute([$projectId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur récupération messages chat: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Générer le contenu du rapport avec Gemini en priorité
     */
    private function generateReportContent($prompt) {
        // Méthode 1: Essayer avec Gemini d'abord (votre clé API)
        if (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY)) {
            error_log("DEBUG: Trying Gemini API");
            $content = $this->tryGemini($prompt);
            if ($content !== false) {
                return $content;
            }
        }

        // Méthode 2: Essayer avec OpenAI si configuré
        if (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY)) {
            error_log("DEBUG: Trying OpenAI API");
            $content = $this->tryOpenAI($prompt);
            if ($content !== false) {
                return $content;
            }
        }

        // Méthode 3: Générer un rapport de base sans IA
        error_log("DEBUG: Using fallback report generation");
        return $this->generateFallbackReport($prompt);
    }

    /**
     * Essayer l'API OpenAI
     */
    private function tryOpenAI($prompt) {
        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $models = ['gpt-3.5-turbo', 'gpt-4']; // Essayer d'abord le moins cher
        
        foreach ($models as $model) {
            error_log("DEBUG: Trying OpenAI model: $model");
            $result = $this->callOpenAIAPI($messages, $model);
            
            if ($result['success']) {
                error_log("DEBUG: OpenAI success with model: $model");
                return $result['content'];
            } else {
                error_log("DEBUG: OpenAI failed with model $model: " . $result['error']);
            }
        }
        
        return false;
    }

    /**
     * Essayer l'API Gemini
     */
    private function tryGemini($prompt) {
        $apiKey = GEMINI_API_KEY;
        $url = GEMINI_API_URL . '?key=' . $apiKey;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }
        }

        error_log("DEBUG: Gemini API failed - HTTP $httpCode: $response");
        return false;
    }

    /**
     * Générer un rapport de base sans IA
     */
    private function generateFallbackReport($prompt) {
        $date = date('d/m/Y');
        
        $report = "# RAPPORT DE CHANTIER - $date\n\n";
        $report .= "**Note:** Ce rapport a été généré automatiquement car les services d'IA ne sont pas disponibles.\n\n";
        
        $report .= "## RÉSUMÉ DE LA JOURNÉE\n";
        $report .= "Activités de chantier réalisées selon les données collectées.\n\n";
        
        $report .= "## DONNÉES COLLECTÉES\n";
        
        // Extraire les informations du prompt
        if (preg_match('/\*\*DONNÉES DE TERRAIN :\*\*\n(.*?)\n\n/s', $prompt, $matches)) {
            $report .= $matches[1] . "\n\n";
        }
        
        if (preg_match('/\*\*NOTES ADDITIONNELLES :\*\*\n(.*?)\n\n/s', $prompt, $matches)) {
            $report .= "## NOTES ADDITIONNELLES\n";
            $report .= $matches[1] . "\n\n";
        }
        
        $report .= "## OBSERVATIONS\n";
        $report .= "- Travaux effectués selon le planning\n";
        $report .= "- Conditions météorologiques prises en compte\n";
        $report .= "- Équipements et personnel mobilisés\n\n";
        
        $report .= "## RECOMMANDATIONS\n";
        $report .= "- Poursuivre les activités selon le planning établi\n";
        $report .= "- Maintenir les mesures de sécurité\n";
        $report .= "- Surveiller l'évolution des conditions météorologiques\n\n";
        
        $report .= "---\n";
        $report .= "*Rapport généré automatiquement le $date*";
        
        return $report;
    }

    /**
     * Appeler l'API OpenAI
     */
    private function callOpenAIAPI($messages, $model = 'gpt-3.5-turbo') {
        $apiKey = OPENAI_API_KEY;
        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'Clé OpenAI non définie'];
        }

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 2000
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, OPENAI_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
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
            return ['success' => false, 'error' => 'Erreur de connexion: ' . $curlError];
        }

        if ($httpCode !== 200) {
            $result = json_decode($response, true);
            $errorMessage = $result['error']['message'] ?? "HTTP $httpCode";
            return ['success' => false, 'error' => $errorMessage];
        }

        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            return ['success' => true, 'content' => $result['choices'][0]['message']['content']];
        }

        return ['success' => false, 'error' => 'Réponse OpenAI invalide'];
    }
}
?>