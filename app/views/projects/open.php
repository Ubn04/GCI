<?php
/**
 * Interface ChatGPT 4 pour un projet
 */
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ouvrir le chat IA - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <style>
        .chat-shell {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 24px;
            flex: 1;
        }
        /* Chat Panel - Interface minimaliste */
        .chat-panel {
            background: white;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        
        .chat-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #06b6d4, #22d3ee);
        }
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 28px;
            text-align: center;
        }
        .welcome-message {
            font-size: 42px;
            font-weight: 300;
            color: #1e3a8a;
            margin-bottom: 36px;
            letter-spacing: -0.02em;
        }
        .chat-input-section {
            width: 100%;
            max-width: 100%;
            padding: 28px;
            border-top: 1px solid #e2e8f0;
            background: #eff6ff;
        }
        .file-list-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }
        .file-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e0f2fe;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 12px;
            color: #1e3a8a;
        }
        .file-chip button {
            background: none;
            border: none;
            cursor: pointer;
            color: #2563eb;
            padding: 0;
        }
        .input-wrapper {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        .file-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #2563eb;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .file-btn:hover {
            background: #eff6ff;
        }
        .file-input {
            display: none;
        }
        .message-input {
            flex: 1;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            font-family: inherit;
            resize: none;
            max-height: 80px;
        }
        .message-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .send-btn {
            background: #2563eb;
            border: none;
            cursor: pointer;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            transition: background 0.2s;
        }
        .send-btn:hover {
            background: #1e40af;
        }
        .send-btn:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }
        /* Side Panel */
        .side-panel {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .side-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            padding: 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .side-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
        }
        
        .side-card:first-child::before {
            background: linear-gradient(90deg, #9333ea, #c084fc);
        }
        
        .side-card:first-child h3 {
            color: #7c3aed;
        }
        
        .side-card:last-child::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        
        .side-card:last-child h3 {
            color: #059669;
        }
        
        .side-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        }
        
        .side-card h3 {
            margin-bottom: 18px;
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .form-control,
        .form-select {
            border-radius: 14px;
        }
        @media (max-width: 1200px) {
            .chat-shell {
                grid-template-columns: 1fr;
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
                            <i class="fas fa-robot"></i>
                            <span>Assistant IA</span>
                        </div>
                        <div class="header-title">
                            <h1>Chat IA du projet</h1>
                            <p>Envoyez des textes, images, vidéos ou audios pour générer votre rapport</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="?action=projects" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="content-area">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success mb-4"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <div class="chat-shell">
                <div class="chat-panel">
                    <div class="chat-container">
                        <div class="welcome-message">Toujours prêt à répondre.</div>
                    </div>
                    
                    <div class="chat-input-section">
                        <div id="fileListPreview" class="file-list-preview"></div>
                        
                        <form id="chatForm" method="POST" action="?action=reports/handle-generate&project_id=<?php echo $project['id']; ?>" enctype="multipart/form-data">
                            <div class="input-wrapper">
                                <button type="button" class="file-btn" onclick="document.getElementById('mediaInput').click()" title="Ajouter des fichiers">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <input 
                                    type="file" 
                                    id="mediaInput" 
                                    name="media[]" 
                                    class="file-input" 
                                    multiple 
                                    accept="image/*,video/*,audio/*,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                    onchange="updateFileList()"
                                >
                                <textarea 
                                    id="messageInput" 
                                    name="message_text" 
                                    class="message-input" 
                                    placeholder="Poser une question"
                                    rows="1"
                                ></textarea>
                                <button type="submit" class="send-btn" id="sendBtn">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>

                            <!-- Champs cachés pour le formulaire -->
                            <input type="hidden" id="hiddenNotes" name="notes" value="">
                            <input type="hidden" id="hiddenReportType" name="report_type" value="daily">
                        </form>
                    </div>
                </div>

                <div class="side-panel">
                    <div class="side-card">
                        <h3>Détails du rapport</h3>
                        <div class="mb-3">
                            <label for="report_type_select" class="form-label">Type de rapport</label>
                            <select id="report_type_select" class="form-select rounded-4">
                                <option value="daily">Rapport Journalier</option>
                                <option value="monthly">Rapport Mensuel</option>
                                <option value="annual">Rapport Annuel</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="report_notes" class="form-label">Notes supplémentaires</label>
                            <textarea id="report_notes" class="form-control rounded-4" rows="4" placeholder="Ajoutez des détails importants..."></textarea>
                        </div>
                    </div>

                    <div class="side-card">
                        <h3>Résumé du projet</h3>
                        <p class="text-muted mb-2"><strong>Projet :</strong> <?php echo htmlspecialchars($project['name']); ?></p>
                        <p class="text-muted mb-2"><strong>Localisation :</strong> <?php echo htmlspecialchars($project['location']); ?></p>
                        <p class="text-muted mb-2"><strong>Début :</strong> <?php echo date('d/m/Y', strtotime($project['start_date'])); ?></p>
                        <p class="text-muted mb-0"><strong>Données collectées :</strong> <?php echo count($siteData); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedFiles = [];
        const messageInput = document.getElementById('messageInput');
        const mediaInput = document.getElementById('mediaInput');
        const chatForm = document.getElementById('chatForm');
        const fileListDiv = document.getElementById('fileListPreview');
        const sendBtn = document.getElementById('sendBtn');

        // Auto-resize textarea
        messageInput.addEventListener('input', () => {
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 80) + 'px';
        });

        function updateFileList() {
            selectedFiles = Array.from(mediaInput.files);
            fileListDiv.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const fileChip = document.createElement('div');
                fileChip.className = 'file-chip';
                fileChip.innerHTML = `
                    <i class="fas fa-file"></i>
                    <span>${file.name}</span>
                    <button type="button" onclick="removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                fileListDiv.appendChild(fileChip);
            });
        }

        function removeFile(index) {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((file, i) => {
                if (i !== index) {
                    dataTransfer.items.add(file);
                }
            });
            mediaInput.files = dataTransfer.files;
            updateFileList();
        }

        // Gérer la soumission du formulaire
        chatForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Récupérer les valeurs
            const reportType = document.getElementById('report_type_select').value;
            const reportNotes = document.getElementById('report_notes').value;
            const messageText = messageInput.value.trim();
            
            // Vérifier qu'il y a au moins un message ou des fichiers
            if (!messageText && selectedFiles.length === 0 && !reportNotes) {
                alert('Veuillez saisir un message, ajouter des fichiers ou des notes avant d\'envoyer.');
                return;
            }
            
            // Combiner le message et les notes
            let finalNotes = '';
            if (messageText) {
                finalNotes = messageText;
            }
            if (reportNotes) {
                finalNotes = finalNotes ? (finalNotes + '\n\n' + reportNotes) : reportNotes;
            }
            
            // Remplir les champs cachés
            document.getElementById('hiddenNotes').value = finalNotes;
            document.getElementById('hiddenReportType').value = reportType;
            
            // Désactiver le bouton pour éviter les doubles soumissions
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Soumettre le formulaire
            chatForm.submit();
        });
    </script>
</body>
</html>
