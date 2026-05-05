<?php
/**
 * Contrôleur des rapports optimisé pour Gemini API
 */

require_once ROOT_PATH . '/app/models/Report.php';
require_once ROOT_PATH . '/app/models/Project.php';
require_once ROOT_PATH . '/app/models/SiteData.php';

class ReportControllerGemini {
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
     * Traiter la génération d'un rapport via Gemini API (priorité) puis fallback
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
        $siteDataList = $this->siteDataModel->getDataForReport($project_id);
        error_log("DEBUG: Site data count: " . count($siteDataList));

        // Récupérer les informations du brouillon
        $draft = $_SESSION['report_draft'][$project_id] ?? [];

        // Vérifier qu'il y a des données à traiter
        if (empty($siteDataList) && empty($additionalNotes) && empty($draft)) {
            setFlash('error', 'Ajoutez des données de chantier, des informations de projet ou des notes avant de générer le rapport.');
            redirect("reports/generate&project_id=$project_id");
        }

        // Préparer les données pour le prompt
        $dataText = '';
        foreach ($siteDataList as $data) {
            $dataText .= "[{$data['data_type']}] {$data['content']}\n";
        }

        // Ajouter les informations du brouillon
        $draftText = $this->formatDraftData($draft);

        // Construire le prompt optimisé pour Gemini
        $prompt = $this->buildGeminiPrompt($project, $dataText, $draftText, $report_type, $additionalNotes);
        error_log("DEBUG: Prompt built, trying Gemini API first");

        $reportContent = null;

