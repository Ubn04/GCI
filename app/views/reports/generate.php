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
        }
        
        .send-button:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
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
                                <div class="alert alert-info mt-3">
                                    <strong>Astuce :</strong> Utilisez le bouton ci-dessous pour revenir à la page d'information si besoin.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h2>Informations à envoyer à l'IA</h2>
                                <p>Ajoutez des notes, images ou vidéos avant de générer.</p>
                            </div>
                            <div class="card-body">
                                <div id="messagesArea" class="messages-area">
                                    <div class="empty-state" id="emptyState">
                                        <i class="fas fa-comment-dots"></i>
                                        <p>Commencez par envoyer un message ou ajouter un fichier.</p>
                                    </div>
                                </div>

                                <form id="reportForm" action="?action=reports/handle-generate&project_id=<?php echo $project['id']; ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="report_type" value="daily">
                                    <textarea name="notes" id="notesInput" hidden></textarea>
                                    <input type="file" id="fileInput" name="media[]" class="file-input" accept="image/*,video/*,audio/*" multiple>

                                    <div class="input-toolbar">
                                        <button type="button" class="attach-button" id="attachButton" title="Ajouter un fichier"><i class="fas fa-plus"></i></button>
                                        <textarea id="messageInput" class="message-input" placeholder="Écrivez un message pour l'IA..."></textarea>
                                        <button type="button" class="send-button" id="actionBtn" title="Enregistrer un audio"><i class="fas fa-microphone"></i></button>
                                    </div>
                                    <div class="recording-status" id="recordingStatus"></div>

                                    <div class="d-flex justify-content-between align-items-center mt-4 gap-3">
                                        <a href="?action=reports/project-info&project_id=<?php echo $project['id']; ?>" class="btn btn-secondary">Retour aux infos</a>
                                        <button type="submit" class="btn btn-primary" id="generateBtn" disabled>
                                            <span class="btn-text">Générer le rapport</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                    </div>
                                    <p class="text-muted mt-2 small">Le bouton est activé quand vous avez ajouté du texte ou des fichiers.</p>
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
        const storageKey = `report-draft-${projectId}`;
        const messageInput = document.getElementById('messageInput');
        const actionBtn = document.getElementById('actionBtn');
        const attachButton = document.getElementById('attachButton');
        const fileInput = document.getElementById('fileInput');
        const messagesArea = document.getElementById('messagesArea');
        const emptyState = document.getElementById('emptyState');
        const generateBtn = document.getElementById('generateBtn');
        const notesInput = document.getElementById('notesInput');
        const reportForm = document.getElementById('reportForm');
        const recordingStatus = document.getElementById('recordingStatus');

        let messages = [];
        let attachments = [];
        let isRecording = false;
        let mediaRecorder = null;
        let audioChunks = [];

        const draftData = loadDraft();
        if (draftData) {
            messages = draftData.messages || [];
            renderMessages();
        }

        updateButtons();

        messageInput.addEventListener('input', () => {
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 150) + 'px';
            updateActionButton();
        });

        attachButton.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', handleFileSelection);
        actionBtn.addEventListener('click', onActionButtonClick);
        reportForm.addEventListener('submit', prepareSubmit);

        function loadDraft() {
            const raw = localStorage.getItem(storageKey);
            if (!raw) return null;
            try {
                const parsed = JSON.parse(raw);
                if (!parsed.updatedAt || Date.now() - parsed.updatedAt > 24 * 60 * 60 * 1000) {
                    localStorage.removeItem(storageKey);
                    return null;
                }
                return parsed;
            } catch (err) {
                localStorage.removeItem(storageKey);
                return null;
            }
        }

        function saveDraft() {
            localStorage.setItem(storageKey, JSON.stringify({
                messages,
                updatedAt: Date.now()
            }));
        }

        function onActionButtonClick() {
            const text = messageInput.value.trim();
            if (text) {
                addTextMessage();
                return;
            }
            if (isRecording) {
                stopRecording();
            } else {
                startRecording();
            }
        }

        function addTextMessage() {
            const text = messageInput.value.trim();
            if (!text) return;
            messages.push({
                id: Date.now(),
                type: 'text',
                content: text,
                time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
            });
            messageInput.value = '';
            messageInput.style.height = 'auto';
            renderMessages();
            saveDraft();
            updateButtons();
        }

        function startRecording() {
            if (!navigator.mediaDevices || !window.MediaRecorder) {
                alert('Votre navigateur ne supporte pas l\'enregistrement audio.');
                return;
            }
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];
                    mediaRecorder.addEventListener('dataavailable', event => {
                        if (event.data.size > 0) audioChunks.push(event.data);
                    });
                    mediaRecorder.addEventListener('stop', () => {
                        stream.getTracks().forEach(track => track.stop());
                        const blob = new Blob(audioChunks, { type: 'audio/webm' });
                        const fileName = `voice-${Date.now()}.webm`;
                        const recordedFile = new File([blob], fileName, { type: 'audio/webm' });
                        attachments.push(recordedFile);
                        updateFileInput();
                        renderMessages();
                        saveDraft();
                        updateButtons();
                        recordingStatus.textContent = 'Audio enregistré.';
                    });
                    mediaRecorder.start();
                    isRecording = true;
                    recordingStatus.textContent = 'Enregistrement en cours...';
                    updateActionButton();
                })
                .catch(error => {
                    console.error(error);
                    alert('Impossible d\'accéder au microphone.');
                });
        }

        function stopRecording() {
            if (!mediaRecorder) return;
            mediaRecorder.stop();
            mediaRecorder = null;
            isRecording = false;
            updateActionButton();
        }

        function updateActionButton() {
            const icon = actionBtn.querySelector('i');
            if (messageInput.value.trim().length > 0) {
                icon.className = 'fas fa-paper-plane';
                actionBtn.title = 'Envoyer le message';
                actionBtn.classList.remove('recording');
            } else if (isRecording) {
                icon.className = 'fas fa-stop';
                actionBtn.title = 'Arrêter l\'enregistrement';
                actionBtn.classList.add('recording');
            } else {
                icon.className = 'fas fa-microphone';
                actionBtn.title = 'Enregistrer un audio';
                actionBtn.classList.remove('recording');
            }
        }

        function handleFileSelection() {
            if (!fileInput.files.length) return;
            const newFiles = Array.from(fileInput.files);
            attachments = attachments.concat(newFiles);
            updateFileInput();
            renderMessages();
            saveDraft();
            updateButtons();
        }

        function updateFileInput() {
            const dt = new DataTransfer();
            attachments.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
        }

        function renderMessages() {
            if (!messages.length && !attachments.length) {
                emptyState.style.display = 'grid';
                messagesArea.querySelectorAll('.message-row').forEach(node => node.remove());
                return;
            }
            emptyState.style.display = 'none';
            messagesArea.querySelectorAll('.message-row').forEach(node => node.remove());

            messages.forEach(msg => {
                const row = document.createElement('div');
                row.className = 'message-row';
                row.innerHTML = `
                    <div class="message-bubble">
                        <div class="message-meta"><span>Message</span><span>${msg.time}</span></div>
                        <div class="message-text">${escapeHtml(msg.content)}</div>
                    </div>
                `;
                messagesArea.appendChild(row);
            });

            if (attachments.length) {
                attachments.forEach(att => {
                    const row = document.createElement('div');
                    row.className = 'message-row';
                    row.innerHTML = `
                        <div class="message-bubble">
                            <div class="message-meta"><span>Fichier</span><span>${att.name}</span></div>
                            <div class="message-text">Type : ${escapeHtml(att.type || 'fichier')}</div>
                        </div>
                    `;
                    messagesArea.appendChild(row);
                });
            }
        }

        function updateButtons() {
            generateBtn.disabled = messages.length === 0 && attachments.length === 0;
        }

        function prepareSubmit(event) {
            const allNotes = messages.map(m => m.content).join('\n\n');
            notesInput.value = allNotes;
            const btn = generateBtn;
            const btnText = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner-border');
            btn.disabled = true;
            btnText.textContent = 'Génération en cours...';
            spinner.classList.remove('d-none');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
