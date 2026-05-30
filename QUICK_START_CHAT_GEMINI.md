# 🤖 Chat IA Gemini - Démarrage Rapide

## Comment accéder au Chat IA?

### Option 1 : Depuis la page de génération
1. Allez sur "Générer rapport" d'un projet
2. Cliquez sur le lien **"Chat IA"** dans la boîte bleue
3. Le chat s'ouvre dans un nouvel onglet

### Option 2 : URL directe
```
http://localhost:8000/chantier-ai-php/?action=chat&project_id=1
```
Remplacez `1` par l'ID de votre projet

### Option 3 : Tester l'intégration
```
http://localhost:8000/chantier-ai-php/test_gemini_multimodal.php
```

## 💬 Utiliser le Chat

### 1️⃣ Texte uniquement

```
Message: "Quelle est la différence entre béton et ciment?"

→ L'IA répond comme un expert en génie civil
```

### 2️⃣ Image uniquement

```
1. Cliquez l'icône 🖼️ (image)
2. Sélectionnez une photo
3. Cliquez "Envoyer" (aucun texte requis)

→ L'IA analyse la photo
```

### 3️⃣ Texte + Image (Multimodal)

```
1. Écrivez votre question
   "Analysez les risques de sécurité"
   
2. Cliquez l'icône 🖼️
3. Sélectionnez une photo du chantier
4. Cliquez "Envoyer"

→ L'IA reçoit TOUT ensemble
→ Elle analyse la photo DANS LE CONTEXTE de votre question
```

## 🎯 Exemples d'utilisation

### Exemple 1 : Analyse de sécurité
```
Question: "Y a-t-il des risques de sécurité ici?"
Image: [Photo du chantier]
↓
Réponse: "Je remarque:
- Manque de casque de sécurité
- Équipement non normalisé
- Recommandations pour améliorer..."
```

### Exemple 2 : Conseil technique
```
Question: "Quel type de fondation utiliser pour ce sol?"
Image: [Photo de l'excavation]
↓
Réponse: "Basé sur ce que je vois:
- Type de sol: argile compacte
- Profondeur recommandée: 1.5m
- Armature suggérée..."
```

### Exemple 3 : Question générale
```
Question: "Explique les normes de construction"
↓
Réponse: "Les normes de construction incluent:
- Sécurité structurelle
- Accessibilité
- Efficacité énergétique..."
```

## ✨ Caractéristiques

| Fonctionnalité | Description |
|---|---|
| 💬 Chat en temps réel | Réponses instantanées de l'IA |
| 📸 Upload d'image | Jusqu'à 5MB (JPEG, PNG, GIF, WebP) |
| 🔗 Multimodal | Texte + image ensemble dans une requête |
| 💾 Historique | Tous les messages sont sauvegardés |
| 🔐 Sécurisé | Validation stricte + accès au projet vérifié |
| 📱 Responsive | Fonctionne sur PC, tablette, mobile |
| ⚡ Rapide | Modèle optimisé pour la vitesse |

## 🎨 Interface

```
┌─────────────────────────────────────┐
│  Assistant IA                    [←] │  Header
├─────────────────────────────────────┤
│                                     │
│  Message utilisateur avec image  ← │  Messages précédents
│  Réponse IA avec analyse        ← │
│                                     │
├─────────────────────────────────────┤
│ [🖼️] [____Message____] [Envoyer] │  Input area
├─────────────────────────────────────┤
│ Aperçu de l'image si sélectionnée   │
└─────────────────────────────────────┘
```

## ⌨️ Raccourcis clavier

| Raccourci | Action |
|-----------|--------|
| `Entrée` | Envoyer le message |
| `Shift + Entrée` | Nouvelle ligne dans le texte |

## ⚠️ Limites

- Taille max image : **5MB**
- Formats image : **JPEG, PNG, GIF, WebP**
- Limite API : **60 requêtes/minute**
- Historique : Les **50 derniers messages** sont chargés
- L'IA répond seulement au dernier message (pas de threading)

## 🆘 Problèmes courants

### ❌ "Clé API non configurée"
**Solution:** La clé Gemini doit être dans `config/config.php`
```php
define('GEMINI_API_KEY', 'YOUR_KEY');
```

### ❌ "Accès refusé au projet"
**Solution:** Vous devez être propriétaire du projet
- Vérifiez que vous êtes connecté
- Vérifiez que c'est votre projet

### ❌ "Image trop grande"
**Solution:** Réduisez la taille < 5MB
- Utilisez un logiciel de compression
- Ou prenez une photo en meilleure qualité

### ❌ "Pas de réponse"
**Solution:** 
- Vérifiez votre connexion internet
- Actualisez la page
- Vérifiez les logs serveur

## 🔄 Mise à jour de la clé API

Pour utiliser votre propre clé Gemini :

1. Allez sur https://makersuite.google.com/app/apikey
2. Cliquez "Create API Key"
3. Copie la clé
4. Dans `config/config.php`, remplacez :
   ```php
   define('GEMINI_API_KEY', 'VOTRE_CLE_ICI');
   ```
5. Sauvegardez et rechargez

## 📊 Historique

L'historique du chat est automatiquement sauvegardé dans la base de données.

Pour voir l'historique :
- L'historique se charge automatiquement au démarrage
- Les 50 derniers messages sont affichés
- Les nouveaux messages s'ajoutent au bas
- Rafraîchissez pour voir les anciens messages

## 🚀 Cas d'usage recommandés

✅ **Parfait pour :**
- Analyser des photos de chantier
- Poser des questions techniques
- Demander des conseils d'experts
- Valider des décisions
- Apprendre les normes
- Résoudre des problèmes

❌ **Pas recommandé pour :**
- Générer des rapports (utilisez le générateur)
- Calculs complexes
- Données confidentielles sensibles
- Sujet hors génie civil

## 📞 Support & Documentations

- **Guide complet:** [GUIDE_CHAT_GEMINI.md](GUIDE_CHAT_GEMINI.md)
- **Résumé technique:** [RESUME_IMPLEMENTATION_CHAT_GEMINI.md](RESUME_IMPLEMENTATION_CHAT_GEMINI.md)
- **Tests:** [test_gemini_multimodal.php](test_gemini_multimodal.php)

## 💡 Tips & Tricks

1. **Soyez spécifique** - Plus détaillée votre question, meilleure la réponse
2. **Contexte important** - L'IA comprend mieux avec un contexte
3. **Utilisez des images** - Une photo vaut mille mots
4. **Posez des suivis** - Vous pouvez continuer la conversation
5. **Sauvegardez les bonnes réponses** - Consultez votre historique

---

**Happy chatting! 🎉**

Pour commencer maintenant : [Ouvrir le Chat IA](?action=chat&project_id=1)