        // Essayer d'abord avec Gemini (gratuit et performant)
        $geminiResponse = $this->callGeminiAPI($prompt);
        if ($geminiResponse !== false) {
            $reportContent = $geminiResponse;
            error_log("DEBUG: Gemini API successful, content length: " . strlen($reportContent));
            setFlash('success', 'Rapport généré avec succès via Gemini AI !');
        } else {
            error_log("DEBUG: Gemini API failed, using basic report generation");
            
            // Générer un rapport de base si Gemini échoue
            $reportContent = $this->generateBasicReport($project, $dataText, $draftText, $additionalNotes);
            error_log("DEBUG: Using basic report generation");
            setFlash('success', 'Rapport généré avec le système de base (Gemini indisponible).');
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
            // Rediriger vers la liste des rapports avec le modal
            redirect("reports?show_modal=$reportId");
        } else {
            error_log("DEBUG: Failed to save report to database");
            setFlash('error', 'Erreur lors de la sauvegarde du rapport');
            redirect("reports/generate&project_id=$project_id");
        }
    }

    /**
     * Construire un prompt optimisé pour Gemini
     */
    private function buildGeminiPrompt($project, $dataText, $draftText, $report_type, $notes) {
        $date = date('d/m/Y');
        
        $prompt = "Tu es un expert en génie civil spécialisé dans la rédaction de rapports de chantier professionnels.\n\n";
        $prompt .= "MISSION : Génère un rapport journalier détaillé et professionnel en français.\n\n";
        
        $prompt .= "=== INFORMATIONS DU PROJET ===\n";
        $prompt .= "• Nom du projet : {$project['name']}\n";
        $prompt .= "• Localisation : {$project['location']}\n";
        $prompt .= "• Description : {$project['description']}\n";
        $prompt .= "• Date du rapport : $date\n";
        $prompt .= "• Date de début : " . date('d/m/Y', strtotime($project['start_date'])) . "\n\n";
        
        if (!empty($draftText)) {
            $prompt .= "=== INFORMATIONS DE CHANTIER ===\n";
            $prompt .= $draftText . "\n";
        }
        
        if (!empty($dataText)) {
            $prompt .= "=== DONNÉES TERRAIN COLLECTÉES ===\n";
            $prompt .= $dataText . "\n";
        }
        
        if (!empty($notes)) {
            $prompt .= "=== NOTES ADDITIONNELLES ===\n";
            $prompt .= $notes . "\n\n";
        }
        
        $prompt .= "=== STRUCTURE DEMANDÉE ===\n";
        $prompt .= "Organise le rapport avec ces sections obligatoires :\n\n";
        $prompt .= "# RAPPORT DE CHANTIER - $date\n\n";
        $prompt .= "## 1. RÉSUMÉ EXÉCUTIF\n";
        $prompt .= "## 2. CONDITIONS GÉNÉRALES\n";
        $prompt .= "## 3. RESSOURCES MOBILISÉES\n";
        $prompt .= "## 4. ACTIVITÉS RÉALISÉES\n";
        $prompt .= "## 5. PROBLÈMES RENCONTRÉS\n";
        $prompt .= "## 6. SOLUTIONS APPORTÉES\n";
        $prompt .= "## 7. AVANCEMENT DU PROJET\n";
        $prompt .= "## 8. OBSERVATIONS ET RECOMMANDATIONS\n\n";
        
        $prompt .= "=== INSTRUCTIONS SPÉCIFIQUES ===\n";
        $prompt .= "• Utilise un langage professionnel et technique approprié\n";
        $prompt .= "• Sois précis et factuel, base-toi uniquement sur les données fournies\n";
        $prompt .= "• Inclus des détails quantitatifs quand disponibles\n";
        $prompt .= "• Mentionne les conditions météorologiques et leur impact\n";
        $prompt .= "• Détaille l'utilisation des équipements et du personnel\n";
        $prompt .= "• Propose des recommandations constructives\n";
        $prompt .= "• Utilise le format Markdown pour la structure\n\n";
        
        $prompt .= "Génère maintenant un rapport complet et professionnel.";
        
        return $prompt;
    }

    /**
     * Formater les données du brouillon
     */
    private function formatDraftData($draft) {
        if (empty($draft)) return '';
        
        $text = '';
        
        if (!empty($draft['weather'])) {
            $text .= "• Conditions météorologiques : {$draft['weather']}\n";
        }
        
        if (!empty($draft['equipments'])) {
            $text .= "\n• ÉQUIPEMENTS SUR LE CHANTIER :\n";
            foreach ($draft['equipments'] as $eq) {
                $text .= "  - {$eq['designation']} : {$eq['present']} présent(s), {$eq['marche']} en marche, {$eq['immob']} immobilisé(s), {$eq['panne']} en panne\n";
            }
        }
        
        if (!empty($draft['personnels'])) {
            $text .= "\n• PERSONNEL MOBILISÉ :\n";
            $total = 0;
            foreach ($draft['personnels'] as $pers) {
                $text .= "  - {$pers['profile']} : {$pers['nbr']} personne(s)\n";
                $total += $pers['nbr'];
            }
            $text .= "  Total personnel : $total personne(s)\n";
        }
        
        if (!empty($draft['materials'])) {
            $text .= "\n• MATÉRIAUX UTILISÉS :\n";
            foreach ($draft['materials'] as $mat) {
                $text .= "  - {$mat['designation']} : {$mat['quantite']} {$mat['unite']}\n";
            }
        }
        
        return $text;
    }

    /**
     * Appeler l'API Gemini pour générer le contenu
     */
    private function callGeminiAPI($prompt) {
        $apiKey = GEMINI_API_KEY;
        if (empty($apiKey)) {
            error_log("DEBUG: Gemini API key not configured");
            return false;
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
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];

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
            error_log("DEBUG: Gemini API cURL error: $curlError");
            return false;
        }

        if ($httpCode !== 200) {
            error_log("DEBUG: Gemini API HTTP error $httpCode: $response");
            return false;
        }

        $result = json_decode($response, true);
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }

        error_log("DEBUG: Gemini API invalid response: $response");
        return false;
    }

    /**
     * Générer un rapport de base sans IA
     */
    private function generateBasicReport($project, $dataText, $draftText, $notes) {
        $date = date('d/m/Y');
        
        $report = "# RAPPORT DE CHANTIER - $date\n\n";
        $report .= "**Projet :** {$project['name']}\n";
        $report .= "**Localisation :** {$project['location']}\n";
        $report .= "**Date :** $date\n\n";
        
        $report .= "## 1. RÉSUMÉ EXÉCUTIF\n\n";
        $report .= "Rapport journalier des activités du chantier {$project['name']}.\n\n";
        
        $report .= "## 2. CONDITIONS GÉNÉRALES\n\n";
        if (!empty($draftText)) {
            $report .= $draftText . "\n";
        } else {
            $report .= "Conditions normales de travail.\n\n";
        }
        
        $report .= "## 3. ACTIVITÉS RÉALISÉES\n\n";
        if (!empty($dataText)) {
            $report .= "### Données collectées :\n";
            $report .= $dataText . "\n";
        }
        
        if (!empty($notes)) {
            $report .= "### Notes additionnelles :\n";
            $report .= $notes . "\n\n";
        }
        
        if (empty($dataText) && empty($notes)) {
            $report .= "Activités de chantier selon le planning établi.\n\n";
        }
        
        $report .= "## 4. OBSERVATIONS\n\n";
        $report .= "- Travaux effectués dans les conditions normales\n";
        $report .= "- Respect des mesures de sécurité\n";
        $report .= "- Mobilisation des ressources selon les besoins\n\n";
        
        $report .= "## 5. RECOMMANDATIONS\n\n";
        $report .= "- Poursuivre les activités selon le planning\n";
        $report .= "- Maintenir la vigilance sur la sécurité\n";
        $report .= "- Surveiller l'évolution des conditions météorologiques\n\n";
        
        $report .= "---\n";
        $report .= "*Rapport généré automatiquement le $date*\n";
        $report .= "*Système de génération de base utilisé*";
        
        return $report;
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
}
?>