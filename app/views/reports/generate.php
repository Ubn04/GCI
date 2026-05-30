<?php
/**
 * Vue de génération de rapport avec sidebar fixe
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération de rapport - <?php echo htmlspecialchars($project['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <style>
        /* ===== STYLES PROFESSIONNELS POUR LA GÉNÉRATION DE RAPPORT ===== */
        
        /* Correction espacement */
        .content-area {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }
        
        /* Status pill moderne */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-radius: 12px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #bfdbfe;
            color: #1e40af;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: pulse 3s ease-in-out infinite;
        }
        
        .status-pill i {
            color: #2563eb;
            font-size: 14px;
        }
        
        /* Info summary cards */
        .info-summary {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 24px;
        }
        
        .info-card {
            padding: 20px;
            border-radius: 16px;
            background: white;
            border: 2px solid #f1f5f9;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #2563eb, #60a5fa);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .info-card:hover::before {
            transform: scaleY(1);
        }
        
        .info-card:hover {
            border-color: #e2e8f0;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        
        .info-card:nth-child(1)::before {
            background: linear-gradient(180deg, #06b6d4, #22d3ee);
        }
        
        .info-card:nth-child(2)::before {
            background: linear-gradient(180deg, #9333ea, #c084fc);
        }
        
        .info-card:nth-child(3)::before {
            background: linear-gradient(180deg, #10b981, #34d399);
        }
        
        .info-card:nth-child(4)::before {
            background: linear-gradient(180deg, #f59e0b, #fbbf24);
        }
        
        .info-card strong {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }
        
        .info-card span {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Messages area - Chat style */
        .messages-area {
            min-height: 450px;
            max-height: 550px;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
            border: 2px solid #e2e8f0;
            box-shadow: inset 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        
        .messages-area::-webkit-scrollbar {
            width: 6px;
        }
        
        .messages-area::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .messages-area::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .messages-area::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Empty state */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            color: #64748b;
            padding: 60px 40px;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #cbd5e1;
            animation: bounce 2s ease-in-out infinite;
        }
        
        .empty-state p {
            font-size: 15px;
            font-weight: 500;
            margin: 0;
        }
        
        /* Message bubbles */
        .message-row {
            display: flex;
            gap: 12px;
            animation: fadeInUp 0.4s ease-out;
        }
        
        .message-bubble {
            max-width: 85%;
            padding: 16px 20px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
            transition: all 0.3s ease;
        }
        
        .message-bubble:hover {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
            transform: translateY(-2px);
        }
        
        .message-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .message-meta span:first-child {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #2563eb;
        }
        
        .message-meta span:last-child {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }
        
        .message-text {
            white-space: pre-wrap;
            line-height: 1.7;
            color: #1e3a8a;
            font-size: 14px;
        }

        .report-preview {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            color: #0f172a;
        }

        .report-preview-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .report-preview-label {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f172a;
        }

        .report-preview-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
            color: #475569;
            font-size: 13px;
        }

        .report-preview-meta span {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 6px 14px;
        }

        .report-preview-body {
            font-size: 14px;
            line-height: 1.8;
            color: #1f2937;
        }

        .report-preview-body p {
            margin-bottom: 14px;
        }

        .report-preview-body strong {
            color: #0f172a;
        }

        .report-preview-body .signature {
            margin-top: 36px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            color: #475569;
        }

        .report-preview-body .signature-line {
            margin-top: 28px;
        }

        .report-preview-body .signature-line span {
            display: inline-block;
            width: 240px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 6px;
            color: #0f172a;
        }

        /* Header info grid for horizontal layout */
        .report-info-grid {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            align-items: flex-start;
            margin-top: 8px;
        }

        .report-info-item {
            min-width: 160px;
        }

        .report-info-item .label {
            display: block;
            font-size: 11px;
            color: #1e3a8a;
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-info-item .value {
            font-size: 13px;
            color: #0f172a;
        }
        
        /* Input toolbar */
        .input-toolbar {
            margin-top: 24px;
            display: grid;
            grid-template-columns: 56px minmax(0, 1fr) 56px;
            gap: 12px;
            align-items: end;
        }
        
        .attach-button, .send-button {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
        }
        
        .attach-button {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
        }
        
        .attach-button:hover {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transform: translateY(-2px) rotate(90deg);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }
        
        .send-button {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            transition: all 0.3s ease;
        }
        
        .send-button:hover:not(:disabled) {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        }

        /* État désactivé pour le microphone */
        .send-button:disabled {
            background: linear-gradient(135deg, #94a3b8, #64748b);
            color: white;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .send-button:disabled:hover {
            transform: none;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
        }
        
        .send-button.recording {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        .message-input {
            width: 100%;
            min-height: 56px;
            max-height: 150px;
            resize: none;
            border-radius: 16px;
            border: 2px solid #e2e8f0;
            padding: 16px 20px;
            font-size: 15px;
            line-height: 1.6;
            color: #1e3a8a;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .message-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .message-input::placeholder {
            color: #94a3b8;
        }
        
        /* Recording status */
        .recording-status {
            margin-top: 12px;
            min-height: 24px;
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .recording-status::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2563eb;
            animation: pulse 1s ease-in-out infinite;
        }
        
        /* File input hidden */
        .file-input {
            display: none;
        }
        
        /* Alert info styling */
        .alert-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #bfdbfe;
            border-radius: 16px;
            color: #1e40af;
            padding: 16px 20px;
            margin-top: 24px;
        }
        
        .alert-info strong {
            color: #1e3a8a;
        }
        
        /* Generate button enhanced */
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border: none;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
        }
        
        .btn-primary:disabled {
            background: #cbd5e1;
            box-shadow: none;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: white;
            border: 2px solid #e2e8f0;
            color: #64748b;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #475569;
            transform: translateY(-2px);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .info-summary {
                grid-template-columns: 1fr;
            }
            
            .input-toolbar {
                grid-template-columns: 48px minmax(0, 1fr) 48px;
            }
            
            .attach-button, .send-button {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }
            
            .message-input {
                min-height: 48px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include 'app/views/components/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-greeting">
                            <i class="fas fa-magic"></i>
                            <span>Génération IA</span>
                        </div>
                        <div class="header-title">
                            <h1>Génération de rapport</h1>
                            <p>Projet : <?php echo htmlspecialchars($project['name']); ?></p>
                        </div>
                    </div>
                    <div class="status-pill">
                        <i class="fas fa-clock"></i>
                        Brouillon conservé 24h
                    </div>
                </div>
            </div>
            
            <div class="content-area">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h2>Résumé des informations chantier</h2>
                                <p>Les détails renseignés sur la page d'information sont prêts.</p>
                            </div>
                            <div class="card-body">
                                <div class="info-summary">
                                    <div class="info-card">
                                        <strong>Météo active</strong>
                                        <span><?php echo htmlspecialchars($draft['weather'] ?? 'Ensoleille'); ?></span>
                                    </div>
                                    <div class="info-card">
                                        <strong>Équipements</strong>
                                        <span><?php echo count($draft['equipments']); ?></span>
                                    </div>
                                    <div class="info-card">
                                        <strong>Personnels</strong>
                                        <span><?php echo count($draft['personnels']); ?></span>
                                    </div>
                                    <div class="info-card">
                                        <strong>Matériaux</strong>
                                        <span><?php echo count($draft['materials']); ?></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h2>Assistant</h2>
                            </div>
                            <div class="card-body">
                                <!-- Chat Gemini -->
                                <div id="chatArea" class="messages-area" style="height: 350px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 16px; overflow-y: auto; background: #f8fafc;">
                                    <div class="empty-state" id="chatEmptyState"></div>
                                </div>

                                <!-- Chat Input -->
                                <div id="chatErrorContainer" style="margin-bottom: 12px;"></div>

                                <div class="input-toolbar" style="gap: 12px;">
                                    <button type="button" id="chatAttachBtn" class="attach-button" style="width: 44px; height: 44px; flex-shrink: 0;" title="Ajouter une image">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <textarea id="chatMessageInput" class="message-input" placeholder="Écrivez votre message..." style="min-height: 44px; max-height: 100px;"></textarea>
                                    <button type="button" id="chatSendBtn" class="send-button" style="width: 44px; height: 44px; flex-shrink: 0;" title="Envoyer">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                    <input type="file" id="chatFileInput" accept="image/*" style="display: none;" />
                                </div>

                                <!-- Image Preview -->
                                <div id="chatImagePreview" style="display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap;"></div>


                            </div>
                        </div>

                        <!-- Form d'envoi au rapport -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h2>Générer le rapport</h2>
                            </div>
                            <div class="card-body">
                                <form id="reportForm" action="?action=reports/handle-generate&project_id=<?php echo $project['id']; ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="report_type" value="daily">
                                    <textarea name="notes" id="notesInput" hidden></textarea>
                                    <input type="file" id="fileInput" name="media[]" class="file-input" accept="image/*,video/*,audio/*" multiple>

                                    <div class="d-flex justify-content-between align-items-center mt-2 gap-3">
                                        <a href="?action=reports/project-info&project_id=<?php echo $project['id']; ?>" class="btn btn-secondary">Retour aux infos</a>
                                        <button type="submit" class="btn btn-primary" id="generateBtn" disabled>
                                            <span class="btn-text">Générer le rapport</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                    </div>
                                    <p class="text-muted mt-2 small">Le bouton est activé quand vous avez des informations prêtes.</p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const projectId = <?php echo json_encode($project['id']); ?>;
        const projectData = <?php echo json_encode([
            'type' => $project['project_type'] ?? $project['type'] ?? '',
            'owner' => $project['maitre_ouvrage'] ?? $project['owner'] ?? '',
            'control' => $project['missions_controle'] ?? $project['control'] ?? '',
            'location' => $project['location'] ?? '',
            'company' => $project['company'] ?? '',
            'weather' => $draft['weather'] ?? '',
            'date' => date('d/m/Y')
        ]); ?>;
        const generateBtn = document.getElementById('generateBtn');
        const reportForm = document.getElementById('reportForm');
        const fileInput = document.getElementById('fileInput');

        // État simple pour le rapport
        let reportReady = false;

        /**
         * Mettre à jour l'état du bouton de génération
         */
        function updateGenerateButton() {
            generateBtn.disabled = false; // Toujours activé maintenant
        }

        updateGenerateButton();

        /**
         * Gérer la soumission du formulaire - AJAX sans redirection
         */
        reportForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            generateBtn.disabled = true;
            const spinner = generateBtn.querySelector('.spinner-border');
            const btnText = generateBtn.querySelector('.btn-text');
            
            if (spinner && btnText) {
                spinner.classList.remove('d-none');
                btnText.textContent = 'Génération en cours...';
            }
            
                try {
                    // Envoyer les données via FormData (inclut fichiers et notes)
                    const formData = new FormData(reportForm);

                    const response = await fetch(reportForm.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Marquer pour rafraîchir la page project_info si elle est ouverte
                        try {
                            localStorage.setItem('refresh_project_' + projectId, Date.now());
                        } catch (e) {}

                        // Afficher le rapport dans le modal avec le HTML renvoyé
                        const previewHtml = result.preview_html || result.report || '';
                        displayReportModal(previewHtml, null, projectId, '<?php echo addslashes($project['name']); ?>', 'Rapport généré', '<?php echo addslashes($_SESSION['user']['name'] ?? $_SESSION['user_name'] ?? ''); ?>', result.report_id || '', '<?php echo addslashes($_SESSION['user']['role'] ?? $_SESSION['user_role'] ?? ''); ?>');
                    } else {
                        showError('Erreur : ' + (result.message || 'Impossible de générer le rapport'));
                    }
            } catch (error) {
                console.error('Erreur AJAX:', error);
                showError('Erreur : ' + error.message);
            } finally {
                generateBtn.disabled = false;
                if (spinner && btnText) {
                    spinner.classList.add('d-none');
                    btnText.textContent = 'Générer le rapport';
                }
            }
        });

        /**
         * Afficher le rapport dans un modal
         */
        function displayReportModal(reportContent, images, projectId, projectName, reportTitle, userName, reportNumber, userRole) {
            // Créer ou récupérer le modal existant
            let reportModal = document.getElementById('reportModal');
            if (!reportModal) {
                reportModal = createReportModal();
                document.body.appendChild(reportModal);
            }

            // Remplir le contenu du rapport avec le HTML renvoyé par le serveur
            const reportContentDiv = document.getElementById('reportContent');
            reportContentDiv.innerHTML = reportContent;

            // Afficher le modal
            const modal = new bootstrap.Modal(reportModal);
            modal.show();

            // Gérer les clics sur les boutons
            const closeBtn = reportModal.querySelector('[data-action="close"]');
            const pdfBtn = reportModal.querySelector('[data-action="pdf"]');
            const wordBtn = reportModal.querySelector('[data-action="word"]');

            closeBtn.onclick = () => modal.hide();
            pdfBtn.onclick = () => exportReportPDF(reportContent, projectId, userName, reportNumber, userRole);
            wordBtn.onclick = () => exportReportWord(reportContent, projectId, userName, reportNumber, userRole);
        }

        /**
         * Créer le modal de rapport
         */
        function createReportModal() {
            const modal = document.createElement('div');
            modal.id = 'reportModal';
            modal.className = 'modal fade';
            modal.setAttribute('tabindex', '-1');
            modal.innerHTML = `
                <div class="modal-dialog modal-lg" style="max-height: 90vh;">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-file-alt"></i> Rapport généré
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="max-height: calc(90vh - 200px); overflow-y: auto; background: #f8f9fa;">
                            <div id="reportContent"></div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-action="close" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Fermer
                            </button>
                            <button type="button" class="btn btn-success" data-action="pdf">
                                <i class="fas fa-file-pdf"></i> Exporter PDF
                            </button>
                            <button type="button" class="btn btn-info text-white" data-action="word">
                                <i class="fas fa-file-word"></i> Exporter Word
                            </button>
                        </div>
                    </div>
                </div>
            `;
            return modal;
        }

        /**
         * Exporter le rapport en PDF
         */
        function exportReportPDF(reportContent, projectId, userName, reportNumber, userRole) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `?action=reports/export-pdf`;

            form.appendChild(createHiddenInput('content', reportContent));
            form.appendChild(createHiddenInput('project_id', projectId));
            form.appendChild(createHiddenInput('user_name', userName));
            form.appendChild(createHiddenInput('report_number', reportNumber));
            form.appendChild(createHiddenInput('user_role', userRole));

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        /**
         * Exporter le rapport en Word
         */
        function exportReportWord(reportContent, projectId, userName, reportNumber, userRole) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `?action=reports/export-word`;

            form.appendChild(createHiddenInput('content', reportContent));
            form.appendChild(createHiddenInput('project_id', projectId));
            form.appendChild(createHiddenInput('user_name', userName));
            form.appendChild(createHiddenInput('report_number', reportNumber));
            form.appendChild(createHiddenInput('user_role', userRole));

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        /**
         * Créer un input hidden
         */
        function createHiddenInput(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            return input;
        }

        /**
         * Afficher une erreur
         */
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger alert-dismissible fade show';
            errorDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            reportForm.parentElement.insertBefore(errorDiv, reportForm);
            
            // Disparaître après 5 secondes
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }


        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ============ CHAT GEMINI INTÉGRÉ ============

        const chatArea = document.getElementById('chatArea');
        const chatEmptyState = document.getElementById('chatEmptyState');
        const chatMessageInput = document.getElementById('chatMessageInput');
        const chatSendBtn = document.getElementById('chatSendBtn');
        const chatAttachBtn = document.getElementById('chatAttachBtn');
        const chatFileInput = document.getElementById('chatFileInput');
        const chatImagePreview = document.getElementById('chatImagePreview');
        const chatErrorContainer = document.getElementById('chatErrorContainer');

        let selectedChatImage = null;
        let mediaRecorder = null;
        let audioChunks = [];
        let isRecording = false;

        // Event listeners
        chatAttachBtn.addEventListener('click', () => chatFileInput.click());
        chatFileInput.addEventListener('change', handleChatImageSelect);
        chatSendBtn.addEventListener('click', handleChatSendBtnClick);
        chatMessageInput.addEventListener('input', updateChatSendButtonState);
        chatMessageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                // Envoyer le message uniquement s'il y a du texte
                if (chatMessageInput.value.trim().length > 0) {
                    sendChatMessage();
                }
            }
        });

        // Initialiser l'état du bouton au chargement
        updateChatSendButtonState();

        /**
         * Gérer la sélection d'image pour le chat
         */
        function handleChatImageSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                showChatError('Veuillez sélectionner une image');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showChatError('Image trop grande (max 5MB)');
                return;
            }

            selectedChatImage = file;
            showChatImagePreview(file);
            updateChatSendButtonState();
        }

        /**
         * Afficher l'aperçu de l'image
         */
        function showChatImagePreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                chatImagePreview.innerHTML = `
                    <div style="position: relative; width: 60px; height: 60px; border-radius: 8px; overflow: hidden; border: 2px solid #e2e8f0; background: #f8fafc;">
                        <img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        <button type="button" onclick="removeChatImage()" style="position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; background: #ef4444; color: white; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }

        /**
         * Supprimer l'image
         */
        function removeChatImage() {
            selectedChatImage = null;
            chatImagePreview.innerHTML = '';
            chatFileInput.value = '';
            updateChatSendButtonState();
        }

        /**
         * Mettre à jour l'état du bouton avec changement d'icône
         */
        function updateChatSendButtonState() {
            const hasMessage = chatMessageInput.value.trim().length > 0;
            const hasImage = selectedChatImage !== null;
            const isEnabled = hasMessage || hasImage;
            
            chatSendBtn.disabled = !isEnabled;
            
            // Changer l'icône en fonction du contenu
            const icon = chatSendBtn.querySelector('i');
            if (isRecording) {
                // En cours d'enregistrement
                icon.className = 'fas fa-stop';
                chatSendBtn.title = 'Arrêter l\'enregistrement';
            } else if (hasMessage || hasImage) {
                // S'il y a du texte, afficher l'icône d'envoi
                icon.className = 'fas fa-paper-plane';
                chatSendBtn.disabled = false;
                chatSendBtn.title = 'Envoyer';
            } else {
                // Sinon, afficher l'icône microphone
                icon.className = 'fas fa-microphone';
                chatSendBtn.title = 'Enregistrer un message audio';
                chatSendBtn.disabled = false;
            }
        }

        /**
         * Gérer le clic sur le bouton d'envoi/microphone
         */
        async function handleChatSendBtnClick() {
            const hasMessage = chatMessageInput.value.trim().length > 0;
            const hasImage = selectedChatImage !== null;

            // Si y a du texte ou image, envoyer le message
            if (hasMessage || hasImage) {
                sendChatMessage();
            } else {
                // Sinon, gérer l'enregistrement audio
                if (!isRecording) {
                    startAudioRecording();
                } else {
                    stopAudioRecording();
                }
            }
        }

        /**
         * Démarrer l'enregistrement audio
         */
        async function startAudioRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                audioChunks = [];
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    sendAudioMessage(audioBlob);
                    
                    // Arrêter le stream
                    stream.getTracks().forEach(track => track.stop());
                };

                mediaRecorder.start();
                isRecording = true;
                
                // Mettre à jour l'UI
                const icon = chatSendBtn.querySelector('i');
                icon.className = 'fas fa-stop';
                chatSendBtn.classList.add('recording');
                chatSendBtn.disabled = false;
                chatSendBtn.title = 'Arrêter l\'enregistrement';
                
                // Afficher le statut
                showRecordingStatus();

            } catch (error) {
                showChatError('Erreur d\'accès au microphone: ' + error.message);
            }
        }

        /**
         * Arrêter l'enregistrement audio
         */
        function stopAudioRecording() {
            if (mediaRecorder && isRecording) {
                mediaRecorder.stop();
                isRecording = false;
                
                // Mettre à jour l'UI
                const icon = chatSendBtn.querySelector('i');
                icon.className = 'fas fa-paper-plane';
                chatSendBtn.classList.remove('recording');
                chatSendBtn.title = 'Envoyer';
                
                // Masquer le statut
                hideRecordingStatus();
            }
        }

        /**
         * Envoyer le message audio
         */
        async function sendAudioMessage(audioBlob) {
            try {
                addChatMessage('user', '[Message audio]', null);
                
                showChatLoader();

                const formData = new FormData();
                formData.append('message', '[Audio envoyé]');
                formData.append('project_id', projectId);
                formData.append('audio', audioBlob, 'audio.webm');

                const response = await fetch('?action=chat/send-message', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const responseText = await response.text();
                let data;

                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    removeChatLoader();
                    // Afficher la réponse actuelle pour le débogage
                    const preview = responseText.substring(0, 200).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    console.error('Réponse serveur invalide:', responseText);
                    showChatError('Réponse serveur invalide: ' + preview);
                    return;
                }

                removeChatLoader();

                if (!response.ok) {
                    showChatError('Erreur serveur : ' + response.status);
                    return;
                }

                if (!data.success) {
                    showChatError('Erreur : ' + (data.message || 'Erreur inconnue'));
                    return;
                }

                addChatMessage('ai', data.data.response, null);
                updateChatSendButtonState();

            } catch (error) {
                removeChatLoader();
                showChatError('Erreur lors de l\'envoi de l\'audio: ' + error.message);
            }
        }

        /**
         * Afficher le statut d'enregistrement
         */
        function showRecordingStatus() {
            const statusEl = document.createElement('div');
            statusEl.id = 'recordingStatus';
            statusEl.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #ef4444;
                color: white;
                padding: 12px 24px;
                border-radius: 24px;
                font-size: 14px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);
                z-index: 1000;
                animation: slideUp 0.3s ease;
            `;
            statusEl.innerHTML = `
                <div style="width: 8px; height: 8px; background: white; border-radius: 50%; animation: pulse 1s ease-in-out infinite;"></div>
                <span>Enregistrement en cours...</span>
            `;
            document.body.appendChild(statusEl);
        }

        /**
         * Masquer le statut d'enregistrement
         */
        function hideRecordingStatus() {
            const statusEl = document.getElementById('recordingStatus');
            if (statusEl) {
                statusEl.remove();
            }
        }
        async function sendChatMessage() {
            const message = chatMessageInput.value.trim();
            const image = selectedChatImage;

            if (!message && !image) {
                showChatError('Veuillez envoyer un message ou une image');
                return;
            }

            clearChatError();

            // Afficher le message utilisateur
            if (message) {
                addChatMessage('user', message, null);
            }
            if (image) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    addChatMessage('user', message || '[Image envoyée]', e.target.result);
                };
                reader.readAsDataURL(image);
            }

            // Nettoyer
            chatMessageInput.value = '';
            removeChatImage();
            updateChatSendButtonState();

            // Afficher loader
            showChatLoader();

            try {
                const formData = new FormData();
                formData.append('message', message);
                formData.append('project_id', projectId);
                if (image) {
                    formData.append('image', image);
                }

                const response = await fetch('?action=chat/send-message', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const responseText = await response.text();
                let data;

                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    removeChatLoader();
                    const preview = responseText.substring(0, 300).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    console.error('Réponse JSON invalide:', responseText);
                    showChatError('Réponse serveur invalide: ' + preview + '...');
                    return;
                }

                removeChatLoader();

                if (!response.ok) {
                    showChatError('Erreur serveur : ' + response.status + ' - ' + (data.message || response.statusText));
                    return;
                }

                if (!data.success) {
                    showChatError('Erreur : ' + (data.message || 'Erreur inconnue'));
                    return;
                }

                addChatMessage('ai', data.data.response, null);

            } catch (error) {
                removeChatLoader();
                showChatError('Erreur de connexion : ' + error.message);
            }
        }

        async function sendChatRequest(message, image) {
            const formData = new FormData();
            formData.append('message', message);
            formData.append('project_id', projectId);
            if (image) {
                formData.append('image', image);
            }

            const response = await fetch('?action=chat/send-message', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            });

            const responseText = await response.text();
            let data;

            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                const preview = responseText.substring(0, 300).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                console.error('Reponse JSON invalide:', responseText);
                throw new Error('Reponse serveur invalide: ' + preview + '...');
            }

            if (!response.ok) {
                throw new Error('Erreur serveur : ' + response.status + ' - ' + (data.message || response.statusText));
            }

            if (!data.success) {
                throw new Error(data.message || 'Erreur inconnue');
            }

            return data;
        }

        async function resizeChatImage(file) {
            if (!file || !file.type.startsWith('image/')) {
                return file;
            }

            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const img = new Image();
                    img.onload = () => {
                        const maxSide = 900;
                        const ratio = Math.min(1, maxSide / Math.max(img.width, img.height));
                        const width = Math.round(img.width * ratio);
                        const height = Math.round(img.height * ratio);

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                resolve(file);
                                return;
                            }

                            resolve(new File([blob], 'chat-image.jpg', { type: 'image/jpeg' }));
                        }, 'image/jpeg', 0.65);
                    };
                    img.onerror = () => resolve(file);
                    img.src = event.target.result;
                };
                reader.onerror = () => resolve(file);
                reader.readAsDataURL(file);
            });
        }

        async function sendChatMessage() {
            const message = chatMessageInput.value.trim();
            const image = selectedChatImage ? await resizeChatImage(selectedChatImage) : null;

            if (!message && !image) {
                showChatError('Veuillez envoyer un message ou une image');
                return;
            }

            clearChatError();

            if (image) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    addChatMessage('user', '[Image envoyee pour analyse]', e.target.result);
                };
                reader.readAsDataURL(image);
            }

            chatMessageInput.value = '';
            removeChatImage();
            updateChatSendButtonState();

            try {
                if (image) {
                    showChatLoader();
                    const imageData = await sendChatRequest('', image);
                    removeChatLoader();
                    addChatMessage('ai', imageData.data.response, null);
                }

                if (message) {
                    addChatMessage('user', message, null);
                    showChatLoader();
                    const messageData = await sendChatRequest(message, null);
                    removeChatLoader();
                    addChatMessage('ai', messageData.data.response, null);
                }
            } catch (error) {
                removeChatLoader();
                showChatError('Erreur de connexion : ' + error.message);
            }
        }

        /**
         * Ajouter un message au chat
         */
        function addChatMessage(sender, text, imageData = null) {
            if (chatEmptyState) {
                chatEmptyState.style.display = 'none';
            }

            const messageDiv = document.createElement('div');
            messageDiv.style.cssText = `
                display: flex;
                gap: 12px;
                margin-bottom: 12px;
                animation: slideUp 0.3s ease;
                ${sender === 'user' ? 'justify-content: flex-end;' : ''}
            `;

            const contentStyle = sender === 'user' 
                ? 'background: linear-gradient(135deg, #2563eb, #1e40af); color: white; border-bottom-right-radius: 4px;'
                : 'background: white; color: #1e3a8a; border: 1px solid #e2e8f0; border-bottom-left-radius: 4px;';

            let content = `<div style="max-width: 70%; padding: 12px 16px; border-radius: 12px; word-wrap: break-word; line-height: 1.5; ${contentStyle}">`;
            if (text) {
                content += `<div style="font-size: 14px;">${escapeHtml(text)}</div>`;
            }
            if (imageData) {
                content += `<img src="${imageData}" style="max-width: 100%; border-radius: 8px; margin: 8px 0; max-height: 200px; object-fit: contain;">`;
            }
            content += `<div style="font-size: 12px; opacity: 0.7; margin-top: 4px;">${formatTime(new Date())}</div>`;
            content += `</div>`;

            messageDiv.innerHTML = content;
            chatArea.appendChild(messageDiv);
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        /**
         * Afficher un loader
         */
        function showChatLoader() {
            const loaderDiv = document.createElement('div');
            loaderDiv.id = 'chat-loader';
            loaderDiv.style.cssText = `
                display: flex;
                gap: 12px;
                margin-bottom: 12px;
                align-items: center;
            `;
            loaderDiv.innerHTML = `
                <div style="display: flex; gap: 6px; padding: 12px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb; animation: bounce 1.4s ease-in-out infinite;"></div>
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb; animation: bounce 1.4s ease-in-out infinite 0.2s;"></div>
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb; animation: bounce 1.4s ease-in-out infinite 0.4s;"></div>
                </div>
            `;
            chatArea.appendChild(loaderDiv);
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        /**
         * Supprimer le loader
         */
        function removeChatLoader() {
            const loader = document.getElementById('chat-loader');
            if (loader) {
                loader.remove();
            }
        }

        /**
         * Afficher erreur
         */
        function showChatError(message) {
            chatErrorContainer.innerHTML = `
                <div style="background: #fee; border: 1px solid #fcc; color: #dc2626; padding: 12px; border-radius: 8px; font-size: 13px; display: flex; gap: 8px;">
                    <i class="fas fa-exclamation-circle" style="flex-shrink: 0; margin-top: 2px;"></i>
                    <span>${escapeHtml(message)}</span>
                </div>
            `;
        }

        /**
         * Effacer les erreurs
         */
        function clearChatError() {
            chatErrorContainer.innerHTML = '';
        }

        /**
         * Formater l'heure
         */
        function formatTime(date) {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        }

        // Ajouter CSS pour animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            @keyframes bounce {
                0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
                40% { transform: scale(1); opacity: 1; }
            }
        `;
        document.head.appendChild(style);

        updateChatSendButtonState();
    </script>
</body>
</html>
