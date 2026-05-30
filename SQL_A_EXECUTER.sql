-- ═══════════════════════════════════════════════════════════════
-- 🎯 SQL À EXÉCUTER DANS PHPMYADMIN
-- ═══════════════════════════════════════════════════════════════
-- 
-- INSTRUCTIONS :
-- 1. Ouvrir : http://localhost/phpmyadmin
-- 2. Cliquer sur : chantier_ai (à gauche)
-- 3. Cliquer sur : Onglet "SQL" (en haut)
-- 4. Copier-coller ce fichier complet
-- 5. Cliquer sur : "Exécuter"
-- 
-- RÉSULTAT ATTENDU : "2 lignes affectées"
-- ═══════════════════════════════════════════════════════════════

-- Sélectionner la base de données
USE chantier_ai;

-- Ajouter le champ client (Maître d'ouvrage)
ALTER TABLE projects 
ADD COLUMN client VARCHAR(200) DEFAULT 'Non spécifié' AFTER description;

-- Ajouter le champ control_mission (Mission contrôle)
ALTER TABLE projects 
ADD COLUMN control_mission VARCHAR(200) DEFAULT 'Non spécifié' AFTER client;

-- Mettre à jour les projets existants (optionnel)
UPDATE projects 
SET client = 'Non spécifié', control_mission = 'Non spécifié' 
WHERE client IS NULL OR control_mission IS NULL;

-- Vérifier la structure de la table
DESCRIBE projects;

-- ═══════════════════════════════════════════════════════════════
-- ✅ C'EST TOUT ! Tu peux maintenant tester le nouveau format !
-- ═══════════════════════════════════════════════════════════════
