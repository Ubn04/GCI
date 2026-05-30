-- Fix foreign key constraints to ensure proper cascading deletes
USE chantier_ai;

-- Drop existing constraints and recreate with ON DELETE CASCADE
ALTER TABLE chat_messages DROP FOREIGN KEY chat_messages_ibfk_1;
ALTER TABLE chat_messages ADD CONSTRAINT chat_messages_ibfk_1 
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;

-- Ensure other tables also have proper cascading
ALTER TABLE site_data DROP FOREIGN KEY site_data_ibfk_1;
ALTER TABLE site_data ADD CONSTRAINT site_data_ibfk_1 
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;

ALTER TABLE reports DROP FOREIGN KEY reports_ibfk_1;
ALTER TABLE reports ADD CONSTRAINT reports_ibfk_1 
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;

-- Verify the constraints are set correctly
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'chantier_ai' 
AND REFERENCED_TABLE_NAME IS NOT NULL;
