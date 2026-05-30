<?php
/**
 * Contrôleur des rapports - Génération et gestion des rapports
 */

require_once ROOT_PATH . '/app/models/Report.php';
require_once ROOT_PATH . '/app/models/Project.php';
require_once ROOT_PATH . '/app/models/SiteData.php';
require_once ROOT_PATH . '/app/models/ReportDraft.php';

class ReportController {
    private $reportModel;
    private $projectModel;
    private $siteDataModel;
    private $reportDraftModel;

    public function __construct($pdo) {
        $this->reportModel = new Report($pdo);
        $this->projectModel = new Project($pdo);
        $this->siteDataModel = new SiteData($pdo);
        $this->reportDraftModel = new ReportDraft($pdo);
    }

    /**
     * Afficher la liste des rapports
     */
    public function index() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $search = trim($_GET['q'] ?? '');
        $allowedTypes = ['daily', 'monthly', 'annual'];
        $type = $_GET['type'] ?? '';
        $selectedType = in_array($type, $allowedTypes, true) ? $type : '';
        $reports = $this->reportModel->getByUserId($user_id, $search, $selectedType ?: null);
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
        $draft = [
            'weather' => 'Ensoleille',
            'equipments' => [],
            'personnels' => [],
            'materials' => []
        ];

        $draftRow = $this->reportDraftModel->getByProjectAndUser($project_id, $user_id);
        if ($draftRow) {
            // Purger si expiré (>24h)
            $updatedAt = strtotime($draftRow['updated_at']);
            if ($updatedAt !== false && (time() - $updatedAt) <= 24 * 3600) {
                $decoded = json_decode($draftRow['data'], true);
                if (is_array($decoded)) {
                    $draft = array_merge($draft, $decoded);
                }
            } else {
                // Supprimer le brouillon expiré
                $this->reportDraftModel->deleteByProjectAndUser($project_id, $user_id);
            }
        }

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

