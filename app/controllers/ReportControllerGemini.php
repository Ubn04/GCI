<?php
/**
 * Contrôleur des rapports optimisé pour Gemini API
 */

require_once ROOT_PATH . '/app/models/Report.php';
require_once ROOT_PATH . '/app/models/Project.php';
require_once ROOT_PATH . '/app/models/SiteData.php';
require_once ROOT_PATH . '/app/models/ReportDraft.php';

class ReportControllerGemini {
    private $pdo;
    private $reportModel;
    private $projectModel;
    private $siteDataModel;
    private $reportDraftModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
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
                    'quantite' => is_numeric($item['quantite'] ?? null) ? (float) $item['quantite'] : 0,
                ];
            }, $data['materials'] ?? [])),
        ];

        // Persister dans la base
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

        // Récupérer les informations du brouillon depuis la base (24h)
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
        $reportNumber = $this->reportModel->countByProjectId($project_id) + 1;
        $currentUser = $this->getCurrentUserIdentity($user_id);
        $userFullName = $currentUser['name'];
        $userRole = $currentUser['role'];

        // Construire le prompt optimisé pour Gemini
        $prompt = $this->buildGeminiPrompt($project, $dataText, $draftText, $report_type, $additionalNotes);
        error_log("DEBUG: Prompt built, trying Gemini API first");

        $reportContent = null;

        // Essayer d'abord avec Gemini (gratuit et performant)
        $geminiResponse = $this->callGeminiAPI($prompt);
        if ($geminiResponse !== false) {
            $aiContent = $geminiResponse;
            error_log("DEBUG: Gemini API successful, content length: " . strlen($aiContent));
            setFlash('success', 'Rapport généré avec succès via Gemini AI !');

            // Parser les sections attendues (INTRODUCTION, RESUME, RECOMMANDATIONS)
            $introduction = "Ce rapport rend compte du suivi de chantier effectué ce jour sur le projet {$project['name']}.";
            $resume = "Les travaux se sont déroulés conformément au planning établi.";
            $recommandations = "Poursuivre les travaux en respectant les normes de sécurité et de qualité.";

            if (preg_match('/^\s*INTRODUCTION\s*:\s*(.+?)(?=^\s*(RESUME|RECOMMANDATIONS?)\s*:|$)/msi', $aiContent, $m)) {
                $introduction = trim($m[1]);
            }
            if (preg_match('/^\s*RESUME\s*:\s*(.+?)(?=^\s*RECOMMANDATIONS?\s*:|$)/msi', $aiContent, $m)) {
                $resume = trim($m[1]);
            }
            if (preg_match('/^\s*RECOMMANDATIONS?\s*:\s*(.+?)$/msi', $aiContent, $m)) {
                $recommandations = trim($m[1]);
            }

            // Nettoyage des balises et entités
            $introduction = html_entity_decode(strip_tags($introduction), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $resume = html_entity_decode(strip_tags($resume), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $recommandations = html_entity_decode(strip_tags($recommandations), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $introduction = preg_replace('/\s+/u', ' ', trim($introduction));
            $resume = preg_replace('/\s+/u', ' ', trim($resume));
            $recommandations = preg_replace('/\s+/u', ' ', trim($recommandations));

            // Construire les lignes de tableaux depuis le brouillon
            $equipRows = '';
            if (!empty($draft['equipments'])) {
                foreach ($draft['equipments'] as $eq) {
                    $d = htmlspecialchars($eq['designation'] ?? '');
                    $p = htmlspecialchars($eq['present'] ?? '-');
                    $m = htmlspecialchars($eq['marche'] ?? '-');
                    $i = htmlspecialchars($eq['immob'] ?? '-');
                    $pa = htmlspecialchars($eq['panne'] ?? '-');
                    $equipRows .= "\n<tr>\n  <td style='padding:8px;border:1px solid #666;'>$d</td>\n  <td style='padding:8px;border:1px solid #666;text-align:center;'>$p</td>\n  <td style='padding:8px;border:1px solid #666;text-align:center;'>$m</td>\n  <td style='padding:8px;border:1px solid #666;text-align:center;'>$i</td>\n  <td style='padding:8px;border:1px solid #666;text-align:center;'>$pa</td>\n</tr>";
                }
            } else {
                $equipRows = "<tr><td colspan='5' style='padding:8px;border:1px solid #666;text-align:center;color:#999;'>Aucun équipement renseigné</td></tr>";
            }

            $personnelRows = '';
            if (!empty($draft['personnels'])) {
                foreach ($draft['personnels'] as $p) {
                    $profil = htmlspecialchars($p['profile'] ?? '');
                    $nbr = htmlspecialchars($p['nbr'] ?? '0');
                    $personnelRows .= "\n<tr>\n  <td style='padding:8px;border:1px solid #666;'>$profil</td>\n  <td style='padding:8px;border:1px solid #666;text-align:center;'>$nbr</td>\n</tr>";
                }
            } else {
                $personnelRows = "<tr><td colspan='2' style='padding:8px;border:1px solid #666;text-align:center;color:#999;'>Aucun personnel renseigné</td></tr>";
            }

            $materiauxRows = '';
            if (!empty($draft['materials'])) {
                foreach ($draft['materials'] as $mat) {
                    $desig = htmlspecialchars($mat['designation'] ?? '');
                    $unite = htmlspecialchars($mat['unite'] ?? '');
                    $qte = htmlspecialchars($mat['quantite'] ?? '0');
                    $materiauxRows .= "\n<tr>\n  <td style='padding:8px;border:1px solid #666;'>$desig</td>\n  <td style='padding:8px;border:1px solid #666;text-align:center;'>$unite</td>\n  <td style='padding:8px;border:1px solid #666;text-align:center;'>$qte</td>\n</tr>";
                }
            } else {
                $materiauxRows = "<tr><td colspan='3' style='padding:8px;border:1px solid #666;text-align:center;color:#999;'>Aucun matériau renseigné</td></tr>";
            }

            // Annexes photos
            $annexesHtml = '';
            $uploadedFiles = $uploadedFiles ?? [];
            $photoFiles = array_filter($uploadedFiles, fn($f) => strpos($f['type'], 'image/') === 0);
            if (!empty($photoFiles)) {
                $annexesHtml = "<h2 style='font-size:16px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>ANNEXES PHOTOGRAPHIQUES</h2>";
                $annexesHtml .= "<div style='display:flex;gap:16px;flex-wrap:wrap;justify-content:space-between;margin-bottom:16px;'>";
                $count = 0;
                foreach ($photoFiles as $photo) {
                    if ($count >= 2) break;
                    $imageUrl = htmlspecialchars($photo['url'] ?? $photo['name'], ENT_QUOTES, 'UTF-8');
                    $photoName = htmlspecialchars($photo['name'] ?? '', ENT_QUOTES, 'UTF-8');
                    $annexesHtml .= "<div style='width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:12px;overflow:hidden;background:#fff;'>";
                    $annexesHtml .= "<div style='padding:10px;font-size:13px;color:#475569;border-bottom:1px solid #e5e7eb;'>$photoName</div>";
                    $annexesHtml .= "<img src='$imageUrl' alt='Photo chantier' style='width:100%;height:auto;display:block;' />";
                    $annexesHtml .= "</div>";
                    $count++;
                }
                $annexesHtml .= "</div>";
            }

            // Construire le template HTML (même structure que l'autre contrôleur)
            $html = "<div style='font-family:Arial,sans-serif;font-size:14px;color:#000;max-width:900px;margin:0 auto;'>";
            $html .= "<h1 style='font-size:18px;font-weight:bold;text-align:center;padding:12px 0;margin:0 0 16px;color:#000;'>Rapport numéro $reportNumber - {$project['name']} - " . date('d/m/Y') . "</h1>";
            $html .= "<div style='border:1px solid #999;background:#f2f2f2;padding:14px;margin-bottom:16px;'><table style='width:100%;border-collapse:collapse;'><tr><td style='padding:6px;width:50%;vertical-align:top;'><strong>Projet :</strong> {$project['name']}</td><td style='padding:6px;width:50%;vertical-align:top;'><strong>Type de projet :</strong> {$project['project_type']}</td></tr>";
            $html .= "<tr><td style='padding:6px;vertical-align:top;'><strong>Description :</strong> " . htmlspecialchars($project['description'] ?? 'Non spécifiée') . "</td><td style='padding:6px;vertical-align:top;'><strong>Date de début :</strong> " . (!empty($project['start_date']) ? htmlspecialchars(date('d/m/Y', strtotime($project['start_date']))) : 'Non spécifiée') . "</td></tr>";
            $html .= "<tr><td style='padding:6px;vertical-align:top;'><strong>Maître d'ouvrage :</strong> " . htmlspecialchars($project['maitre_ouvrage'] ?? $project['client'] ?? 'Non spécifié') . "</td><td style='padding:6px;vertical-align:top;'><strong>Mission de contrôle :</strong> " . htmlspecialchars($project['missions_controle'] ?? $project['control_mission'] ?? 'Non spécifié') . "</td></tr>";
            $html .= "<tr><td style='padding:6px;vertical-align:top;'><strong>Localisation :</strong> {$project['location']}</td><td style='padding:6px;vertical-align:top;'><strong>Entreprise exécutante :</strong> Génie Concept Innovation</td></tr>";
            $html .= "<tr><td style='padding:6px;vertical-align:top;'><strong>Météo :</strong> " . htmlspecialchars($draft['weather'] ?? 'Non spécifiée') . "</td><td style='padding:6px;vertical-align:top;'><strong>Date du rapport :</strong> " . date('d/m/Y') . "</td></tr></table></div>";
            $html .= "<div style='background:#f5f5f5;border:1px solid #ccc;padding:12px;margin-bottom:24px;'><p style='margin:0;font-size:13px;line-height:1.6;'>Ce compte-rendu synthétise les activités de suivi et de contrôle technique réalisées sur le chantier du projet {$project['name']} situé à {$project['location']}.</p></div>";
            $html .= "<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>INTRODUCTION</h2><p style='margin:0 0 16px;line-height:1.8;'>" . htmlspecialchars($introduction) . "</p>";
            $html .= "<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>INFORMATIONS DU CHANTIER</h2>";
            $html .= "<h3 style='font-size:14px;font-weight:bold;margin:16px 0 8px;'>Partie 1 - Conditions et materiel</h3><table style='width:100%;border-collapse:collapse;border:1px solid #666;margin:8px 0;'><tr style='background:#d0d0d0;'><th style='padding:8px;border:1px solid #666;text-align:center;'>Meteo</th></tr><tr><td style='padding:8px;border:1px solid #666;'>" . htmlspecialchars($draft['weather'] ?? 'Non specifiee') . "</td></tr></table>";
            $html .= "<h3 style='font-size:14px;font-weight:bold;margin:16px 0 8px;'>Partie 1 &mdash; Conditions et materiel</h3><table style='width:100%;border-collapse:collapse;border:1px solid #666;margin:8px 0;'><tr style='background:#d0d0d0;'><th style='padding:8px;border:1px solid #666;text-align:center;'>Désignation</th><th style='padding:8px;border:1px solid #666;text-align:center;'>Présent</th><th style='padding:8px;border:1px solid #666;text-align:center;'>Marche</th><th style='padding:8px;border:1px solid #666;text-align:center;'>Immob</th><th style='padding:8px;border:1px solid #666;text-align:center;'>Panne</th></tr>" . $equipRows . "</table>";
            $html .= "<h3 style='font-size:14px;font-weight:bold;margin:16px 0 8px;'>Partie 2 &mdash; Personnel</h3><table style='width:100%;border-collapse:collapse;border:1px solid #666;margin:8px 0;'><tr style='background:#d0d0d0;'><th style='padding:8px;border:1px solid #666;text-align:center;'>Profil</th><th style='padding:8px;border:1px solid #666;text-align:center;'>Nombre</th></tr>" . $personnelRows . "</table>";
            $html .= "<h3 style='font-size:14px;font-weight:bold;margin:16px 0 8px;'>Partie 3 &mdash; Matériaux</h3><table style='width:100%;border-collapse:collapse;border:1px solid #666;margin:8px 0;'><tr style='background:#d0d0d0;'><th style='padding:8px;border:1px solid #666;text-align:center;'>Désignation</th><th style='padding:8px;border:1px solid #666;text-align:center;'>Unité</th><th style='padding:8px;border:1px solid #666;text-align:center;'>Quantité</th></tr>" . $materiauxRows . "</table>";
            $html .= "<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>RÉSUMÉ DES TRAVAUX EXÉCUTÉS</h2><p style='margin:0 0 16px;line-height:1.8;text-align:justify;'>" . htmlspecialchars($resume) . "</p>";
            $html .= "<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>RECOMMANDATIONS</h2><p style='margin:0 0 16px;line-height:1.8;text-align:justify;'>" . htmlspecialchars($recommandations) . "</p>";
            $html .= $annexesHtml;
            $html .= "<h2 style='font-size:15px;font-weight:bold;border-bottom:2px solid #1e3a8a;padding-bottom:6px;margin:24px 0 12px;text-transform:uppercase;'>RÉDIGÉ PAR</h2><p style='margin:0 0 8px;font-weight:bold;'>" . htmlspecialchars($userFullName) . "</p><p style='margin:0 0 24px;color:#555;'>" . htmlspecialchars($userRole) . "</p></div>";

            $reportContent = $this->buildStructuredReportContent($project, $draft, $reportNumber, $introduction, $resume, $recommandations, $userFullName, $userRole);
        } else {
            error_log("DEBUG: Gemini API failed, using basic report generation");
            
            // Générer un rapport de base si Gemini échoue
            $basicResume = $this->generateBasicReport($project, $dataText, $draftText, $additionalNotes);
            $reportContent = $this->buildStructuredReportContent(
                $project,
                $draft,
                $reportNumber,
                "Ce compte-rendu synthetise les activites de suivi et de controle technique realisees sur le chantier du projet {$project['name']}.",
                $basicResume,
                "Poursuivre les activites selon le planning, maintenir la vigilance securite et documenter les prochaines observations de chantier.",
                $userFullName,
                $userRole
            );
            error_log("DEBUG: Using basic report generation");
            setFlash('success', 'Rapport généré avec le système de base (Gemini indisponible).');
        }

        // Numérotation séquentielle par projet
        $reportNumber = $this->reportModel->countByProjectId($project_id) + 1;

        // Sauvegarder le rapport
        $reportData = [
            'project_id' => $project_id,
            'user_id' => $user_id,
            'title' => "Rapport numéro $reportNumber - {$project['name']}",
            'content' => $reportContent,
            'report_type' => $report_type,
            'report_date' => date('Y-m-d')
        ];

        $reportData['content'] = $this->decodeHtmlEntitiesRecursively($reportContent);

        if ($reportId = $this->reportModel->create($reportData)) {
            error_log("DEBUG: Report created successfully with ID: $reportId");
            // Garder les informations chantier pour les exports et les prochains rapports.
            // Si requête AJAX, retourner l'aperçu et l'ID, sinon rediriger
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                $preview = $this->formatReportContentForHtml($reportContent);
                echo json_encode(['success' => true, 'report_id' => $reportId, 'preview_html' => $preview]);
                exit;
            }
            redirect("reports&show_modal=$reportId");
        } else {
            error_log("DEBUG: Failed to save report to database");
            setFlash('error', 'Erreur lors de la sauvegarde du rapport');
            redirect("reports/generate&project_id=$project_id");
        }
    }

    /**
     * Construire un prompt optimisé pour Gemini
     */
    private function buildGeminiPrompt($project, $dataText, $draftText, $report_type, $notes, $reportNumber = null, $userFullName = null, $userRole = null) {
        $date = date('d/m/Y');
        $userFullName = $userFullName ?? 'Nom non renseigne';
        $userRole = $userRole ?? 'Profil non renseigne';
        $reportLabel = $reportNumber ? "Rapport numéro $reportNumber" : 'Rapport de chantier';

        $prompt = "Tu es un expert en génie civil spécialisé dans la rédaction de rapports de chantier professionnels.\n";
        $prompt .= "Génère un rapport de chantier clair et sobre en français.\n";
        $prompt .= "Présente les informations du projet dans un en-tête vertical professionnel, chacune sur une ligne distincte.\n";
        $prompt .= "Ne mets pas le mot 'Entête' et n'utilise pas de numérotation de sections.\n";
        $prompt .= "N'utilise pas de tableaux ASCII, de tirets décoratifs, de séparateurs étendus ou de bordures graphiques.\n";
        $prompt .= "La seule séparation autorisée est une ligne discrète sous l'en-tête.\n";
        $prompt .= "N'ajoute pas de signature ou de champ 'Rédigé par' dans le corps du rapport.\n";
        $prompt .= "Le nom et le rôle du rédacteur seront ajoutés par le système séparément.\n\n";

        $prompt .= "IMPORTANT: ne genere pas les tableaux, l'entete, la signature ou le bloc 'Redige par'. Le systeme les construit deja avec les donnees utilisateur.\n";
        $prompt .= "Tu dois seulement produire deux contenus de qualite ingenieur: RESUME et RECOMMANDATIONS.\n";
        $prompt .= "Raisonne comme un expert ingenieur en controle technique: factuel, precis, prudent, adapte au chantier, sans inventer de donnees non fournies.\n";
        $prompt .= "Reponds uniquement sous cette forme exacte, sans HTML et sans Markdown:\n";
        $prompt .= "RESUME: [paragraphe de 4 a 6 phrases]\n";
        $prompt .= "RECOMMANDATIONS: [paragraphe de 4 a 6 phrases]\n\n";

        $prompt .= "$reportLabel - $date\n\n";
        $prompt .= "Projet : {$project['name']}\n";
        $prompt .= "Localisation : {$project['location']}\n";
        $prompt .= "Description : {$project['description']}\n";
        $prompt .= "Date du rapport : $date\n";
        $prompt .= "Date de début : " . date('d/m/Y', strtotime($project['start_date'])) . "\n\n";

        if (!empty($draftText)) {
            $prompt .= "Informations du chantier\n";
            $prompt .= "Décris les ressources mobilisées, les équipements présents et l'état du chantier aujourd'hui. Si aucune donnée précise n'est disponible, indique qu'aucune information n'a été enregistrée.\n";
            $prompt .= $draftText . "\n";
        }

        if (!empty($dataText)) {
            $prompt .= "Données terrain collectées\n";
            $prompt .= $dataText . "\n";
        }

        if (!empty($notes)) {
            $prompt .= "Notes additionnelles\n";
            $prompt .= $notes . "\n\n";
        }

        $chatMessages = $this->getChatMessagesForProject($project['id']);
        if (!empty($chatMessages)) {
            $prompt .= "Informations supplémentaires issues de la conversation avec l'assistant :\n";
            foreach ($chatMessages as $message) {
                $userMsg = $this->cleanChatMessage($message['user_message']);
                if (!empty($userMsg)) {
                    $prompt .= "- Utilisateur : $userMsg\n";
                }

                $aiMsg = $this->cleanChatMessage($message['ai_response']);
                if (!empty($aiMsg)) {
                    $prompt .= "- Assistant : $aiMsg\n";
                }
            }
            $prompt .= "\n";
        }

        $prompt .= "Résumé des travaux exécutés\n";
        $prompt .= "Rédige un paragraphe clair et professionnel en 4 à 6 phrases, sans numéros ni listes décoratives.\n\n";

        $prompt .= "Conditions générales et état du chantier\n";
        $prompt .= "Décris l'avancement général du chantier, les conditions de travail et les points importants à surveiller.\n\n";

        $prompt .= "Ressources mobilisées\n";
        $prompt .= "Présente les équipes, les équipements et les matériaux mobilisés de manière concise et professionnelle.\n\n";

        $prompt .= "Observations et recommandations\n";
        $prompt .= "Fournis des observations claires et des recommandations techniques adaptées au chantier du jour.\n\n";

        $prompt .= "Ne mentionne pas l'IA ou intelligence artificielle.\n";
        $prompt .= "IMPORTANT: Réponds uniquement avec du texte brut en français, sans balises HTML, sans code HTML, sans entités HTML encodées et sans caractères d'échappement.\n";
        $prompt .= "Ne fournis que trois sections clairement identifiées comme ceci :\n";
        $prompt .= "INTRODUCTION: [une phrase concise].\n";
        $prompt .= "RESUME: [un paragraphe de 4 à 6 phrases].\n";
        $prompt .= "RECOMMANDATIONS: [un paragraphe de 3 à 5 phrases].\n";
        $prompt .= "Ne mets aucun autre titre, numéro ou commentaire. Le système ajoutera le reste de la mise en forme.\n";

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

    private function buildStructuredReportContent($project, $draft, $reportNumber, $introduction, $resume, $recommandations, $userFullName, $userRole) {
        $weather = htmlspecialchars($draft['weather'] ?? 'Non precisee', ENT_QUOTES, 'UTF-8');
        $projectName = htmlspecialchars($project['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $projectType = htmlspecialchars($project['project_type'] ?? $project['type'] ?? '-', ENT_QUOTES, 'UTF-8');
        $maitreOuvrage = htmlspecialchars($project['maitre_ouvrage'] ?? $project['client'] ?? '-', ENT_QUOTES, 'UTF-8');
        $missionControle = htmlspecialchars($project['missions_controle'] ?? $project['control_mission'] ?? '-', ENT_QUOTES, 'UTF-8');
        $location = htmlspecialchars($project['location'] ?? '', ENT_QUOTES, 'UTF-8');
        $reportDate = date('d/m/Y');
        $titleDate = date('Y-m-d');
        $userName = htmlspecialchars($userFullName ?: 'Nom non renseigne', ENT_QUOTES, 'UTF-8');
        $role = htmlspecialchars($userRole ?: 'Profil non renseigne', ENT_QUOTES, 'UTF-8');

        $equipmentRows = '';
        foreach (($draft['equipments'] ?? []) as $eq) {
            if (trim($eq['designation'] ?? '') === '') continue;
            $equipmentRows .= '<tr><td style="padding:8px;border:1px solid #999;">' . htmlspecialchars($eq['designation'] ?? '', ENT_QUOTES, 'UTF-8') . '</td><td style="padding:8px;border:1px solid #999;text-align:center;">' . htmlspecialchars($eq['present'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td><td style="padding:8px;border:1px solid #999;text-align:center;">' . htmlspecialchars($eq['marche'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td><td style="padding:8px;border:1px solid #999;text-align:center;">' . htmlspecialchars($eq['immob'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td><td style="padding:8px;border:1px solid #999;text-align:center;">' . htmlspecialchars($eq['panne'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        if ($equipmentRows === '') {
            $equipmentRows = '<tr><td style="padding:8px;border:1px solid #b7b7b7;">Aucune donnee renseignee</td><td style="padding:8px;border:1px solid #b7b7b7;text-align:center;">-</td><td style="padding:8px;border:1px solid #b7b7b7;text-align:center;">-</td><td style="padding:8px;border:1px solid #b7b7b7;text-align:center;">-</td><td style="padding:8px;border:1px solid #b7b7b7;text-align:center;">-</td></tr>';
        }

        $personnelRows = '';
        foreach (($draft['personnels'] ?? []) as $pers) {
            if (trim($pers['profile'] ?? '') === '') continue;
            $personnelRows .= '<tr><td style="padding:8px;border:1px solid #999;">' . htmlspecialchars($pers['profile'] ?? '', ENT_QUOTES, 'UTF-8') . '</td><td style="padding:8px;border:1px solid #999;text-align:center;">' . htmlspecialchars($pers['nbr'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        if ($personnelRows === '') {
            $personnelRows = '<tr><td style="padding:8px;border:1px solid #b7b7b7;">Aucune donnee renseignee</td><td style="padding:8px;border:1px solid #b7b7b7;text-align:center;">-</td></tr>';
        }

        $materialRows = '';
        foreach (($draft['materials'] ?? []) as $mat) {
            if (trim($mat['designation'] ?? '') === '') continue;
            $materialRows .= '<tr><td style="padding:8px;border:1px solid #999;">' . htmlspecialchars($mat['designation'] ?? '', ENT_QUOTES, 'UTF-8') . '</td><td style="padding:8px;border:1px solid #999;text-align:center;">' . htmlspecialchars($mat['unite'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td><td style="padding:8px;border:1px solid #999;text-align:center;">' . htmlspecialchars($mat['quantite'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        if ($materialRows === '') {
            $materialRows = '<tr><td style="padding:8px;border:1px solid #b7b7b7;">Aucune donnee renseignee</td><td style="padding:8px;border:1px solid #b7b7b7;text-align:center;">-</td><td style="padding:8px;border:1px solid #b7b7b7;text-align:center;">-</td></tr>';
        }

        $html = '<div style="font-family:Arial,sans-serif;font-size:16px;line-height:1.35;color:#000;max-width:940px;margin:0 auto;background:#fff;">';
        $html .= '<h1 style="font-size:30px;line-height:1.15;margin:0 0 12px;font-weight:700;">Rapport n' . htmlspecialchars((string)$reportNumber, ENT_QUOTES, 'UTF-8') . ' - ' . $projectName . ' - ' . $titleDate . '</h1>';
        $html .= '<div style="border:1px solid #b7b7b7;background:#f4f4f4;width:390px;max-width:100%;padding:12px 14px;margin:0 0 16px;">';
        $html .= '<div><strong>Projet:</strong> ' . $projectName . '</div>';
        $html .= '<div><strong>Type de projet:</strong> ' . $projectType . '</div>';
        $html .= '<div><strong>Maitre d\'ouvrage:</strong> ' . $maitreOuvrage . '</div>';
        $html .= '<div><strong>Mission de controle:</strong> ' . $missionControle . '</div>';
        $html .= '<div><strong>Localisation:</strong> ' . $location . '</div>';
        $html .= '<div><strong>Entreprise executante:</strong> Genie Concept Innovation</div>';
        $html .= '<div><strong>Meteo:</strong> ' . $weather . '</div>';
        $html .= '<div><strong>Date du rapport:</strong> ' . $reportDate . '</div>';
        $html .= '</div>';
        $html .= '<p style="margin:0 0 16px;">Ce compte-rendu synthetise les activites de suivi et de controle technique realisees sur le chantier du projet ' . $projectName . (!empty($location) ? ' situe a ' . $location : '') . '.</p>';
        $html .= '<h2 style="font-size:21px;margin:18px 0 8px;font-weight:700;">Informations du chantier</h2>';
        $html .= '<h3 style="font-size:19px;margin:8px 0 8px;font-weight:700;">Partie 1 - Conditions et materiel</h3>';
        $html .= '<table style="width:100%;border-collapse:collapse;margin:0 0 10px;"><tr style="background:#d9d9d9;"><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Meteo</th></tr><tr><td style="padding:8px;border:1px solid #b7b7b7;">' . $weather . '</td></tr></table>';
        $html .= '<table style="width:100%;border-collapse:collapse;margin:0 0 12px;"><tr style="background:#d9d9d9;"><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Designation</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Present</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Marche</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Immob</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Panne</th></tr>' . $equipmentRows . '</table>';
        $html .= '<h3 style="font-size:19px;margin:12px 0 8px;font-weight:700;">Partie 2 - Personnel</h3><table style="width:100%;border-collapse:collapse;margin:0 0 12px;"><tr style="background:#d9d9d9;"><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Profil</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Nombre</th></tr>' . $personnelRows . '</table>';
        $html .= '<h3 style="font-size:19px;margin:12px 0 8px;font-weight:700;">Partie 3 - Materiaux</h3><table style="width:100%;border-collapse:collapse;margin:0 0 20px;"><tr style="background:#d9d9d9;"><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Designation</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Unite</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:center;">Quantite</th></tr>' . $materialRows . '</table>';
        $html .= '<h2 style="font-size:21px;margin:20px 0 8px;font-weight:700;">Resume des travaux executes</h2><p style="margin:0 0 14px;text-align:justify;">' . htmlspecialchars($resume, ENT_QUOTES, 'UTF-8') . '</p>';
        $html .= '<h2 style="font-size:21px;margin:20px 0 8px;font-weight:700;">Recommandations</h2><p style="margin:0 0 28px;text-align:justify;">' . htmlspecialchars($recommandations, ENT_QUOTES, 'UTF-8') . '</p>';
        $html .= '<h2 style="font-size:21px;margin:0 0 8px;font-weight:700;">Redige par</h2>';
        $html .= '<p style="margin:0 0 2px;">' . $userName . '</p>';
        $html .= '<p style="margin:0 0 28px;">' . $role . '</p>';
        $html .= '<h2 style="font-size:16px;margin:0 0 26px;font-weight:700;">Signature</h2>';
        $html .= '<div style="width:285px;border-bottom:2px solid #444;margin-bottom:36px;"></div>';
        $html .= '</div>';

        return $html;
    }

    private function buildFallbackResumeFromDraft($project, $draft) {
        $projectName = $project['name'] ?? 'ce projet';
        $weather = $draft['weather'] ?? 'Non specifiee';
        $equipmentCount = count($draft['equipments'] ?? []);
        $personnelCount = 0;

        foreach (($draft['personnels'] ?? []) as $person) {
            $personnelCount += intval($person['nbr'] ?? 0);
        }

        $materialCount = count($draft['materials'] ?? []);

        return "Les activites de suivi du chantier {$projectName} ont ete etablies a partir des informations renseignees par l'utilisateur sur la page des informations du chantier. Les conditions meteorologiques indiquees sont : {$weather}. Le rapport prend en compte {$equipmentCount} ligne(s) de materiel, {$personnelCount} personne(s) mobilisee(s) et {$materialCount} ligne(s) de materiaux. Les tableaux ci-dessus constituent la base factuelle du compte-rendu et doivent etre completes par les observations terrain disponibles. Le suivi doit rester regulier afin de documenter l'avancement, les ressources presentes et les points techniques a surveiller.";
    }

    private function parseGeminiTextSections($content) {
        $content = trim((string) $content);
        $sections = [
            'resume' => $content,
            'recommandations' => "Formaliser les observations du jour, documenter les points de controle, verifier la conformite des travaux executes et assurer la tracabilite des ressources mobilisees. Les prochaines interventions doivent etre accompagnees de donnees terrain completes afin de faciliter l'analyse technique du chantier."
        ];

        if (preg_match('/^\s*RESUME\s*:\s*(.+?)(?=^\s*RECOMMANDATIONS?\s*:|$)/msi', $content, $match)) {
            $sections['resume'] = trim($match[1]);
        }
        if (preg_match('/^\s*RECOMMANDATIONS?\s*:\s*(.+?)$/msi', $content, $match)) {
            $sections['recommandations'] = trim($match[1]);
        }

        $sections['resume'] = preg_replace('/\s+/u', ' ', strip_tags($sections['resume']));
        $sections['recommandations'] = preg_replace('/\s+/u', ' ', strip_tags($sections['recommandations']));

        return $sections;
    }

    private function getCurrentUserIdentity($userId) {
        $identity = [
            'name' => trim($_SESSION['user']['name'] ?? $_SESSION['user_name'] ?? ''),
            'role' => trim($_SESSION['user']['role'] ?? $_SESSION['user_role'] ?? '')
        ];

        try {
            $stmt = $this->pdo->prepare('SELECT name, role FROM users WHERE id = :id LIMIT 1');
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $identity['name'] = trim($user['name'] ?? $identity['name']);
                $identity['role'] = trim($user['role'] ?? $identity['role']);
            }
        } catch (Exception $e) {
            error_log('Erreur recuperation utilisateur rapport: ' . $e->getMessage());
        }

        if ($identity['name'] === '') {
            $identity['name'] = 'Nom non renseigne';
        }
        if ($identity['role'] === '') {
            $identity['role'] = 'Profil non renseigne';
        }

        return $identity;
    }

    /**
     * Appeler l'API Gemini pour générer le contenu
     */
    private function callGeminiAPI($prompt) {
        // Augmenter le timeout PHP pour éviter les timeouts
        set_time_limit(300);

        $apiKey = GEMINI_API_KEY;
        if (empty($apiKey)) {
            error_log("DEBUG: Gemini API key not configured");
            return false;
        }
        
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
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
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
            curl_setopt($ch, CURLOPT_TIMEOUT, 180);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                error_log("DEBUG: Gemini API cURL error attempt $attempt: $curlError");
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                    continue;
                }
                return false;
            }

            if (in_array($httpCode, [429, 503, 504], true)) {
                error_log("DEBUG: Gemini API transient HTTP error $httpCode on attempt $attempt: $response");
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                    continue;
                }
                return false;
            }

            if ($httpCode !== 200) {
                error_log("DEBUG: Gemini API HTTP error $httpCode: $response");
                return false;
            }

            // Vérifier si la réponse commence par du HTML (erreur serveur)
            if (strpos(trim($response), '<') === 0) {
                error_log("DEBUG: Gemini API returned HTML instead of JSON on attempt $attempt");
                error_log("DEBUG: HTML response: " . substr($response, 0, 200));
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                    continue;
                }
                return false;
            }

            // Décoder la réponse JSON
            $result = json_decode($response, true);
            
            // Vérifier les erreurs de décodage JSON
            if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
                error_log("DEBUG: Gemini API JSON decode error: " . json_last_error_msg());
                error_log("DEBUG: Response received: " . substr($response, 0, 500));
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                    continue;
                }
                return false;
            }

            // Vérifier que la structure JSON est correcte
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }

            error_log("DEBUG: Gemini API invalid response structure on attempt $attempt: " . json_encode($result));
            return false;
        }

        return false;
    }

    /**
     * Générer un rapport de base sans IA
     */
    private function generateBasicReport($project, $dataText, $draftText, $notes) {
        $date = date('d/m/Y');
        
        $report = "Rapport de chantier - $date\n\n";
        $report .= "Projet : {$project['name']}\n";
        $report .= "Localisation : {$project['location']}\n";
        $report .= "Date : $date\n\n";
        
        $report .= "1. Résumé des travaux exécutés\n\n";
        $report .= "Rapport journalier des activités du chantier {$project['name']}.\n\n";
        
        $report .= "2. Conditions générales\n\n";
        if (!empty($draftText)) {
            $report .= $draftText . "\n";
        } else {
            $report .= "Conditions normales de travail.\n\n";
        }
        
        $report .= "3. Activités réalisées\n\n";
        if (!empty($dataText)) {
            $report .= $dataText . "\n";
        }
        
        if (!empty($notes)) {
            $report .= "Notes additionnelles :\n";
            $report .= $notes . "\n\n";
        }
        
        if (empty($dataText) && empty($notes)) {
            $report .= "Activités de chantier selon le planning établi.\n\n";
        }
        
        $report .= "4. Observations\n\n";
        $report .= "Travaux effectués dans des conditions normales.\n";
        $report .= "Respect des mesures de sécurité.\n";
        $report .= "Mobilisation des ressources selon les besoins.\n\n";
        
        $report .= "5. Recommandations\n\n";
        $report .= "Poursuivre les activités selon le planning.\n";
        $report .= "Maintenir la vigilance sur la sécurité.\n";
        $report .= "Surveiller l'évolution des conditions météorologiques.\n\n";
        
        $report .= "Rapport généré automatiquement le $date\n";
        
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
        $projects = $this->projectModel->getByUserId($user_id);
        $project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;
        $selectedMonth = isset($_GET['month']) ? max(1, min(12, intval($_GET['month']))) : intval(date('n'));
        $selectedYear = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
        $project = null;
        $sourceReports = [];
        $generatedMonthlyReports = [];
        $error = getFlash('error');
        $success = getFlash('success');

        if ($project_id) {
            if (!$this->projectModel->isOwner($project_id, $user_id)) {
                redirect('reports/monthly');
            }

            $project = $this->projectModel->getById($project_id);
            $sourceReports = $this->reportModel->getByProjectMonth($project_id, $user_id, $selectedYear, $selectedMonth, 'daily');
            $generatedMonthlyReports = $this->reportModel->getByProjectMonth($project_id, $user_id, $selectedYear, $selectedMonth, 'monthly');
        }

        require VIEWS_PATH . '/reports/monthly.php';
    }

    public function generateMonthly() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('reports/monthly');
        }

        $user_id = $_SESSION['user_id'];
        $project_id = intval($_POST['project_id'] ?? 0);
        $month = max(1, min(12, intval($_POST['month'] ?? date('n'))));
        $year = intval($_POST['year'] ?? date('Y'));

        if (!$project_id || !$this->projectModel->isOwner($project_id, $user_id)) {
            redirect('reports/monthly');
        }

        $project = $this->projectModel->getById($project_id);
        $reports = $this->reportModel->getByProjectMonth($project_id, $user_id, $year, $month, 'daily');

        if (empty($reports)) {
            setFlash('error', 'Aucun rapport journalier disponible pour ce projet sur la periode choisie.');
            redirect("reports/monthly&project_id=$project_id&month=$month&year=$year");
        }

        $content = $this->buildPeriodReportContent($project, $reports, 'monthly', $year, $month);
        $title = 'Rapport mensuel - ' . ($project['name'] ?? 'Projet') . ' - ' . sprintf('%02d/%04d', $month, $year);
        $reportDate = sprintf('%04d-%02d-01', $year, $month);

        $reportId = $this->reportModel->create([
            'project_id' => $project_id,
            'user_id' => $user_id,
            'title' => $title,
            'content' => $content,
            'report_type' => 'monthly',
            'report_date' => $reportDate
        ]);

        if ($reportId) {
            setFlash('success', 'Rapport mensuel genere avec succes.');
            redirect("reports&show_modal=$reportId");
        }

        setFlash('error', 'Erreur lors de la sauvegarde du rapport mensuel.');
        redirect("reports/monthly&project_id=$project_id&month=$month&year=$year");
    }

    /**
     * Afficher les rapports annuels
     */
    public function yearly() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $projects = $this->projectModel->getByUserId($user_id);
        $project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;
        $selectedYear = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
        $project = null;
        $sourceReports = [];
        $generatedAnnualReports = [];
        $error = getFlash('error');
        $success = getFlash('success');

        if ($project_id) {
            if (!$this->projectModel->isOwner($project_id, $user_id)) {
                redirect('reports/yearly');
            }

            $project = $this->projectModel->getById($project_id);
            $sourceReports = $this->reportModel->getByProjectYear($project_id, $user_id, $selectedYear, 'monthly');
            $generatedAnnualReports = $this->reportModel->getByProjectYear($project_id, $user_id, $selectedYear, 'annual');
        }

        require VIEWS_PATH . '/reports/yearly.php';
    }

    public function generateYearly() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('reports/yearly');
        }

        $user_id = $_SESSION['user_id'];
        $project_id = intval($_POST['project_id'] ?? 0);
        $year = intval($_POST['year'] ?? date('Y'));

        if (!$project_id || !$this->projectModel->isOwner($project_id, $user_id)) {
            redirect('reports/yearly');
        }

        $project = $this->projectModel->getById($project_id);
        $reports = $this->reportModel->getByProjectYear($project_id, $user_id, $year, 'monthly');

        if (empty($reports)) {
            setFlash('error', 'Aucun rapport mensuel disponible pour ce projet sur l annee choisie.');
            redirect("reports/yearly&project_id=$project_id&year=$year");
        }

        $content = $this->buildPeriodReportContent($project, $reports, 'annual', $year);
        $title = 'Rapport annuel - ' . ($project['name'] ?? 'Projet') . ' - ' . $year;
        $reportDate = sprintf('%04d-12-31', $year);

        $reportId = $this->reportModel->create([
            'project_id' => $project_id,
            'user_id' => $user_id,
            'title' => $title,
            'content' => $content,
            'report_type' => 'annual',
            'report_date' => $reportDate
        ]);

        if ($reportId) {
            setFlash('success', 'Rapport annuel genere avec succes.');
            redirect("reports&show_modal=$reportId");
        }

        setFlash('error', 'Erreur lors de la sauvegarde du rapport annuel.');
        redirect("reports/yearly&project_id=$project_id&year=$year");
    }

    private function buildPeriodReportContent($project, $reports, $periodType, $year, $month = null) {
        $periodLabel = $periodType === 'annual'
            ? "Annuel $year"
            : sprintf('Mensuel %02d/%04d', intval($month), intval($year));
        $title = $periodType === 'annual' ? 'Rapport annuel' : 'Rapport mensuel';
        $projectName = htmlspecialchars($project['name'] ?? 'Projet', ENT_QUOTES, 'UTF-8');
        $location = htmlspecialchars($project['location'] ?? '', ENT_QUOTES, 'UTF-8');
        $projectType = htmlspecialchars($project['project_type'] ?? '-', ENT_QUOTES, 'UTF-8');
        $maitreOuvrage = htmlspecialchars($project['maitre_ouvrage'] ?? $project['client'] ?? '-', ENT_QUOTES, 'UTF-8');
        $missionControle = htmlspecialchars($project['missions_controle'] ?? $project['control_mission'] ?? '-', ENT_QUOTES, 'UTF-8');

        $sourceText = $this->buildSourceReportsText($reports);
        $prompt = "Tu es un ingenieur en genie civil senior.\n";
        $prompt .= "Redige une synthese professionnelle en francais pour un $title.\n";
        $prompt .= "Le rapport doit contenir uniquement deux blocs: RESUME: puis RECOMMANDATIONS:.\n";
        $prompt .= "Ne recopie pas les rapports un par un. Fais une synthese globale claire, factuelle et utile.\n";
        $prompt .= "Projet: " . ($project['name'] ?? 'Projet') . "\n";
        $prompt .= "Localisation: " . ($project['location'] ?? '') . "\n";
        $prompt .= "Periode: $periodLabel\n";
        $prompt .= "Nombre de rapports sources: " . count($reports) . "\n\n";
        $prompt .= "Rapports sources:\n$sourceText\n";

        $aiContent = $this->callGeminiAPI($prompt);
        if ($aiContent === false) {
            $aiContent = $this->buildFallbackPeriodText($reports, $periodType);
        }

        $sections = $this->parseGeminiTextSections($aiContent);

        $html = '<div style="font-family:Arial,sans-serif;font-size:16px;line-height:1.45;color:#000;max-width:940px;margin:0 auto;background:#fff;">';
        $html .= '<h1 style="font-size:30px;line-height:1.15;margin:0 0 12px;font-weight:700;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' - ' . $projectName . ' - ' . htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8') . '</h1>';
        $html .= '<div style="border:1px solid #b7b7b7;background:#f4f4f4;width:430px;max-width:100%;padding:12px 14px;margin:0 0 18px;">';
        $html .= '<div><strong>Projet:</strong> ' . $projectName . '</div>';
        $html .= '<div><strong>Type de projet:</strong> ' . $projectType . '</div>';
        $html .= '<div><strong>Maitre d ouvrage:</strong> ' . $maitreOuvrage . '</div>';
        $html .= '<div><strong>Mission de controle:</strong> ' . $missionControle . '</div>';
        $html .= '<div><strong>Localisation:</strong> ' . $location . '</div>';
        $html .= '<div><strong>Periode:</strong> ' . htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div><strong>Rapports analyses:</strong> ' . count($reports) . '</div>';
        $html .= '</div>';

        $html .= '<h2 style="font-size:21px;margin:20px 0 8px;font-weight:700;">Rapports pris en compte</h2>';
        $html .= '<table style="width:100%;border-collapse:collapse;margin:0 0 20px;">';
        $html .= '<tr style="background:#d9d9d9;"><th style="padding:8px;border:1px solid #b7b7b7;text-align:left;">Date</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:left;">Titre</th><th style="padding:8px;border:1px solid #b7b7b7;text-align:left;">Type</th></tr>';
        foreach ($reports as $report) {
            $html .= '<tr>';
            $html .= '<td style="padding:8px;border:1px solid #b7b7b7;">' . htmlspecialchars(date('d/m/Y', strtotime($report['report_date'] ?? $report['created_at'])), ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding:8px;border:1px solid #b7b7b7;">' . htmlspecialchars($report['title'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding:8px;border:1px solid #b7b7b7;">' . htmlspecialchars($report['report_type'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        $html .= '<h2 style="font-size:21px;margin:20px 0 8px;font-weight:700;">Resume global</h2>';
        $html .= '<p style="margin:0 0 14px;text-align:justify;">' . htmlspecialchars($sections['resume'], ENT_QUOTES, 'UTF-8') . '</p>';
        $html .= '<h2 style="font-size:21px;margin:20px 0 8px;font-weight:700;">Recommandations</h2>';
        $html .= '<p style="margin:0 0 28px;text-align:justify;">' . htmlspecialchars($sections['recommandations'], ENT_QUOTES, 'UTF-8') . '</p>';
        $html .= '</div>';

        return $html;
    }

    private function buildSourceReportsText($reports) {
        $parts = [];
        foreach ($reports as $index => $report) {
            $content = html_entity_decode(strip_tags($report['content'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $content = preg_replace('/\s+/', ' ', trim($content));
            if (strlen($content) > 1800) {
                $content = substr($content, 0, 1800) . '...';
            }
            $date = !empty($report['report_date']) ? date('d/m/Y', strtotime($report['report_date'])) : '';
            $parts[] = 'Rapport ' . ($index + 1) . ' - ' . ($report['title'] ?? '') . ' - ' . $date . "\n" . $content;
        }

        return implode("\n\n", $parts);
    }

    private function buildFallbackPeriodText($reports, $periodType) {
        $count = count($reports);
        $label = $periodType === 'annual' ? 'rapports mensuels' : 'rapports journaliers';
        $firstTitles = array_slice(array_map(function ($report) {
            return $report['title'] ?? 'Rapport';
        }, $reports), 0, 5);

        $resume = "La synthese est etablie a partir de $count $label disponibles pour la periode selectionnee. Les documents analyses couvrent notamment : " . implode(', ', $firstTitles) . ". Les informations consolidees permettent de suivre l evolution generale du chantier, les actions executees, les ressources mobilisees et les points de vigilance mentionnes dans les rapports sources.";
        $recommandations = "Poursuivre le suivi regulier du chantier, consolider les observations importantes dans chaque rapport, verifier les points techniques recurrents et traiter rapidement les anomalies signalees. Les prochaines periodes doivent etre documentees avec des donnees completes afin de faciliter les syntheses et les decisions de pilotage.";

        return "RESUME: $resume\n\nRECOMMANDATIONS: $recommandations";
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
     * Nettoyer un message du chat pour l'inclure dans le prompt
     */
    private function cleanChatMessage($message) {
        if (empty($message)) return '';

        // Supprimer les balises HTML
        $message = strip_tags($message);

        // Supprimer les caractères de contrôle et les espaces multiples
        $message = preg_replace('/\s+/', ' ', $message);

        // Limiter la longueur pour éviter les prompts trop longs
        if (strlen($message) > 500) {
            $message = substr($message, 0, 500) . '...';
        }

        return trim($message);
    }

    /**
     * Générer un rapport via AJAX (retourne JSON pour modal)
     */
    public function generateReportAjax($project_id) {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Vérifier que le projet appartient à l'utilisateur
        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès refusé à ce projet']);
            exit;
        }

        $project = $this->projectModel->getById($project_id);
        if (!$project) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Projet non trouvé']);
            exit;
        }

        // Récupérer les données
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

        // Numérotation séquentielle par projet
        $reportNumber = $this->reportModel->countByProjectId($project_id) + 1;
        $currentUser = $this->getCurrentUserIdentity($user_id);
        $userFullName = $currentUser['name'];
        $userRole = $currentUser['role'];

        // Construire le prompt pour Gemini avec le NOUVEAU format
        $prompt = $this->buildNewGeminiPrompt($project, $draft, $reportNumber, $userFullName, $userRole);

        // Générer le contenu avec Gemini
        $reportContent = $this->callGeminiAPI($prompt);
        if ($reportContent === false) {
            $reportContent = $this->buildFallbackResumeFromDraft($project, $draft);
        }

        if ($reportContent === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la génération du rapport']);
            exit;
        }

        $reportContent = $this->sanitizeReportContent($reportContent);
        $sections = $this->parseGeminiTextSections($reportContent);
        $reportContent = $this->buildStructuredReportContent(
            $project,
            $draft,
            $reportNumber,
            "Ce compte-rendu synthetise les activites de suivi et de controle technique realisees sur le chantier du projet {$project['name']}.",
            $sections['resume'],
            $sections['recommandations'],
            $userFullName,
            $userRole
        );

        // Récupérer les images du chat IA
        $imagesChat = [];

        // Répondre en JSON
        echo json_encode([
            'success' => true,
            'report' => $reportContent,
            'report_number' => $reportNumber,
            'report_title' => "Rapport numéro $reportNumber",
            'project_id' => $project_id,
            'project_name' => $project['name'],
            'user_name' => $userFullName,
            'user_role' => $userRole
        ]);
        exit;
    }

    /**
     * Construire le prompt pour le NOUVEAU format professionnel
     */
    private function buildNewGeminiPrompt($project, $draft, $reportNumber = null, $userFullName = null, $userRole = null) {
        $date = date('d/m/Y');
        $userFullName = $userFullName ?? 'Nom non renseigne';
        $userRole = $userRole ?? 'Profil non renseigne';
        $reportLabel = $reportNumber ? "Rapport numéro $reportNumber" : 'Rapport de chantier';

        $prompt = "Tu es un ingénieur en génie civil senior avec 15 ans d'expérience.\n";
        $prompt .= "Génère un rapport de chantier professionnel en français.\n";
        $prompt .= "Utilise un style sobre, clair et professionnel.\n";
        $prompt .= "Présente les informations du projet verticalement dans l'en-tête, chacune sur sa propre ligne.\n";
        $prompt .= "Ne mets pas le mot 'ENTÊTE' dans le rapport.\n";
        $prompt .= "N'utilise pas de numérotation pour les sections.\n";
        $prompt .= "Ne fais pas apparaître de traits décoratifs, de tableaux ASCII ou de listes inutiles.\n";
        $prompt .= "La seule séparation autorisée est une ligne discrète sous l'en-tête.\n";
        $prompt .= "N'ajoute pas de signature ou de champ 'Rédigé par' dans le corps du rapport.\n";
        $prompt .= "Le nom et le rôle du rédacteur seront ajoutés par le système séparément.\n\n";

        $prompt .= "$reportLabel - $date\n\n";
        $prompt .= "Projet : {$project['name']}\n";
        $prompt .= "Type du projet : {$project['project_type']}\n";
        $prompt .= "Maître d'ouvrage : {$project['maitre_ouvrage']}\n";
        $prompt .= "Mission de contrôle : {$project['missions_controle']}\n";
        $prompt .= "Localisation : {$project['location']}\n";
        $prompt .= "Entreprise exécutante : Génie Concept Innovation\n";
        $prompt .= "Météo : " . ($draft['weather'] ?? 'Non spécifiée') . "\n";
        $prompt .= "Date du rapport : $date\n\n";

        $prompt .= "Informations du chantier\n";
        $prompt .= "Décris les ressources mobilisées, les équipements présents et l'avancement du chantier aujourd'hui. Si aucune donnée précise n'est disponible, indique qu'aucune information n'a été enregistrée.\n\n";

        $prompt .= "Équipements disponibles :\n";
        if (!empty($draft['equipments'])) {
            foreach ($draft['equipments'] as $eq) {
                $prompt .= "- {$eq['designation']} : présent {$eq['present']}, en marche {$eq['marche']}, immobilisé {$eq['immob']}, en panne {$eq['panne']}\n";
            }
        } else {
            $prompt .= "- Aucune donnée enregistrée\n";
        }
        $prompt .= "\n";

        $prompt .= "Personnel mobilisé :\n";
        $totalPersonnel = 0;
        if (!empty($draft['personnels'])) {
            foreach ($draft['personnels'] as $pers) {
                $prompt .= "- {$pers['profile']} : {$pers['nbr']} personne(s)\n";
                $totalPersonnel += intval($pers['nbr']);
            }
            $prompt .= "- Total personnel : $totalPersonnel personne(s)\n";
        } else {
            $prompt .= "- Aucune donnée enregistrée\n";
        }
        $prompt .= "\n";

        $prompt .= "Matériaux utilisés :\n";
        if (!empty($draft['materials'])) {
            foreach ($draft['materials'] as $mat) {
                $prompt .= "- {$mat['designation']} : {$mat['quantite']} {$mat['unite']}\n";
            }
        } else {
            $prompt .= "- Aucune donnée enregistrée\n";
        }
        $prompt .= "\n\n";

        $prompt .= "Résumé des travaux exécutés\n";
        $prompt .= "Rédige un paragraphe clair et professionnel en 4 à 6 phrases, sans numéros ni listes décoratives.\n\n";

        $chatMessages = $this->getChatMessagesForProject($project['id']);
        if (!empty($chatMessages)) {
            $prompt .= "Informations supplémentaires issues des échanges :\n";
            foreach ($chatMessages as $message) {
                $userMsg = $this->cleanChatMessage($message['user_message']);
                if (!empty($userMsg)) {
                    $prompt .= "- Utilisateur : $userMsg\n";
                }

                $aiMsg = $this->cleanChatMessage($message['ai_response']);
                if (!empty($aiMsg)) {
                    $prompt .= "- Assistant : $aiMsg\n";
                }
            }
            $prompt .= "\n";
        }

        $prompt .= "Recommandations\n";
        $prompt .= "Fournis des recommandations techniques précises et adaptées au chantier du jour.\n\n";

        $prompt .= "Ne mentionne pas l'expression IA ou intelligence artificielle.\n";
        $prompt .= "Rappel final: fournis uniquement RESUME et RECOMMANDATIONS, avec une analyse d'ingenieur expert, sans tableau, sans entete et sans signature.\n";

        return $prompt;
    }

    /**
     * Nettoyer le contenu du rapport pour supprimer les séparateurs inutiles
     */
    private function sanitizeReportContent($content) {
        if (!is_string($content)) {
            return $content;
        }

        $content = preg_replace('/^[\s\-=~_^*]{3,}$/m', '', $content);
        $content = preg_replace('/^Rédigé par\s*:\s*.*$/mi', '', $content);
        $content = preg_replace('/^Signature\s*:\s*_+.*$/mi', '', $content);
        $content = preg_replace('/^Signature\s*$/mi', '', $content);
        $content = preg_replace("/(\r?\n){3,}/", "\n\n", $content);
        return trim($content);
    }

    /**
     * Formater le contenu du rapport pour un rendu HTML propre
     */
    private function formatReportContentForHtml($content) {
        $content = $this->sanitizeReportContent($content);

        // Si le contenu contient des balises HTML encodées, on les décode d'abord.
        if (strpos($content, '&lt;') !== false) {
            $decoded = $this->decodeHtmlEntitiesRecursively($content);
            if (preg_match('/<(p|div|table|h[1-6]|ul|ol|li|strong|em|u|br|tr|td|th|header|section|article|span)[\s>]/i', $decoded)) {
                return $decoded;
            }
        }

        // Si le contenu contient déjà du HTML structuré, on le renvoie tel quel.
        if (preg_match('/<(p|div|table|h[1-6]|ul|ol|li|strong|em|u|br|tr|td|th|header|section|article|span)[\s>]/i', $content)) {
            return html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $content = preg_replace('/\r\n|\r/', "\n", $content);
        $content = preg_replace('/\n{2,}/', '</p><p>', $content);
        $content = nl2br($content);
        return '<p>' . $content . '</p>';
    }

    /**
     * Décode les entités HTML plusieurs fois si nécessaire
     */
    private function decodeHtmlEntitiesRecursively($content) {
        if (!is_string($content)) {
            return $content;
        }

        for ($i = 0; $i < 4; $i++) {
            $decoded = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $content) {
                break;
            }
            $content = $decoded;
        }

        return $content;
    }

    /**
     * Exporter le rapport en PDF (version AJAX)
     */
    public function exportReportAjaxPDF() {
        requireLogin();
        
        $content = $_POST['content'] ?? '';
        $projectId = $_POST['project_id'] ?? '';
        $userName = $_POST['user_name'] ?? 'Utilisateur';
        $userRole = $_POST['user_role'] ?? 'Non spécifié';
        $reportNumber = $_POST['report_number'] ?? null;
        $project = $this->projectModel->getById($projectId) ?: [];

        if (empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Contenu du rapport vide']);
            exit;
        }

        try {
            // Charger dompdf
            require_once ROOT_PATH . '/vendor/autoload.php';
            $dompdf = new \Dompdf\Dompdf();

            $formattedContent = $this->formatReportContentForHtml($content);

            // Récupérer la météo depuis le brouillon persistant (24h)
            $user_id = $_SESSION['user_id'] ?? null;
            $draftWeather = 'Non spécifiée';
            if ($user_id) {
                $draftRow = $this->reportDraftModel->getByProjectAndUser($projectId, $user_id);
                if ($draftRow) {
                    $updatedAt = strtotime($draftRow['updated_at']);
                    if ($updatedAt !== false && (time() - $updatedAt) <= 24 * 3600) {
                        $decoded = json_decode($draftRow['data'], true);
                        $draftWeather = htmlspecialchars($decoded['weather'] ?? 'Non spécifiée', ENT_QUOTES, 'UTF-8');
                    } else {
                        $this->reportDraftModel->deleteByProjectAndUser($projectId, $user_id);
                    }
                }
            }

            // Créer un HTML professionnel
            $html = "<!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <style>
                        body { font-family: Arial, sans-serif; color: #111827; margin: 28px; }
                        .report-body { font-size: 13px; line-height: 1.75; color: #1f2937; }
                        .report-body p { margin: 0 0 14px; }
                        .report-body strong { color: #111827; }
                        .signature { margin-top: 42px; padding-top: 18px; border-top: 1px solid #e5e7eb; color: #475569; }
                        .signature-line { margin-top: 32px; }
                        .signature-line span { display: inline-block; width: 280px; border-bottom: 1px solid #9ca3af; padding-bottom: 6px; color: #111827; }
                        .footer { margin-top: 28px; font-size: 11px; color: #6b7280; }
                    </style>
                </head>
                <body>
                    <div class='report-body'>
                        $formattedContent
                    </div>
                </body>
                </html>";

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Nettoyer les buffers
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Télécharger le PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="rapport-' . $projectId . '-' . date('Y-m-d-His') . '.pdf"');
            echo $dompdf->output();
            exit;

        } catch (Exception $e) {
            error_log('Erreur export PDF AJAX: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la génération du PDF']);
            exit;
        }
    }

    /**
     * Exporter le rapport en Word (version AJAX)
     */
    public function exportReportAjaxWord() {
        requireLogin();
        
        $content = $_POST['content'] ?? '';
        $projectId = $_POST['project_id'] ?? '';
        $userName = $_POST['user_name'] ?? 'Utilisateur';
        $userRole = $_POST['user_role'] ?? 'Non spécifié';
        $reportNumber = $_POST['report_number'] ?? null;
        $project = $this->projectModel->getById($projectId) ?: [];

        if (empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Contenu du rapport vide']);
            exit;
        }

        $formattedContent = $this->formatReportContentForHtml($content);

        $wordContent = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Calibri, sans-serif; margin: 1in; color: #111827; }
                    .report-body { font-size: 12pt; line-height: 1.6; color: #1f2937; }
                    .report-body p { margin: 0 0 12px; }
                    .signature { margin-top: 40px; padding-top: 16px; border-top: 1px solid #d1d5db; color: #475569; }
                    .signature-line { margin-top: 30px; }
                    .signature-line span { display: inline-block; width: 260px; border-bottom: 1px solid #9ca3af; padding-bottom: 6px; color: #111827; }
                </style>
            </head>
            <body>
                <div class='report-body'>
                    $formattedContent
                </div>
            </body>
            </html>";

        // Nettoyer les buffers
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Télécharger comme Word
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="rapport-' . $projectId . '-' . date('Y-m-d-His') . '.docx"');
        echo $wordContent;
        exit;
    }
}
