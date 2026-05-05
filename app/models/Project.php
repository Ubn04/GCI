<?php
/**
 * Modèle Project - Gestion des projets de chantier
 */

class Project {
    private $pdo;
    private $table = 'projects';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouveau projet
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (user_id, name, location, project_type, description, start_date, maitre_ouvrage, missions_controle) 
                  VALUES (:user_id, :name, :location, :project_type, :description, :start_date, :maitre_ouvrage, :missions_controle)";
        
        $stmt = $this->pdo->prepare($query);

        $user_id = $data['user_id'];
        $name = $data['name'];
        $location = $data['location'];
        $description = $data['description'] ?? null;
        $project_type = $data['project_type'] ?? 'batiment';
        $start_date = $data['start_date'];
        $maitre_ouvrage = $data['maitre_ouvrage'] ?? null;
        $missions_controle = $data['missions_controle'] ?? null;

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':project_type', $project_type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':maitre_ouvrage', $maitre_ouvrage);
        $stmt->bindParam(':missions_controle', $missions_controle);

        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Obtenir tous les projets d'un utilisateur
     */
    public function getByUserId($user_id) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Obtenir un projet par ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Vérifier si l'utilisateur est propriétaire du projet
     */
    public function isOwner($project_id, $user_id) {
        $query = "SELECT id FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $project_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Mettre à jour un projet
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET name = :name, location = :location, project_type = :project_type, 
                  description = :description, start_date = :start_date, maitre_ouvrage = :maitre_ouvrage, 
                  missions_controle = :missions_controle WHERE id = :id";
        
        $stmt = $this->pdo->prepare($query);

        $name = $data['name'];
        $location = $data['location'];
        $project_type = $data['project_type'] ?? 'batiment';
        $description = $data['description'];
        $start_date = $data['start_date'];
        $maitre_ouvrage = $data['maitre_ouvrage'] ?? null;
        $missions_controle = $data['missions_controle'] ?? null;

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':project_type', $project_type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':maitre_ouvrage', $maitre_ouvrage);
        $stmt->bindParam(':missions_controle', $missions_controle);
        
        return $stmt->execute();
    }

    /**
     * Supprimer un projet
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Compter les projets d'un utilisateur
     */
    public function countByUserId($user_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>
