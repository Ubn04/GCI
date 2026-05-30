<?php
/**
 * Modèle ReportDraft - Gestion des brouillons de rapports par projet/utilisateur
 */

class ReportDraft {
    private $pdo;
    private $table = 'report_drafts';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function saveOrUpdate($user_id, $project_id, $data) {
        $json = json_encode($data);
        // Vérifier si un brouillon existe
        $query = "SELECT id FROM {$this->table} WHERE user_id = :user_id AND project_id = :project_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) {
            $update = "UPDATE {$this->table} SET data = :data, updated_at = NOW() WHERE id = :id";
            $u = $this->pdo->prepare($update);
            $u->bindParam(':data', $json);
            $u->bindParam(':id', $row['id']);
            return $u->execute();
        } else {
            $insert = "INSERT INTO {$this->table} (user_id, project_id, data, created_at, updated_at) VALUES (:user_id, :project_id, :data, NOW(), NOW())";
            $i = $this->pdo->prepare($insert);
            $i->bindParam(':user_id', $user_id);
            $i->bindParam(':project_id', $project_id);
            $i->bindParam(':data', $json);
            return $i->execute();
        }
    }

    public function getByProjectAndUser($project_id, $user_id) {
        $query = "SELECT * FROM {$this->table} WHERE project_id = :project_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch();
        if (!$row) return null;
        return $row;
    }

    public function deleteByProjectAndUser($project_id, $user_id) {
        $query = "DELETE FROM {$this->table} WHERE project_id = :project_id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    public function purgeExpired($hours = 24) {
        $query = "DELETE FROM {$this->table} WHERE updated_at < (NOW() - INTERVAL :hours HOUR)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

?>
