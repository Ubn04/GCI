# ✅ Modifications Page d'Inscription

## 🎯 Modifications Effectuées

### 1. Champ Profil - Input Text
**Avant** : Select avec options prédéfinies
```html
<select class="form-control" id="role" name="role" required>
    <option value="ingénieur">Ingénieur</option>
    <option value="chef_chantier">Chef de chantier</option>
    <option value="conducteur_travaux">Conducteur de travaux</option>
    <option value="technicien">Technicien</option>
</select>
```

**Après** : Input text libre
```html
<input type="text" class="form-control" id="role" name="role" 
       placeholder="Ex: Ingénieur, Chef de chantier, Technicien..." required>
```

### 2. Checkbox Conditions d'Utilisation
**Ajouté** : Checkbox obligatoire avant l'inscription
```html
<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
        <label class="form-check-label" for="terms">
            J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a>
        </label>
    </div>
</div>
```

### 3. Styles CSS Ajoutés
```css
.form-check {
    padding-left: 1.8em;
}
.form-check-input {
    width: 1.2em;
    height: 1.2em;
    margin-top: 0.15em;
    border: 2px solid #bfdbfe;
    border-radius: 6px;
    cursor: pointer;
}
.form-check-input:checked {
    background-color: #2563eb;
    border-color: #2563eb;
}
.form-check-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.18);
}
.form-check-label {
    color: #475569;
    font-size: 14px;
    cursor: pointer;
}
.form-check-label a {
    color: #2563eb;
    font-weight: 600;
}
.form-check-label a:hover {
    color: #1e40af;
}
```

## 📋 Formulaire Final

Le formulaire d'inscription contient maintenant :
1. ✅ **Nom complet** - Input text
2. ✅ **Profil** - Input text libre (au lieu de select)
3. ✅ **Email** - Input email
4. ✅ **Mot de passe** - Input password
5. ✅ **Conditions d'utilisation** - Checkbox obligatoire
6. ✅ **Bouton S'inscrire**

## 🎨 Apparence

### Champ Profil
- Input text avec placeholder explicatif
- L'utilisateur peut saisir n'importe quel profil
- Exemples suggérés : "Ingénieur, Chef de chantier, Technicien..."

### Checkbox Conditions
- Checkbox stylisée en bleu (#2563eb)
- Label cliquable
- Lien "conditions d'utilisation" en bleu
- **Obligatoire** (required) - impossible de s'inscrire sans cocher

## 🔒 Validation

### Côté Client (HTML5)
- Tous les champs sont `required`
- La checkbox doit être cochée pour soumettre le formulaire
- Email validé par le type `email`

### Côté Serveur (À vérifier)
Le contrôleur `AuthController::handleRegister()` doit vérifier :
```php
// Vérifier que la checkbox est cochée
if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
    $error = "Vous devez accepter les conditions d'utilisation";
    return;
}

// Le champ role est maintenant un texte libre
$role = trim($_POST['role']);
if (empty($role)) {
    $error = "Le profil est obligatoire";
    return;
}
```

## 📝 Notes

### Profil Libre
- L'utilisateur peut maintenant saisir n'importe quel profil
- Plus flexible que les options prédéfinies
- Permet des profils personnalisés

### Conditions d'Utilisation
- Checkbox obligatoire pour la conformité légale
- Le lien peut être modifié pour pointer vers une vraie page de CGU
- Actuellement : `href="#"` (à remplacer par l'URL des CGU)

## 🚀 Test

1. **Accéder** : `http://votre-site/?action=auth/register`
2. **Remplir** :
   - Nom : "Jean Dupont"
   - Profil : "Ingénieur Civil" (texte libre)
   - Email : "jean@exemple.com"
   - Mot de passe : "********"
3. **Cocher** : "J'accepte les conditions d'utilisation"
4. **Cliquer** : "S'inscrire"

### Comportement Attendu
- ✅ Si checkbox non cochée → Erreur HTML5 "Veuillez cocher cette case"
- ✅ Si tous les champs remplis + checkbox cochée → Inscription réussie
- ✅ Le profil saisi est enregistré tel quel dans la base de données

## 📁 Fichier Modifié

- **`app/views/auth/register.php`** - Page d'inscription mise à jour

---

**Date** : 5 mai 2026  
**Statut** : ✅ TERMINÉ  
**Modifications** : Profil en input text + Checkbox CGU obligatoire
