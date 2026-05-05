<?php
/**
 * Modèle SiteData - Gestion des données terrain collectées
 */

class SiteData {
    private $pdo;
    private $table = 'site_data';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer une nouvelle entrée de données terrain
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (project_id, user_id, content, data_type, file_url) 
                  VALUES (:project_id, :user_id, :content, :data_type, :file_url)";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':project_id', $data['project_id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':data_type', $data['data_type'] ?? 'text');
        $stmt->bindParam(':file_url', $data['file_url'] ?? null);

        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Obtenir toutes les données d'un projet
     */
    public function getByProjectId($project_id) {
        $query = "SELECT * FROM {$this->table} WHERE project_id = :project_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Obtenir une entrée par ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Supprimer une entrée
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Obtenir les données d'un projet pour la génération de rapport
     */
    public function getDataForReport($project_id) {
        $query = "SELECT content, data_type FROM {$this->table} 
                  WHERE project_id = :project_id 
                  ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Compter les données d'un projet
     */
    public function countByProjectId($project_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE project_id = :project_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>
