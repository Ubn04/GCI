-- Script de migration pour ajouter les champs maitre_ouvrage et missions_controle à la table projects

-- Vérifier et ajouter la colonne maitre_ouvrage si elle n'existe pas
ALTER TABLE projects ADD COLUMN IF NOT EXISTS maitre_ouvrage VARCHAR(255) DEFAULT NULL;

-- Vérifier et ajouter la colonne missions_controle si elle n'existe pas
ALTER TABLE projects ADD COLUMN IF NOT EXISTS missions_controle TEXT DEFAULT NULL;

-- Afficher un message de confirmation
SELECT 'Migration terminée : Les colonnes maitre_ouvrage et missions_controle ont été ajoutées à la table projects.' AS message;
