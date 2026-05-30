-- Ajouter les champs manquants à la table projects pour le nouveau format de rapport

USE chantier_ai;

-- Ajouter le champ client (Maître d'ouvrage)
ALTER TABLE projects 
ADD COLUMN IF NOT EXISTS client VARCHAR(200) DEFAULT 'Non spécifié' AFTER description;

-- Ajouter le champ control_mission (Mission contrôle)
ALTER TABLE projects 
ADD COLUMN IF NOT EXISTS control_mission VARCHAR(200) DEFAULT 'Non spécifié' AFTER client;

-- Mettre à jour les projets existants
UPDATE projects 
SET client = 'Non spécifié', control_mission = 'Non spécifié' 
WHERE client IS NULL OR control_mission IS NULL;

-- Afficher la structure mise à jour
DESCRIBE projects;
