# Fix: Erreur JSON "Unexpected token '<'" 

## Problème
Quand on envoie un message au chat ou génère un rapport, on reçoit:
```
Erreur : Unexpected token '<', "
"... is not valid JSON
```

Cela signifie que le serveur retourne du HTML au lieu de JSON.

## Causes Possibles
1. **Erreur PHP fatale** - Une exception ou erreur PHP non gérée retourne du HTML
2. **Timeout dépassé** - Le PHP timeout (120s) est atteint pendant le traitement
3. **API Gemini invalide** - La clé API ou configuration n'est pas correcte
4. **Mauvais format de réponse** - Gemini retourne du HTML au lieu de JSON

## Corrections Apportées

### 1. Gestionnaire d'Erreurs Global (index.php)
✅ Ajouté `set_error_handler()` et `set_exception_handler()`
- Capture toutes les erreurs PHP non gérées
- Retourne du JSON au lieu de HTML pour les requêtes AJAX
- Aide au débogage en affichant le message d'erreur réel

### 2. Augmentation des Timeouts
✅ ChatAIController::sendMessage() - `set_time_limit(300)` 
✅ ReportControllerGemini::callGeminiAPI() - `set_time_limit(300)`
- GeminiService déjà configuré avec 300s

### 3. Meilleur Débogage dans ChatAIController
✅ Vérification que la réponse Gemini a le bon format
✅ Messages d'erreur détaillés loggés dans error_log
✅ Vérification que `$geminiResponse['data']` existe

### 4. Messages d'Erreur JavaScript Améliorés
✅ Affiche maintenant la réponse réelle du serveur (premiers 300 caractères)
✅ Échappe le HTML pour l'affichage
✅ Logs console pour le débogage

## Comment Déboguer

Si vous recevez toujours l'erreur:

1. **Ouvrir la Console du Navigateur** (F12 → Console)
   - La première erreur affichera la réponse réelle du serveur

2. **Vérifier les Logs PHP**
   ```
   C:\xampp\apache\logs\error.log
   ```
   Ou regarder dans les fichiers de débogage de votre application

3. **Points de Vérification**
   - La clé API Gemini est-elle correcte? 
   - GEMINI_API_KEY est défini dans config/config.php?
   - Le serveur peut-il faire des requêtes cURL à googleapis.com?

## Logs Utiles à Vérifier

Dans `error_log`, cherchez:
- `DEBUG: Gemini error in chat:`
- `DEBUG: Gemini response malformed:`
- `DEBUG: GeminiService cURL error`
- `DEBUG: JSON decode error:`

## Résolution Rapide

1. Ouvrir la console navigateur (F12)
2. Essayer d'envoyer un message
3. Copier le message d'erreur qui s'affiche
4. Chercher ce message dans les logs PHP
5. Mettre à jour la configuration si nécessaire

## Test Rapide

Vous pouvez tester l'API Gemini directement avec:
```bash
curl -X POST "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{"contents": [{"parts": [{"text": "Bonjour"}]}]}'
```

Si cette requête retourne du HTML au lieu de JSON, c'est un problème d'API, pas de votre code.
