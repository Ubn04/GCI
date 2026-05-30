# Chat IA Gemini Multimodal - Guide Technique

## Vue d'ensemble

Le système de chat IA intègre l'API Google Gemini avec support multimodal complet :
- **Texte seul** : Envoyez des questions ou observations en texte
- **Image seule** : Envoyez une photo pour analyse
- **Texte + Image** : Envoyez les deux ensemble dans une seule requête

## Architecture

### Composants

#### 1. **GeminiService** (`app/services/GeminiService.php`)
Service PHP pour communiquer avec l'API Gemini

```php
// Initialisation
$gemini = new GeminiService($apiKey);

// Envoi multimodal (texte + image)
$response = $gemini->sendMultimodalMessage(
    $message,      // Texte (optionnel)
    $imagePath,    // Chemin de l'image (optionnel)
    $mimeType      // Type MIME de l'image
);
```

**Fonctionnalités clés :**
- Construction dynamique des `parts` (texte + image)
- Encodage base64 des images
- Gestion sécurisée des fichiers uploadés
- Parsing de la réponse Gemini

#### 2. **ChatAIController** (`app/controllers/ChatAIController.php`)
Contrôleur pour gérer les requêtes de chat

```php
// Envoyer un message multimodal
POST ?action=chat/send-message
Body: FormData {
    message: "Texte",
    project_id: 1,
    image: File (optionnel)
}

// Obtenir l'historique
GET ?action=chat/history?project_id=1
```

**Réponse JSON :**
```json
{
    "success": true,
    "message": "Message traité avec succès",
    "data": {
        "response": "Réponse IA",
        "finishReason": "STOP",
        "timestamp": "2024-05-11 14:30:00"
    }
}
```

#### 3. **Vue Chat** (`app/views/chat/index.php`)
Interface utilisateur Bootstrap 5 avec support multimodal

## Structure de la Requête Gemini

### Requête JSON envoyée à l'API

```json
{
    "contents": [
        {
            "parts": [
                {
                    "text": "Décrivez cette image"
                },
                {
                    "inline_data": {
                        "mime_type": "image/jpeg",
                        "data": "BASE64_ENCODED_IMAGE"
                    }
                }
            ]
        }
    ]
}
```

**Points clés :**
- Les `parts` contiennent à la fois le texte ET l'image
- UNE SEULE requête (pas deux requêtes séparées)
- L'image est encodée en base64
- Support des formats : JPEG, PNG, GIF, WebP

## Configuration

### Clé API Gemini

Dans `config/config.php` :

```php
define('GEMINI_API_KEY', 'YOUR_API_KEY_HERE');
```

Obtenir une clé gratuitement :
1. Aller sur https://makersuite.google.com/app/apikey
2. Créer une nouvelle clé
3. Copier la clé dans la configuration

### Modèles disponibles

- `gemini-2.5-flash-lite` (recommandé - gratuit, rapide)
- `gemini-2.5-flash` (plus puissant)

## Utilisation Frontend

### JavaScript - Envoyer un message multimodal

```javascript
const formData = new FormData();
formData.append('message', 'Texte du message');
formData.append('project_id', 1);
formData.append('image', imageFile); // Optionnel

const response = await fetch('?action=chat/send-message', {
    method: 'POST',
    body: formData
});

const data = await response.json();
```

## Validation

### Sécurité des fichiers

✅ **Vérifications implémentées :**
- Type MIME réel validé (pas d'extension falsifiée)
- Taille maximale : 5 MB
- Formats acceptés : JPEG, PNG, GIF, WebP
- Nettoyage automatique des fichiers temporaires
- Validation de l'authentification
- Vérification d'accès au projet

### Gestion des erreurs

```json
{
    "success": false,
    "message": "Image trop grande (max 5MB)",
    "data": null
}
```

## Routage

### Routes implémentées

| Route | Méthode | Description |
|-------|---------|-------------|
| `?action=chat` | GET | Afficher la page de chat |
| `?action=chat/index` | GET | Afficher la page de chat |
| `?action=chat/send-message` | POST | Envoyer un message multimodal |
| `?action=chat/history` | GET | Récupérer l'historique |

## Base de données

### Table `chat_messages` (créée automatiquement)

```sql
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    user_message LONGTEXT,
    ai_response LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX (project_id, created_at)
);
```

## Réponse de l'IA

La réponse de Gemini est extraite depuis :
```
candidates[0].content.parts[0].text
```

## Flux complet

```
1. Utilisateur envoie texte + image via formulaire
   ↓
2. JavaScript crée FormData avec message + fichier image
   ↓
3. Requête POST vers ?action=chat/send-message
   ↓
4. ChatAIController valide l'utilisateur et le projet
   ↓
5. Image est traitée : upload, validation, encodage base64
   ↓
6. GeminiService construit requête JSON avec parts
   ↓
7. Envoi à l'API Gemini via cURL
   ↓
8. Parsing de la réponse
   ↓
9. Sauvegarde en base de données
   ↓
10. Retour JSON au frontend
    ↓
11. Affichage du message et de la réponse dans le chat
```

## Exemple pratique

### Scénario : Analyser une photo de chantier

```javascript
// 1. Utilisateur envoie photo + message
const formData = new FormData();
formData.append('message', 'Quelle est l\'analyse de sécurité pour cette photo?');
formData.append('image', photoFile);
formData.append('project_id', 123);

// 2. Envoi
const response = await fetch('?action=chat/send-message', {
    method: 'POST',
    body: formData
});

// 3. Réponse de l'IA
{
    "success": true,
    "data": {
        "response": "La photo montre des violations de sécurité...",
        "finishReason": "STOP"
    }
}
```

## Dépannage

| Problème | Solution |
|----------|----------|
| Erreur 401 Unauthorized | Vérifier la clé API Gemini dans config.php |
| "Clé API non configurée" | Ajouter GEMINI_API_KEY dans config/config.php |
| "Image trop grande" | Réduire la taille à < 5MB |
| "Type de fichier non autorisé" | Utiliser JPEG, PNG, GIF ou WebP |
| Pas de réponse IA | Vérifier les logs de l'API Gemini |

## Améliorations futures

- [ ] Support des vidéos
- [ ] Support des audios
- [ ] Historique avec pagination
- [ ] Téléchargement de l'historique
- [ ] Filtres et recherche
- [ ] Conversations multiples
- [ ] Export PDF du chat

## Notes

- La clé API Gemini est gratuite et limitée à 60 requêtes/minute
- L'image et le texte sont toujours envoyés ensemble (pas de requêtes séparées)
- Les fichiers temporaires sont nettoyés automatiquement après le traitement
- L'historique est stocké dans la base de données pour un accès ultérieur
