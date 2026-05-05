<?php
/**
 * Contrôleur des rapports - Génération et gestion des rapports
 */

require_once ROOT_PATH . '/app/models/Report.php';
require_once ROOT_PATH . '/app/models/Project.php';
require_once ROOT_PATH . '/app/models/SiteData.php';

class ReportController {
    private $reportModel;
    private $projectModel;
    private $siteDataModel;

    public function __construct($pdo) {
        $this->reportModel = new Report($pdo);
        $this->projectModel = new Project($pdo);
        $this->siteDataModel = new SiteData($pdo);
    }

    /**
     * Afficher la liste des rapports
     */
    public function index() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $search = trim($_GET['q'] ?? '');
        $reports = $this->reportModel->getByUserId($user_id, $search);
        $error = getFlash('error');
        $success = getFlash('success');
        
        require VIEWS_PATH . '/reports/index.php';
    }

    /**
     * Afficher la page de sélection d'un projet avant génération
     */
    public function selectProject() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $projects = $this->projectModel->getByUserId($user_id);
        $error = getFlash('error');
        $success = getFlash('success');

        require VIEWS_PATH . '/reports/select_project.php';
    }

    /**
     * Afficher la page d'information chantier pour un projet sélectionné
     */
    public function projectInfo($project_id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];

        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            redirect('projects');
        }

        $project = $this->projectModel->getById($project_id);
        $draft = $_SESSION['report_draft'][$project_id] ?? [
            'weather' => 'Ensoleille',
            'equipments' => [],
            'personnels' => [],
            'materials' => []
        ];

        require VIEWS_PATH . '/reports/project_info.php';
    }

    /**
     * Enregistrer le brouillon de la page d'information chantier
     */
    public function saveDraft($project_id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            exit;
        }

        $_SESSION['report_draft'][$project_id] = [
            'weather' => $data['weather'] ?? 'Ensoleille',
            'equipments' => array_values(array_map(function ($item) {
                return [
                    'designation' => trim($item['designation'] ?? ''),
                    'present' => trim($item['present'] ?? ''),
                    'marche' => trim($item['marche'] ?? ''),
                    'immob' => trim($item['immob'] ?? ''),
                    'panne' => trim($item['panne'] ?? ''),
                ];
            }, $data['equipments'] ?? [])),
            'personnels' => array_values(array_map(function ($item) {
                return [
                    'profile' => trim($item['profile'] ?? ''),
                    'nbr' => intval($item['nbr'] ?? 0),
                ];
            }, $data['personnels'] ?? [])),
            'materials' => array_values(array_map(function ($item) {
                return [
                    'designation' => trim($item['designation'] ?? ''),
                    'unite' => trim($item['unite'] ?? ''),
                    'quantite' => intval($item['quantite'] ?? 0),
                ];
            }, $data['materials'] ?? [])),
        ];

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Afficher le formulaire de génération de rapport
     */
    public function generate($project_id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        // Vérifier que le projet appartient à l'utilisateur
        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            redirect('projects');
        }

        $project = $this->projectModel->getById($project_id);
        $siteData = $this->siteDataModel->getByProjectId($project_id);
        $draft = $_SESSION['report_draft'][$project_id] ?? [
            'weather' => 'Ensoleille',
            'equipments' => [],
            'personnels' => [],
            'materials' => []
        ];

        $error = getFlash('error');
        require VIEWS_PATH . '/reports/generate.php';
    }

    /**
     * Traiter la génération d'un rapport via Gemini API
     */
    public function handleGenerate($project_id) {
        // Capturer les erreurs PHP
        $_SESSION['debug_errors'] = [];

        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            $_SESSION['debug_errors'][] = "PHP Error [$errno]: $errstr in $errfile:$errline";
        });

        set_exception_handler(function($exception) {
            $_SESSION['debug_errors'][] = "PHP Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine();
        });

        error_log("DEBUG: handleGenerate called with project_id: $project_id");
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("DEBUG: Not a POST request, redirecting");
            redirect("reports/generate&project_id=$project_id");
        }

        $user_id = $_SESSION['user_id'];
        error_log("DEBUG: User ID: $user_id");
        $report_type = $_POST['report_type'] ?? 'daily';
        $additionalNotes = trim($_POST['notes'] ?? '');

        error_log("DEBUG: Report type: $report_type, Notes length: " . strlen($additionalNotes));

        // Vérifier que le projet appartient à l'utilisateur
        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            error_log("DEBUG: User is not owner of project $project_id");
            redirect('projects');
        }

        $project = $this->projectModel->getById($project_id);
        error_log("DEBUG: Project loaded: " . ($project ? $project['name'] : 'null'));
        $siteDataList = $this->siteDataModel->getDataForReport($project_id);
        error_log("DEBUG: Site data count: " . count($siteDataList));

        $uploadedFiles = [];
        $audioTranscripts = [];
        if (isset($_FILES['media'])) {
            error_log("DEBUG: Files uploaded: " . count($_FILES['media']['name']));
            foreach ($_FILES['media']['tmp_name'] as $index => $tmpName) {
                if (empty($tmpName) || $_FILES['media']['error'][$index] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $file = [
                    'name' => $_FILES['media']['name'][$index],
                    'type' => $_FILES['media']['type'][$index],
                    'tmp_name' => $tmpName,
                    'size' => $_FILES['media']['size'][$index],
                ];

                if (strpos($file['type'], 'audio/') === 0) {
                    error_log("DEBUG: Processing audio file: " . $file['name']);
                    $transcript = $this->transcribeAudioFile($file);
                    if ($transcript) {
                        $audioTranscripts[] = $transcript;
                        error_log("DEBUG: Audio transcript: " . substr($transcript, 0, 100) . "...");
                    }
                }

                $fileUrl = $this->uploadMediaFile($file);
                $uploadedFiles[] = [
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'url' => $fileUrl,
                    'size' => $file['size'],
                    'description' => $this->getFileDescription($file)
                ];
            }
        }

        error_log("DEBUG: Uploaded files count: " . count($uploadedFiles));
        error_log("DEBUG: Audio transcripts count: " . count($audioTranscripts));

        if (!empty($audioTranscripts)) {
            $transcriptionText = implode("\n\n", $audioTranscripts);
            if (!empty($additionalNotes)) {
                $additionalNotes .= "\n\nTranscription audio :\n" . $transcriptionText;
            } else {
                $additionalNotes = "Transcription audio :\n" . $transcriptionText;
            }
        }

        if (empty($siteDataList) && empty($additionalNotes) && empty($uploadedFiles)) {
            setFlash('error', 'Ajoutez un texte, un fichier ou des données de chantier avant de générer le rapport.');
            redirect("reports/generate&project_id=$project_id");
        }

        // Préparer les données pour le prompt
        $dataText = '';
        foreach ($siteDataList as $data) {
            $dataText .= "[{$data['data_type']}] {$data['content']}\n";
        }

        $attachmentsText = '';
        foreach ($uploadedFiles as $file) {
            $attachmentsText .= "- {$file['description']}\n";
        }

        $messages = $this->buildOpenAIMessages($project, $dataText, $report_type, $additionalNotes, $attachmentsText, $uploadedFiles);
        error_log("DEBUG: Messages built, calling OpenAI API");
        $response = $this->callOpenAIAPI($messages);
        error_log("DEBUG: OpenAI API response received: " . json_encode($response));
        $reportContent = null;

        if (!$response['success']) {
            error_log("DEBUG: OpenAI API failed: " . $response['error']);
            // Si l'accès à GPT-4 est refusé, essaye un modèle de secours
            if (stripos($response['error'], 'gpt-4') !== false || stripos($response['error'], 'model') !== false || stripos($response['error'], '403') !== false || stripos($response['error'], '401') !== false) {
                error_log("DEBUG: Trying fallback to GPT-3.5-turbo");
                $fallback = $this->callOpenAIAPI($messages, 'gpt-3.5-turbo');
                if ($fallback['success']) {
                    $reportContent = $fallback['content'];
                    error_log("DEBUG: Fallback successful, content length: " . strlen($reportContent));
                    setFlash('success', 'Rapport généré avec le modèle de secours GPT-3.5 car GPT-4 n’était pas disponible.');
                } else {
                    error_log("DEBUG: Fallback also failed: " . $fallback['error']);
                    setFlash('error', 'Erreur OpenAI : ' . $response['error'] . ' | fallback : ' . $fallback['error']);
                    redirect("reports/generate&project_id=$project_id");
                }
            } else {
                setFlash('error', 'Erreur OpenAI : ' . $response['error']);
                redirect("reports/generate&project_id=$project_id");
            }
        } else {
            $reportContent = $response['content'];
            error_log("DEBUG: OpenAI API successful, content length: " . strlen($reportContent));
        }

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
     * Afficher un rapport
     */
    public function show($id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        $report = $this->reportModel->getById($id);
        
        if (!$report || $report['user_id'] != $user_id) {
            redirect('reports');
        }

        require VIEWS_PATH . '/reports/show.php';
    }

    /**
     * Afficher les rapports mensuels
     */
    public function monthly() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $monthlyReports = $this->reportModel->getMonthlySummaryByUserId($user_id, $year);
        $selectedYear = $year;

        require VIEWS_PATH . '/reports/monthly.php';
    }

    /**
     * Afficher les rapports annuels
     */
    public function yearly() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $yearlyReports = $this->reportModel->getAnnualSummaryByUserId($user_id);

        require VIEWS_PATH . '/reports/yearly.php';
    }

    /**
     * Supprimer un rapport
     */
    public function delete($id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        $report = $this->reportModel->getById($id);
        
        if (!$report || $report['user_id'] != $user_id) {
            redirect('reports');
        }

        if ($this->reportModel->delete($id)) {
            setFlash('success', 'Rapport supprimé avec succès !');
        } else {
            setFlash('error', 'Erreur lors de la suppression');
        }
        
        redirect('reports');
    }

    /**
     * Construire le prompt pour Gemini
     */
    private function buildPrompt($project, $dataText, $report_type) {
        $date = date('d/m/Y');
        
        if ($report_type === 'daily') {
            return "Tu es un expert en génie civil spécialisé dans la rédaction de rapports de chantier. 
            
Génère un rapport journalier professionnel en français basé sur les données suivantes :

**Projet:** {$project['name']}
**Localisation:** {$project['location']}
**Description:** {$project['description']}
**Date:** $date

**Données terrain collectées:**
$dataText

**Structure le rapport avec les sections suivantes:**
1. Résumé de la journée
2. Activités réalisées
3. Problèmes rencontrés
4. Solutions apportées
5. Niveau d'avancement estimé
6. Observations et recommandations

Sois professionnel, précis et structuré.";
        } elseif ($report_type === 'monthly') {
            return "Tu es un expert en génie civil. Génère un rapport mensuel professionnel en français.

**Projet:** {$project['name']}
**Localisation:** {$project['location']}

**Données du mois:**
$dataText

Structure le rapport avec : résumé global, évolution du projet, problèmes majeurs, et recommandations.";
        } else {
            return "Tu es un expert en génie civil. Génère un rapport annuel professionnel en français.

**Projet:** {$project['name']}
**Localisation:** {$project['location']}

**Données de l'année:**
$dataText

Structure le rapport avec : résumé annuel, évolution générale, défis majeurs, et perspectives futures.";
        }
    }

    /**
     * Appeler l'API Gemini pour générer le contenu
     */
    private function callGeminiAPI($prompt) {
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

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return false;
        }

        $result = json_decode($response, true);
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }

        return false;
    }

    private function buildOpenAIMessages($project, $dataText, $report_type, $notes, $attachmentsText, $uploadedFiles) {
        $projectSummary = "Projet : {$project['name']}\nLocalisation : {$project['location']}\nDescription : {$project['description']}\n";
        $projectSummary .= "Date de début : " . date('d/m/Y', strtotime($project['start_date'])) . "\n";

        $siteDataSection = "";
        if (!empty($dataText)) {
            $siteDataSection = "Données terrain :\n" . $dataText . "\n";
        }

        $attachmentsSection = "";
        if (!empty($attachmentsText)) {
            $attachmentsSection = "Fichiers joints (images, vidéos, audios, documents) :\n" . $attachmentsText . "\n";
        }

        $notesSection = "";
        if (!empty($notes)) {
            $notesSection = "Notes du terrain :\n" . $notes . "\n";
        }

        $systemMessage = "Tu es un expert en gestion de chantier et rédaction de rapports professionnels. Ta mission est de créer des rapports de chantier complets, structurés et utiles pour les chefs de projet, les clients et les équipes.

INSTRUCTIONS IMPORTANTES :
1. Analyse toutes les informations fournies (données terrain, fichiers joints, notes)
2. Structure le rapport avec ces sections OBLIGATOIRES :
   - RÉSUMÉ EXÉCUTIF (synthèse des points clés)
   - SITUATION ACTUELLE (état d'avancement, conditions météo, effectifs)
   - TRAVAUX RÉALISÉS (activités accomplies, matériaux utilisés)
   - PROBLÈMES RENCONTRES (difficultés techniques, imprévus)
   - SOLUTIONS APPORTÉES (résolutions, adaptations)
   - PROCHAINES ÉTAPES (planning, objectifs)
   - RECOMMANDATIONS (améliorations, précautions)

3. Sois précis et factuel - base-toi uniquement sur les données fournies
4. Si une information manque, indique-le clairement plutôt que d'inventer
5. Utilise un ton professionnel mais accessible
6. Formate le texte avec des titres clairs et des listes quand approprié

Le rapport doit être en français, complet et directement utilisable.";
        ;

        $userMessage = "RAPPORT DE CHANTIER - " . strtoupper($report_type) . "\n\n" .
            "INFORMATIONS DU PROJET :\n" . $projectSummary . "\n" .
            $siteDataSection .
            $attachmentsSection .
            $notesSection .
            "INSTRUCTIONS SPÉCIFIQUES :\n" .
            "- Type de rapport demandé : " . ucfirst($report_type) . "\n" .
            "- Analyse tous les fichiers joints (images, vidéos, audios, documents)\n" .
            "- Les images montrent probablement l'état du chantier, les travaux en cours, ou des problèmes spécifiques\n" .
            "- Les vidéos et audios contiennent des observations importantes ou des démonstrations\n" .
            "- Les documents joints apportent des informations techniques complémentaires\n\n" .
            "RÉDIGE UN RAPPORT COMPLET ET PROFESSIONNEL en français, structuré selon les sections demandées dans les instructions système.";

        return [
            ['role' => 'system', 'content' => $systemMessage],
            ['role' => 'user', 'content' => $userMessage]
        ];
    }

    private function transcribeAudioFile($file) {
        $apiKey = OPENAI_API_KEY;
        if (empty($apiKey)) {
            return false;
        }

        $url = 'https://api.openai.com/v1/audio/transcriptions';
        $curlFile = curl_file_create($file['tmp_name'], $file['type'], $file['name']);

        $postData = [
            'model' => 'whisper-1',
            'file' => $curlFile
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            error_log("OpenAI Whisper curl error: " . $curlError);
            return false;
        }

        if ($httpCode !== 200) {
            error_log("OpenAI Whisper HTTP Error - HTTP Code: $httpCode, Response: $response");
            return false;
        }

        $result = json_decode($response, true);
        return $result['text'] ?? false;
    }

    private function callOpenAIAPI($messages, $model = OPENAI_MODEL) {
        $apiKey = OPENAI_API_KEY;
        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'Clé OpenAI non définie'];
        }

        $url = OPENAI_API_URL;
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 3000
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
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
            error_log("OpenAI API curl error: " . $curlError);
            return ['success' => false, 'error' => 'Erreur de connexion OpenAI : ' . $curlError];
        }

        if ($httpCode !== 200) {
            $result = json_decode($response, true);
            $errorMessage = $result['error']['message'] ?? "HTTP $httpCode";
            error_log("OpenAI API Error - HTTP Code: $httpCode, Response: $response");
            return ['success' => false, 'error' => $errorMessage, 'http_code' => $httpCode];
        }

        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            return ['success' => true, 'content' => $result['choices'][0]['message']['content']];
        }

        if (isset($result['error'])) {
            error_log("OpenAI API Error: " . json_encode($result['error']));
            return ['success' => false, 'error' => $result['error']['message'] ?? 'Erreur inconnue OpenAI'];
        }

        return ['success' => false, 'error' => 'Réponse OpenAI invalide ou vide'];
    }

    private function uploadMediaFile($file) {
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'video/mp4',
            'video/quicktime',
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'audio/webm',
            'application/pdf',
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $maxSize = 15 * 1024 * 1024; // 15 Mo

        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $uploadDir = PUBLIC_PATH . '/uploads/chatmedia';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('chat_') . '.' . ($extension ?: 'bin');
        $targetPath = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return false;
        }

        return APP_URL . '/public/uploads/chatmedia/' . $filename;
    }

    private function getFileDescription($file) {
        $type = $file['type'];
        $name = $file['name'];

        if (strpos($type, 'image/') === 0) {
            return "Image du chantier : {$name} - Cette image montre probablement des éléments visuels importants du site (matériel, travaux en cours, problèmes identifiés, progression des travaux, etc.)";
        } elseif (strpos($type, 'video/') === 0) {
            return "Vidéo du chantier : {$name} - Cette vidéo capture probablement des séquences importantes montrant l'activité sur le chantier, des démonstrations techniques, ou l'évolution des travaux";
        } elseif (strpos($type, 'audio/') === 0) {
            return "Enregistrement audio : {$name} - Cet audio contient probablement des commentaires vocaux, des observations importantes, ou des instructions relatives au chantier";
        } elseif (strpos($type, 'application/pdf') === 0) {
            return "Document PDF : {$name} - Ce document contient probablement des informations techniques, des plans, des rapports, ou des spécifications importantes pour le projet";
        } elseif (strpos($type, 'text/plain') === 0) {
            return "Document texte : {$name} - Ce fichier texte contient probablement des notes, des observations, ou des informations complémentaires sur le chantier";
        } elseif (strpos($type, 'application/msword') === 0 || strpos($type, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') === 0) {
            return "Document Word : {$name} - Ce document Word contient probablement des rapports détaillés, des spécifications, ou des informations structurées sur le projet";
        }

        return "Fichier joint : {$name} ({$type}) - Document complémentaire fourni pour ce rapport de chantier";
    }
}
?>
