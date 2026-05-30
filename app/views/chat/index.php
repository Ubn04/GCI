<?php
/**
 * Vue Chat IA - Interface de chat multimodal avec Gemini
 * Support : texte seul, image seule, ou texte + image ensemble
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat IA - <?php echo htmlspecialchars($project['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --success: #10b981;
            --danger: #ef4444;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, #f1f5f9 100%);
            min-height: 100vh;
        }

        .chat-container {
            max-width: 900px;
            margin: 0 auto;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: white;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .chat-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-header .project-name {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            margin: 0;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-2px);
        }

        /* Messages Area */
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: var(--light-bg);
        }

        .messages-container::-webkit-scrollbar {
            width: 8px;
        }

        .messages-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .messages-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Welcome Message */
        .welcome-message {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .welcome-message i {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 16px;
            display: block;
        }

        .welcome-message h3 {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 8px;
        }

        .welcome-message p {
            font-size: 14px;
            margin: 0;
        }

        /* Message Styles */
        .message {
            display: flex;
            gap: 12px;
            animation: slideUp 0.3s ease;
        }

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

        .message.user {
            justify-content: flex-end;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 12px;
            word-wrap: break-word;
            line-height: 1.5;
        }

        .message.user .message-content {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-bottom-right-radius: 4px;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
        }

        .message.ai .message-content {
            background: white;
            color: #1e3a8a;
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 4px;
        }

        .message-image {
            max-width: 100%;
            border-radius: 8px;
            margin: 8px 0;
            max-height: 300px;
            object-fit: contain;
        }

        .message-time {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 4px;
        }

        /* Input Area */
        .input-area {
            background: white;
            border-top: 1px solid var(--border-color);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .input-toolbar {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .input-field {
            flex: 1;
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        #messageInput {
            width: 100%;
            min-height: 44px;
            max-height: 120px;
            resize: none;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
            outline: none;
        }

        #messageInput:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
            background: transparent;
            color: var(--primary);
        }

        .btn-attach {
            border: 2px solid var(--border-color);
            color: var(--primary);
        }

        .btn-attach:hover {
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.05);
            transform: scale(1.05);
        }

        .btn-send {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-send:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-send.loading {
            position: relative;
            color: transparent;
        }

        .btn-send.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Image Preview */
        .image-preview {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .preview-item {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--border-color);
            background: var(--light-bg);
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-remove {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .preview-remove:hover {
            transform: scale(1.1);
        }

        /* Loader */
        .loader {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
        }

        .loader .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary);
            animation: bounce 1.4s ease-in-out infinite;
        }

        .loader .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .loader .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }

        /* Error Message */
        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: var(--danger);
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        /* Hidden elements */
        #fileInput {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .message-content {
                max-width: 85%;
            }

            .chat-header h1 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Header -->
        <div class="chat-header">
            <div>
                <h1><i class="fas fa-robot"></i> Assistant IA</h1>
                <p class="project-name">Projet: <?php echo htmlspecialchars($project['name']); ?></p>
            </div>
            <a href="?action=reports/project-info&project_id=<?php echo $project['id']; ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <!-- Messages Container -->
        <div class="messages-container" id="messagesContainer">
            <div class="welcome-message">
                <i class="fas fa-wand-magic-sparkles"></i>
                <h3>Bienvenue dans le Chat IA</h3>
                <p>Envoyez un message, une image ou les deux pour obtenir une analyse d'expert en génie civil.</p>
            </div>
        </div>

        <!-- Input Area -->
        <div class="input-area">
            <!-- Image Preview -->
            <div class="image-preview" id="imagePreview"></div>

            <!-- Error Message Container -->
            <div id="errorContainer"></div>

            <!-- Input Toolbar -->
            <div class="input-toolbar">
                <div class="input-field">
                    <button type="button" class="btn-icon btn-attach" id="attachBtn" title="Ajouter une image">
                        <i class="fas fa-image"></i>
                    </button>
                    <textarea id="messageInput" placeholder="Décrivez votre question, observation ou image..."></textarea>
                    <input type="file" id="fileInput" accept="image/*" />
                </div>
                <button type="button" class="btn-icon btn-send" id="sendBtn" title="Envoyer">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const projectId = <?php echo json_encode($project['id']); ?>;
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const attachBtn = document.getElementById('attachBtn');
        const fileInput = document.getElementById('fileInput');
        const messagesContainer = document.getElementById('messagesContainer');
        const imagePreview = document.getElementById('imagePreview');
        const errorContainer = document.getElementById('errorContainer');

        let selectedImage = null;

        // Event Listeners
        messageInput.addEventListener('input', updateSendButtonState);
        messageInput.addEventListener('keydown', handleKeyDown);
        attachBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', handleImageSelect);
        sendBtn.addEventListener('click', sendMessage);

        /**
         * Gérer la sélection d'image
         */
        function handleImageSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Vérifier le type
            if (!file.type.startsWith('image/')) {
                showError('Veuillez sélectionner une image');
                return;
            }

            // Vérifier la taille (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showError('Image trop grande (max 5MB)');
                return;
            }

            selectedImage = file;
            showImagePreview(file);
            updateSendButtonState();
        }

        /**
         * Afficher l'aperçu de l'image
         */
        function showImagePreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.innerHTML = `
                    <div class="preview-item">
                        <img src="${e.target.result}" alt="Aperçu">
                        <button type="button" class="preview-remove" onclick="removeImage()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }

        /**
         * Supprimer l'image sélectionnée
         */
        function removeImage() {
            selectedImage = null;
            imagePreview.innerHTML = '';
            fileInput.value = '';
            updateSendButtonState();
        }

        /**
         * Mettre à jour l'état du bouton d'envoi
         */
        function updateSendButtonState() {
            const hasMessage = messageInput.value.trim().length > 0;
            const hasImage = selectedImage !== null;
            sendBtn.disabled = !hasMessage && !hasImage;
        }

        /**
         * Gérer l'envoi avec Entrée (Shift+Entrée pour nouvelle ligne)
         */
        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

        /**
         * Envoyer le message multimodal (texte + image)
         * Les deux sont envoyés ENSEMBLE dans une seule requête
         */
        async function sendMessage() {
            const message = messageInput.value.trim();
            const image = selectedImage;

            // Valider
            if (!message && !image) {
                showError('Veuillez envoyer un message ou une image');
                return;
            }

            clearError();

            // Afficher le message utilisateur
            if (message) {
                addMessageToUI('user', message, null);
            }
            if (image) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    addMessageToUI('user', message || '[Image envoyée]', e.target.result);
                };
                reader.readAsDataURL(image);
            }

            // Nettoyer l'input
            messageInput.value = '';
            removeImage();
            updateSendButtonState();

            // Afficher le loader
            showLoader();

            try {
                // Construire la requête multimodale
                const formData = new FormData();
                formData.append('message', message);
                formData.append('project_id', projectId);
                if (image) {
                    formData.append('image', image);
                }

                // Envoyer la requête
                const response = await fetch('?action=chat/send-message', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // Supprimer le loader
                removeLoader();

                if (!data.success) {
                    showError('Erreur: ' + (data.message || 'Erreur inconnue'));
                    return;
                }

                // Afficher la réponse IA
                addMessageToUI('ai', data.data.response, null);

            } catch (error) {
                removeLoader();
                showError('Erreur de connexion: ' + error.message);
                console.error('Error:', error);
            }
        }

        /**
         * Ajouter un message à l'interface
         */
        function addMessageToUI(sender, text, imageData = null) {
            // Supprimer le message de bienvenue
            const welcome = messagesContainer.querySelector('.welcome-message');
            if (welcome) {
                welcome.remove();
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = 'message ' + sender;

            let content = `<div class="message-content">`;
            if (text) {
                content += `<div>${escapeHtml(text)}</div>`;
            }
            if (imageData) {
                content += `<img src="${imageData}" class="message-image" alt="Image">`;
            }
            content += `<div class="message-time">${formatTime(new Date())}</div>`;
            content += `</div>`;

            messageDiv.innerHTML = content;
            messagesContainer.appendChild(messageDiv);

            // Scroller vers le bas
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        /**
         * Afficher un loader
         */
        function showLoader() {
            const loaderDiv = document.createElement('div');
            loaderDiv.id = 'loader';
            loaderDiv.className = 'message ai';
            loaderDiv.innerHTML = `
                <div class="message-content">
                    <div class="loader">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>
            `;
            messagesContainer.appendChild(loaderDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        /**
         * Supprimer le loader
         */
        function removeLoader() {
            const loader = document.getElementById('loader');
            if (loader) {
                loader.remove();
            }
        }

        /**
         * Afficher un message d'erreur
         */
        function showError(message) {
            errorContainer.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>${escapeHtml(message)}</span>
                </div>
            `;
        }

        /**
         * Effacer les erreurs
         */
        function clearError() {
            errorContainer.innerHTML = '';
        }

        /**
         * Formater l'heure
         */
        function formatTime(date) {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        }

        /**
         * Échapper les caractères HTML
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Charger l'historique (optionnel)
        loadHistory();

        async function loadHistory() {
            try {
                const response = await fetch(`?action=chat/history&project_id=${projectId}`);
                const data = await response.json();

                if (data.success && data.data && data.data.length > 0) {
                    messagesContainer.innerHTML = ''; // Effacer le message de bienvenue

                    data.data.forEach(msg => {
                        if (msg.user_message) {
                            addMessageToUI('user', msg.user_message);
                        }
                        if (msg.ai_response) {
                            addMessageToUI('ai', msg.ai_response);
                        }
                    });
                }
            } catch (error) {
                console.error('Erreur chargement historique:', error);
            }
        }
    </script>
</body>
</html>