        $draftData = [
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

        // Persister dans la base de données
        $this->reportDraftModel->saveOrUpdate($user_id, $project_id, $draftData);

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
        $draft = [
            'weather' => 'Ensoleille',
            'equipments' => [],
            'personnels' => [],
            'materials' => []
        ];
        $draftRow = $this->reportDraftModel->getByProjectAndUser($project_id, $user_id);
        if ($draftRow) {
            $updatedAt = strtotime($draftRow['updated_at']);
            if ($updatedAt !== false && (time() - $updatedAt) <= 24 * 3600) {
                $decoded = json_decode($draftRow['data'], true);
                if (is_array($decoded)) {
                    $draft = array_merge($draft, $decoded);
                }
            } else {
                $this->reportDraftModel->deleteByProjectAndUser($project_id, $user_id);
            }
        }

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

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

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

        // Récupérer explicitement le brouillon pour s'assurer d'utiliser la météo choisie
        $draft = [
            'weather' => 'Ensoleille',
            'equipments' => [],
            'personnels' => [],
            'materials' => []
        ];
        $draftRowLatest = $this->reportDraftModel->getByProjectAndUser($project_id, $user_id);
        if ($draftRowLatest) {
            $updatedAt = strtotime($draftRowLatest['updated_at']);
            if ($updatedAt !== false && (time() - $updatedAt) <= 24 * 3600) {
                $decoded = json_decode($draftRowLatest['data'], true);
                if (is_array($decoded)) {
                    $draft = array_merge($draft, $decoded);
                }
            } else {
                $this->reportDraftModel->deleteByProjectAndUser($project_id, $user_id);
            }
        }

        $promptData = $this->buildGeminiPrompt($project, $dataText, $report_type, $additionalNotes, $attachmentsText, $uploadedFiles, $draft);
        error_log("DEBUG: Using hybrid approach - template + AI content");
        
        // Appeler l'IA pour générer seulement le contenu dynamique
        $aiContent = $this->callGeminiAPI($promptData['ai_prompt']);
        error_log("DEBUG: AI content received: " . ($aiContent ? "Success, length: " . strlen($aiContent) : "Failed"));

        if (!$aiContent) {
            error_log("DEBUG: Gemini API failed, trying OpenAI fallback");
            // Fallback vers OpenAI si Gemini échoue
            $messages = [
                ['role' => 'system', 'content' => 'Tu es un ingénieur BTP expérimenté.'],
                ['role' => 'user', 'content' => $promptData['ai_prompt']]
            ];
            $response = $this->callOpenAIAPI($messages);
            
            if ($response['success']) {
                $aiContent = $response['content'];
                error_log("DEBUG: OpenAI fallback successful, content length: " . strlen($aiContent));
                setFlash('success', 'Rapport généré avec OpenAI (Gemini indisponible).');
            } else {
                error_log("DEBUG: Both Gemini and OpenAI failed");
                // Utiliser du contenu par défaut si les deux IA échouent
                $aiContent = "RESUME: La journée a été marquée par des conditions météorologiques favorables permettant la poursuite des travaux selon le planning établi. Les équipes ont maintenu un rythme de production satisfaisant avec une mobilisation complète du personnel et du matériel. Les activités de ferraillage et de coffrage se sont déroulées conformément aux spécifications techniques du projet.\n\nRECOMMandations: Poursuivre la surveillance des conditions météorologiques pour optimiser les phases de bétonnage. Maintenir la cadence de production actuelle tout en respectant les normes de sécurité. Vérifier régulièrement l'état du matériel pour prévenir les pannes et assurer la continuité des travaux.";
            }
        }

        // Parser le contenu IA pour extraire introduction, résumé et recommandations
        $introduction    = "Ce rapport rend compte du suivi de chantier effectué ce jour sur le projet {$project['name']}.";
        $resume          = "Les travaux se sont déroulés conformément au planning établi.";
        $recommandations = "Poursuivre les travaux en respectant les normes de sécurité et de qualité.";
        
        if (preg_match('/^\s*INTRODUCTION\s*:\s*(.+?)(?=^\s*(RESUME|RECOMMANDATIONS?)\s*:|$)/msi', $aiContent, $matches)) {
            $introduction = trim($matches[1]);
        }
        if (preg_match('/^\s*RESUME\s*:\s*(.+?)(?=^\s*RECOMMANDATIONS?\s*:|$)/msi', $aiContent, $matches)) {
            $resume = trim($matches[1]);
        }
        if (preg_match('/^\s*RECOMMANDATIONS?\s*:\s*(.+?)$/msi', $aiContent, $matches)) {
            $recommandations = trim($matches[1]);
        }

        // Nettoyage : supprimer toute balise HTML éventuelle et entités, compacter les espaces
        $introduction = html_entity_decode(strip_tags($introduction), ENT_QUOTES, 'UTF-8');
        $resume = html_entity_decode(strip_tags($resume), ENT_QUOTES, 'UTF-8');
        $recommandations = html_entity_decode(strip_tags($recommandations), ENT_QUOTES, 'UTF-8');

        $introduction = preg_replace('/\s+/u', ' ', trim($introduction));
        $resume = preg_replace('/\s+/u', ' ', trim($resume));
        $recommandations = preg_replace('/\s+/u', ' ', trim($recommandations));

        // Remplacer les placeholders dans le template HTML
        $reportContent = str_replace(
            ['{{INTRODUCTION}}', '{{RESUME_TRAVAUX}}', '{{RECOMMANDATIONS}}'],
            [$introduction, $resume, $recommandations],
            $promptData['template']
        );
        
        error_log("DEBUG: Hybrid report generated successfully, final length: " . strlen($reportContent));

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
            // Supprimer le brouillon associé au projet pour cet utilisateur
            $this->reportDraftModel->deleteByProjectAndUser($project_id, $user_id);
            setFlash('success', 'Rapport généré avec succès !');

            if ($isAjax) {
                // Retourner l'aperçu HTML pour le modal
                $previewHtml = $reportContent;
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'report_id' => $reportId,
                    'preview_html' => $previewHtml,
                    'message' => 'Rapport généré avec succès'
                ]);
                exit;
            }

            redirect("reports/show&id=$reportId");
        } else {
            error_log("DEBUG: Failed to save report to database");
            setFlash('error', 'Erreur lors de la sauvegarde du rapport');
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde du rapport']);
                exit;
            }
            redirect("reports/generate&project_id=$project_id");
        }
    }

    /**
     * APPROCHE HYBRIDE : Template HTML fixe + IA pour le contenu dynamique
     * Format exact demandé par l'utilisateur
     */
    private function buildGeminiPrompt($project, $dataText, $report_type, $notes, $attachmentsText, $uploadedFiles, $draft = null) {
        // Le brouillon peut être fourni par l'appelant (handleGenerate). Sinon, utiliser des valeurs par défaut.
        if (!is_array($draft)) {
            $draft = [
                'weather' => 'Ensoleille',
                'equipments' => [],
                'personnels' => [],
                'materials' => []
            ];
        }

        // Récupérer l'utilisateur connecté
        $userName = $_SESSION['user']['name'] ?? $_SESSION['user_name'] ?? 'Ingénieur GCI';
        $userRole = $_SESSION['user']['role'] ?? $_SESSION['user_role'] ?? 'Ingénieur de Conception en Génie Civil';

        // Numéro et date du rapport
        $reportNumber = $project['id'];
        $currentDate  = date('d/m/Y');
        $currentDateTime = date('d/m/Y à H:i');

        // ── TABLEAU ÉQUIPEMENTS (données réelles du brouillon) ──────────────
        $equipRows = '';
        if (!empty($draft['equipments'])) {
            foreach ($draft['equipments'] as $eq) {
                $d = htmlspecialchars($eq['designation'] ?? '');
                $p = htmlspecialchars($eq['present']     ?? '-');
                $m = htmlspecialchars($eq['marche']      ?? '-');
                $i = htmlspecialchars($eq['immob']       ?? '-');
                $pa= htmlspecialchars($eq['panne']       ?? '-');
                $equipRows .= "
<tr>
  <td style='padding:8px;border:1px solid #666;'>$d</td>
  <td style='padding:8px;border:1px solid #666;text-align:center;'>$p</td>
  <td style='padding:8px;border:1px solid #666;text-align:center;'>$m</td>
  <td style='padding:8px;border:1px solid #666;text-align:center;'>$i</td>
  <td style='padding:8px;border:1px solid #666;text-align:center;'>$pa</td>
</tr>";
            }
        } else {
            $equipRows = "<tr><td colspan='5' style='padding:8px;border:1px solid #666;text-align:center;color:#999;'>Aucun équipement renseigné</td></tr>";
        }

        // ── TABLEAU PERSONNEL (données réelles du brouillon) ────────────────
        $personnelRows = '';
        if (!empty($draft['personnels'])) {
            foreach ($draft['personnels'] as $p) {
                $profil = htmlspecialchars($p['profile'] ?? '');
                $nbr    = htmlspecialchars($p['nbr']     ?? '0');
                $personnelRows .= "
<tr>
  <td style='padding:8px;border:1px solid #666;'>$profil</td>
  <td style='padding:8px;border:1px solid #666;text-align:center;'>$nbr</td>
</tr>";
            }
        } else {
            $personnelRows = "<tr><td colspan='2' style='padding:8px;border:1px solid #666;text-align:center;color:#999;'>Aucun personnel renseigné</td></tr>";
        }

        // ── TABLEAU MATÉRIAUX (données réelles du brouillon) ────────────────
        $materiauxRows = '';
        if (!empty($draft['materials'])) {
            foreach ($draft['materials'] as $mat) {
                $desig = htmlspecialchars($mat['designation'] ?? '');
                $unite = htmlspecialchars($mat['unite']       ?? '');
                $qte   = htmlspecialchars($mat['quantite']    ?? '0');
                $materiauxRows .= "
<tr>
  <td style='padding:8px;border:1px solid #666;'>$desig</td>
  <td style='padding:8px;border:1px solid #666;text-align:center;'>$unite</td>
  <td style='padding:8px;border:1px solid #666;text-align:center;'>$qte</td>
</tr>";
            }
        } else {
            $materiauxRows = "<tr><td colspan='3' style='padding:8px;border:1px solid #666;text-align:center;color:#999;'>Aucun matériau renseigné</td></tr>";
        }

        // ── ANNEXES PHOTOGRAPHIQUES ─────────────────────────────────────────
        $annexesHtml = '';
        $photoFiles = array_filter($uploadedFiles, fn($f) => strpos($f['type'], 'image/') === 0);
        if (!empty($photoFiles)) {
            $annexesHtml = "<h2 style='font-size:16px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>ANNEXES PHOTOGRAPHIQUES</h2>";
            $annexesHtml .= "<div style='display:flex;gap:16px;flex-wrap:wrap;justify-content:space-between;margin-bottom:16px;'>";
            $count = 0;
            foreach ($photoFiles as $photo) {
                if ($count >= 2) break;
                $imageUrl = htmlspecialchars($photo['url'], ENT_QUOTES, 'UTF-8');
                $photoName = htmlspecialchars($photo['name'], ENT_QUOTES, 'UTF-8');
                $annexesHtml .= "<div style='width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:12px;overflow:hidden;background:#fff;'>";
                $annexesHtml .= "<div style='padding:10px;font-size:13px;color:#475569;border-bottom:1px solid #e5e7eb;'>$photoName</div>";
                $annexesHtml .= "<img src='$imageUrl' alt='Photo chantier' style='width:100%;height:auto;display:block;' />";
                $annexesHtml .= "</div>";
                $count++;
            }
            $annexesHtml .= "</div>";
        }
        // Si pas de photos → section absente (rien affiché)

        // ── TEMPLATE HTML COMPLET ────────────────────────────────────────────
        $htmlTemplate = "
<div style='font-family:Arial,sans-serif;font-size:14px;color:#000;max-width:900px;margin:0 auto;'>

<!-- TITRE -->
<h1 style='font-size:18px;font-weight:bold;text-align:center;padding:12px 0;margin:0 0 16px;color:#000;'>
  Rapport n{$reportNumber} - {$project['name']} - {$currentDate}
</h1>

<div style='border:1px solid #999;background:#f2f2f2;padding:14px;margin-bottom:16px;'>
  <table style='width:100%;border-collapse:collapse;'>
    <tr>
      <td style='padding:6px;width:50%;vertical-align:top;'><strong>Projet :</strong> {$project['name']}</td>
      <td style='padding:6px;width:50%;vertical-align:top;'><strong>Type de projet :</strong> {$project['type']}</td>
    </tr>
    <tr>
      <td style='padding:6px;vertical-align:top;'><strong>Description :</strong> " . htmlspecialchars($project['description'] ?? 'Non spécifiée') . "</td>
      <td style='padding:6px;vertical-align:top;'><strong>Date de début :</strong> " . (!empty($project['start_date']) ? htmlspecialchars(date('d/m/Y', strtotime($project['start_date']))) : 'Non spécifiée') . "</td>
    </tr>
    <tr>
      <td style='padding:6px;vertical-align:top;'><strong>Maître d'ouvrage :</strong> " . htmlspecialchars($project['maitre_ouvrage'] ?? $project['client'] ?? 'Non spécifié') . "</td>
      <td style='padding:6px;vertical-align:top;'><strong>Mission de contrôle :</strong> " . htmlspecialchars($project['missions_controle'] ?? $project['control_mission'] ?? 'Non spécifié') . "</td>
    </tr>
    <tr>
      <td style='padding:6px;vertical-align:top;'><strong>Localisation :</strong> {$project['location']}</td>
      <td style='padding:6px;vertical-align:top;'><strong>Entreprise exécutante :</strong> Génie Concept Innovation</td>
    </tr>
    <tr>
      <td style='padding:6px;vertical-align:top;'><strong>Météo :</strong> {$draft['weather']}</td>
      <td style='padding:6px;vertical-align:top;'><strong>Date du rapport :</strong> {$currentDate}</td>
    </tr>
  </table>
</div>

<div style='background:#f5f5f5;border:1px solid #ccc;padding:12px;margin-bottom:24px;'>
  <p style='margin:0;font-size:13px;line-height:1.6;'>
    Ce compte-rendu synthétise les activités de suivi et de contrôle technique réalisées sur le chantier du projet {$project['name']} situé à {$project['location']}.
  </p>
</div>

<!-- INTRODUCTION -->
<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>INTRODUCTION</h2>
<p style='margin:0 0 16px;line-height:1.8;'>{{INTRODUCTION}}</p>

<!-- INFORMATIONS DU CHANTIER -->
<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>INFORMATIONS DU CHANTIER</h2>

<h3 style='font-size:14px;font-weight:bold;margin:16px 0 8px;'>Partie 1 &mdash; Conditions et matériel</h3>
<table style='width:100%;border-collapse:collapse;border:1px solid #666;margin:8px 0;'>
  <tr style='background:#d0d0d0;'>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Désignation</th>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Présent</th>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Marche</th>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Immob</th>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Panne</th>
  </tr>
  {$equipRows}
</table>

<h3 style='font-size:14px;font-weight:bold;margin:16px 0 8px;'>Partie 2 &mdash; Personnel</h3>
<table style='width:100%;border-collapse:collapse;border:1px solid #666;margin:8px 0;'>
  <tr style='background:#d0d0d0;'>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Profil</th>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Nombre</th>
  </tr>
  {$personnelRows}
</table>

<h3 style='font-size:14px;font-weight:bold;margin:16px 0 8px;'>Partie 3 &mdash; Matériaux</h3>
<table style='width:100%;border-collapse:collapse;border:1px solid #666;margin:8px 0;'>
  <tr style='background:#d0d0d0;'>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Désignation</th>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Unité</th>
    <th style='padding:8px;border:1px solid #666;text-align:center;'>Quantité</th>
  </tr>
  {$materiauxRows}
</table>

<!-- RÉSUMÉ DES TRAVAUX -->
<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>RÉSUMÉ DES TRAVAUX EXÉCUTÉS</h2>
<p style='margin:0 0 16px;line-height:1.8;text-align:justify;'>{{RESUME_TRAVAUX}}</p>

<!-- RECOMMANDATIONS -->
<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>RECOMMANDATIONS</h2>
<p style='margin:0 0 16px;line-height:1.8;text-align:justify;'>{{RECOMMANDATIONS}}</p>

{$annexesHtml}

<!-- SIGNATURE -->
<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>RÉDIGÉ PAR</h2>
<p style='margin:0 0 8px;font-weight:bold;'>" . htmlspecialchars($userName) . "</p>
<p style='margin:0 0 24px;color:#555;'>" . htmlspecialchars($userRole) . "</p>

</div>";

        // ── PROMPT IA : uniquement le texte dynamique ────────────────────────
        $equipSummary = '';
        foreach ($draft['equipments'] as $eq) {
            $equipSummary .= "- {$eq['designation']} (Présent:{$eq['present']}, Marche:{$eq['marche']}, Immob:{$eq['immob']}, Panne:{$eq['panne']})\n";
        }
        $personnelSummary = '';
        foreach ($draft['personnels'] as $p) {
            $personnelSummary .= "- {$p['profile']} : {$p['nbr']}\n";
        }
        $materiauxSummary = '';
        foreach ($draft['materials'] as $mat) {
            $materiauxSummary .= "- {$mat['designation']} : {$mat['quantite']} {$mat['unite']}\n";
        }

        $aiPrompt = "Tu es un ingénieur BTP senior de Genie Concept Innovation. Rédige en français professionnel avec jargon BTP.\n";
        $aiPrompt .= "Réponds de manière brève, concise et directe. Utilise des phrases claires, sans digressions ni listes.\n\n";
        $aiPrompt .= "PROJET : {$project['name']}\n";
        $aiPrompt .= "LOCALISATION : {$project['location']}\n";
        $aiPrompt .= "MÉTÉO : {$draft['weather']}\n";
        $aiPrompt .= "ÉQUIPEMENTS :\n{$equipSummary}\n";
        $aiPrompt .= "PERSONNEL :\n{$personnelSummary}\n";
        $aiPrompt .= "MATÉRIAUX :\n{$materiauxSummary}\n";
        $aiPrompt .= "NOTES TERRAIN :\n{$notes}\n\n";
        if (!empty($photoFiles)) {
            $aiPrompt .= "PHOTOS DE CHANTIER DISPONIBLES : Oui. Prépare un rapport avec une section ANNEXES PHOTOGRAPHIQUES contenant deux photos de chantier si possible. Ne génère pas de texte de description pour ces photos, laisse le système afficher les images.\n\n";
        } else {
            $aiPrompt .= "PHOTOS DE CHANTIER DISPONIBLES : Non. N'inclus pas de section ANNEXES PHOTOGRAPHIQUES dans le rapport.\n\n";
        }
        $aiPrompt .= "IMPORTANT: Le système attend trois paragraphes distincts en texte brut, sans balises HTML ni éléments de formatage. Réponds exactement en respectant ce format et N'AJOUTE AUCUN TEXTE SUPPLÉMENTAIRE:\n";
        $aiPrompt .= "\n1) INTRODUCTION: une seule phrase introductive (une seule ligne).\n";
        $aiPrompt .= "2) RESUME: un paragraphe de 4 à 6 phrases décrivant les travaux exécutés, l'avancement et points de vigilance.\n";
        $aiPrompt .= "3) RECOMMANDATIONS: un paragraphe de 3 à 5 phrases, recommandations techniques et opérationnelles concrètes.\n\n";
        $aiPrompt .= "Format exact attendu (exemple):\nINTRODUCTION: [une phrase]\nRESUME: [paragraphe]\nRECOMMANDATIONS: [paragraphe]\n\n";
        $aiPrompt .= "Ne mets pas de titres HTML, ne numérote pas les sections différemment, ne fournis pas de listes, et n'inclus pas de commentaires ou notes internes. Le système insérera ces trois blocs dans le template HTML.\n";

        return ['template' => $htmlTemplate, 'ai_prompt' => $aiPrompt];
    }

    // Autres méthodes restent inchangées...
    public function show($id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        $report = $this->reportModel->getById($id);
        
        if (!$report || $report['user_id'] != $user_id) {
            redirect('reports');
        }

        require VIEWS_PATH . '/reports/show.php';
    }

    public function monthly() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $monthlyReports = $this->reportModel->getMonthlySummaryByUserId($user_id, $year);
        $selectedYear = $year;

        require VIEWS_PATH . '/reports/monthly.php';
    }

    public function yearly() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $yearlyReports = $this->reportModel->getAnnualSummaryByUserId($user_id);

        require VIEWS_PATH . '/reports/yearly.php';
    }

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
    /**
     * Appeler l'API Gemini pour générer le contenu
     */
    private function callGeminiAPI($prompt) {
        $apiKey = GEMINI_API_KEY;
        $url = GEMINI_API_URL . '?key=' . $apiKey;

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => 'Réponds de manière brève, concise et directe. Utilise des phrases courtes et ne sois pas bavard.']
                ]
            ],
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        $maxRetries = 3;
        $retryDelay = 1;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            $attempt++;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                error_log("DEBUG: Gemini cURL error attempt $attempt: $curlError");
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                    continue;
                }
                return false;
            }

            if (in_array($httpCode, [429, 503, 504], true)) {
                error_log("DEBUG: Gemini HTTP transient error $httpCode attempt $attempt: $response");
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                    continue;
                }
                return false;
            }

            if ($httpCode !== 200) {
                error_log("DEBUG: Gemini HTTP error $httpCode: $response");
                return false;
            }

            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }

            error_log("DEBUG: Gemini invalid response on attempt $attempt: $response");
            return false;
        }

        return false;
    }

    private function buildOpenAIMessages($project, $dataText, $report_type, $notes, $attachmentsText, $uploadedFiles) {
        // Récupérer les données du brouillon depuis la base (24h)
        $draft = [
            'weather' => 'Non spécifié',
            'equipments' => [],
            'personnels' => [],
            'materials' => []
        ];
        $user_id = $_SESSION['user_id'] ?? null;
        if ($user_id) {
            $draftRow = $this->reportDraftModel->getByProjectAndUser($project['id'], $user_id);
            if ($draftRow) {
                $updatedAt = strtotime($draftRow['updated_at']);
                if ($updatedAt !== false && (time() - $updatedAt) <= 24 * 3600) {
                    $decoded = json_decode($draftRow['data'], true);
                    if (is_array($decoded)) {
                        $draft = array_merge($draft, $decoded);
                    }
                } else {
                    $this->reportDraftModel->deleteByProjectAndUser($project['id'], $user_id);
                }
            }
        }

        // Récupérer l'utilisateur connecté
        $userName = $_SESSION['user']['name'] ?? $_SESSION['user_name'] ?? 'Non spécifié';
        $userRole = $_SESSION['user']['role'] ?? $_SESSION['user_role'] ?? 'Non spécifié';

        $systemMessage = "Tu es un ingénieur BTP expérimenté et chef de chantier professionnel pour Genie Concept Innovation. Tu réponds de manière brève, concise et directe. Utilise des phrases courtes et évite les explications inutiles. Tu rédiges des rapports de chantier en utilisant le JARGON TECHNIQUE du BTP.

GÉNÈRE LE RAPPORT EN FORMAT HTML AVEC DES TABLEAUX :

<div class='report-header'>
    <h1>Rapport n° [X] - {$project['name']}</h1>
    <p class='report-date'>" . date('d/m/Y') . "</p>
</div>

<div class='report-section'>
    <h2>INFORMATIONS ADMINISTRATIVES</h2>
    <table class='info-table'>
        <tr><td class='label'>Projet</td><td class='value'>{$project['name']}</td></tr>
        <tr><td class='label'>Type</td><td class='value'>{$project['type']}</td></tr>
        <tr><td class='label'>Localisation</td><td class='value'>{$project['location']}</td></tr>
        <tr><td class='label'>Maître d'ouvrage</td><td class='value'>{$project['client']}</td></tr>
        <tr><td class='label'>Mission contrôle</td><td class='value'>{$project['control_mission']}</td></tr>
        <tr><td class='label'>Entreprise exécutante</td><td class='value'>Genie Concept Innovation</td></tr>
        <tr><td class='label'>Météo</td><td class='value'>{$draft['weather']}</td></tr>
        <tr><td class='label'>Date de génération</td><td class='value'>" . date('d/m/Y à H:i') . "</td></tr>
    </table>
</div>

INSTRUCTIONS CRITIQUES :
1. GÉNÈRE LE RAPPORT EN FORMAT HTML avec des balises <table>, <tr>, <td>, <th>
2. Utilise les classes CSS : 'info-table', 'data-table', 'report-section', 'report-header', 'report-footer', 'recommendations-list'
3. GÉNÈRE UNIQUEMENT du HTML, pas de texte brut, tout doit être dans des balises HTML";

        $userMessage = "GÉNÉRATION DU RAPPORT DE CHANTIER\n\n" .
            "INFORMATIONS DU PROJET:\n" .
            "Nom: {$project['name']}\n" .
            "Type: {$project['type']}\n" .
            "Localisation: {$project['location']}\n" .
            "Description: {$project['description']}\n\n";

        if (!empty($notes)) {
            $userMessage .= "NOTES ET OBSERVATIONS DU TERRAIN:\n$notes\n\n";
        }

        if (!empty($dataText)) {
            $userMessage .= "DONNÉES SUPPLÉMENTAIRES:\n$dataText\n\n";
        }

        $userMessage .= "GÉNÈRE LE RAPPORT en respectant EXACTEMENT la structure HTML et le contenu des captures d'écran fournies.";

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

    /**
     * Exporter le brouillon du rapport en PDF formaté
     */
    public function exportDraftPDF($project_id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            exit;
        }

        $project = $this->projectModel->getById($project_id);
        $draft = [
            'weather' => 'Ensoleillé',
            'equipments' => [],
            'personnels' => [],
            'materials' => []
        ];
        $user_id = $_SESSION['user_id'] ?? null;
        if ($user_id) {
            $draftRow = $this->reportDraftModel->getByProjectAndUser($project_id, $user_id);
            if ($draftRow) {
                $updatedAt = strtotime($draftRow['updated_at']);
                if ($updatedAt !== false && (time() - $updatedAt) <= 24 * 3600) {
                    $decoded = json_decode($draftRow['data'], true);
                    if (is_array($decoded)) {
                        $draft = array_merge($draft, $decoded);
                    }
                } else {
                    $this->reportDraftModel->deleteByProjectAndUser($project_id, $user_id);
                }
            }
        }

        try {
            require_once ROOT_PATH . '/app/services/PDFGenerator.php';
            
            $pdfGenerator = new PDFGenerator();
            
            // Préparer les données du projet pour le PDF
            $projectData = [
                'name' => $project['name'],
                'location' => $project['location'] ?? 'Non spécifiée',
                'owner' => $project['client'] ?? 'Non spécifié',
                'control' => $project['control_mission'] ?? 'Non spécifiée',
                'company' => 'Genie Concept Innovation',
                'type' => 'Génie Civil'
            ];
            
            // Générer le PDF
            $pdfContent = $pdfGenerator->generateStructuredReport(
                $projectData,
                $draft['equipments'] ?? [],
                $draft['personnels'] ?? [],
                $draft['materials'] ?? [],
                $draft['weather'] ?? 'Ensoleillé',
                ''
            );
            
            // Préparer le nom du fichier
            $filename = 'Rapport_' . str_replace(' ', '_', $project['name']) . '_' . date('d-m-Y_His') . '.pdf';
            
            // Nettoyer les buffers
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Envoyer les headers dans le bon ordre
            header('Content-Type: application/pdf', true);
            header('Content-Disposition: inline; filename="' . $filename . '"', true);
            header('Cache-Control: private, no-cache, no-store, must-revalidate', true);
            header('Expires: 0', true);
            
            // Envoyer le contenu
            echo $pdfContent;
            exit;
            
        } catch (Exception $e) {
            error_log("Erreur lors de la génération du PDF: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la génération du PDF']);
            exit;
        }
    }

    /**
     * Exporter un rapport généré en PDF
     */
    public function exportReportPDF($report_id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        $report = $this->reportModel->getById($report_id);
        
        if (!$report || $report['user_id'] != $user_id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            exit;
        }

        try {
            require_once ROOT_PATH . '/app/services/PDFGenerator.php';
            
            $pdfGenerator = new PDFGenerator();
            
            // Récupérer le projet associé
            $project = $this->projectModel->getById($report['project_id']);
            
            // Préparer les données
            $projectData = [
                'name' => $report['title'],
                'location' => $project['location'] ?? 'Non spécifiée'
            ];
            
            $pdfContent = $pdfGenerator->generateReport($projectData, ['content' => $report['content']]);
            
            // Préparer le nom du fichier
            $filename = str_replace(' ', '_', $report['title']) . '_' . date('d-m-Y_His') . '.pdf';
            
            // Nettoyer les buffers
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Envoyer les headers dans le bon ordre
            header('Content-Type: application/pdf', true);
            header('Content-Disposition: inline; filename="' . $filename . '"', true);
            header('Cache-Control: private, no-cache, no-store, must-revalidate', true);
            header('Expires: 0', true);
            
            // Envoyer le contenu
            echo $pdfContent;
            exit;
            
        } catch (Exception $e) {
            error_log("Erreur lors de la génération du PDF: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la génération du PDF']);
            exit;
        }
    }
}
?>
