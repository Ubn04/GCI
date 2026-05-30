-- Ajout des colonnes manquantes à la table projects
ALTER TABLE projects 
ADD COLUMN maitre_ouvrage VARCHAR(150) DEFAULT NULL AFTER start_date,
ADD COLUMN missions_controle TEXT DEFAULT NULL AFTER maitre_ouvrage;
