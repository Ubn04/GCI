<?php
/**
 * Modèle Report - Gestion des rapports générés
 */

class Report {
    private $pdo;
    private $table = 'reports';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouveau rapport
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (project_id, user_id, title, content, report_type, report_date) 
                  VALUES (:project_id, :user_id, :title, :content, :report_type, :report_date)";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':project_id', $data['project_id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':report_type', $data['report_type']);
        $stmt->bindParam(':report_date', $data['report_date']);

        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Obtenir tous les rapports d'un utilisateur
     */
    public function getByUserId($user_id, $search = '') {
        $query = "SELECT r.*, p.name as project_name FROM {$this->table} r
                  JOIN projects p ON r.project_id = p.id
                  WHERE r.user_id = :user_id";

        if (!empty($search)) {
            $query .= " AND (r.title LIKE :search OR p.name LIKE :search)";
        }

        $query .= " ORDER BY r.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bindParam(':search', $searchParam);
        }

        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Obtenir les rapports d'un projet
     */
    public function getByProjectId($project_id) {
        $query = "SELECT * FROM {$this->table} WHERE project_id = :project_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Obtenir un rapport par ID
     */
    public function getById($id) {
        $query = "SELECT r.*, p.name as project_name 
                  FROM {$this->table} r 
                  LEFT JOIN projects p ON r.project_id = p.id 
                  WHERE r.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Supprimer un rapport
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Compter les rapports d'un utilisateur
     */
    public function countByUserId($user_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Compter les rapports générés aujourd'hui
     */
    public function countTodayByUserId($user_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id AND DATE(created_at) = CURDATE()";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Compter les rapports générés ce mois-ci
     */
    public function countThisMonthByUserId($user_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Compter les rapports générés cette année
     */
    public function countThisYearByUserId($user_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id AND YEAR(created_at) = YEAR(CURDATE())";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Obtenir les rapports récents (5 derniers)
     */
    public function getRecent($user_id, $limit = 5) {
        $query = "SELECT r.*, p.name as project_name FROM {$this->table} r
                  JOIN projects p ON r.project_id = p.id
                  WHERE r.user_id = :user_id 
                  ORDER BY r.created_at DESC 
                  LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getMonthlySummaryByUserId($user_id, $year = null) {
        $year = $year ?: date('Y');
        $query = "SELECT MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count,
                  MAX(created_at) as last_generated_at
                  FROM {$this->table}
                  WHERE user_id = :user_id AND YEAR(created_at) = :year
                  GROUP BY MONTH(created_at), YEAR(created_at)
                  ORDER BY MONTH(created_at) DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAnnualSummaryByUserId($user_id) {
        $query = "SELECT YEAR(created_at) as year, COUNT(*) as count,
                  MAX(created_at) as last_generated_at
                  FROM {$this->table}
                  WHERE user_id = :user_id
                  GROUP BY YEAR(created_at)
                  ORDER BY YEAR(created_at) DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>
