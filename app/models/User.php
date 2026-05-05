<?php
/**
 * Modèle User - Gestion des utilisateurs
 */

class User {
    private $pdo;
    private $table = 'users';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function create($data) {
        $this->ensureVerificationColumns();

        $query = "INSERT INTO {$this->table} (name, email, password, role, is_verified, verification_token) 
                  VALUES (:name, :email, :password, :role, :is_verified, :verification_token)";
        
        $stmt = $this->pdo->prepare($query);
        
        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $role = $data['role'] ?? 'ingénieur';
        $is_verified = $data['is_verified'] ?? 0;
        $verification_token = $data['verification_token'] ?? null;

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindValue(':is_verified', $is_verified, PDO::PARAM_INT);
        $stmt->bindParam(':verification_token', $verification_token);

        return $stmt->execute();
    }

    /**
     * Trouver un utilisateur par email
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Trouver un utilisateur par ID
     */
    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * S'assurer que la colonne photo existe
     */
    private function ensurePhotoColumn() {
        $query = "SHOW COLUMNS FROM {$this->table} LIKE 'photo'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        if (!$stmt->fetch()) {
            $this->pdo->exec("ALTER TABLE {$this->table} ADD COLUMN photo VARCHAR(255) DEFAULT NULL");
        }
    }

    /**
     * S'assurer que les colonnes de vérification email existent
     */
    private function ensureVerificationColumns() {
        $stmt = $this->pdo->prepare("SHOW COLUMNS FROM {$this->table} LIKE 'is_verified'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $this->pdo->exec("ALTER TABLE {$this->table} ADD COLUMN is_verified TINYINT(1) DEFAULT 0");
        }

        $stmt = $this->pdo->prepare("SHOW COLUMNS FROM {$this->table} LIKE 'verification_token'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $this->pdo->exec("ALTER TABLE {$this->table} ADD COLUMN verification_token VARCHAR(255) DEFAULT NULL");
        }
    }

    /**
     * Trouver un utilisateur par token de vérification
     */
    public function findByVerificationToken($token) {
        $query = "SELECT * FROM {$this->table} WHERE verification_token = :token";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Vérifier un utilisateur
     */
    public function verify($id) {
        $this->ensureVerificationColumns();
        $query = "UPDATE {$this->table} SET is_verified = 1, verification_token = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Vérifier les identifiants de connexion
     */
    public function login($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    /**
     * Vérifier si un email existe
     */
    public function emailExists($email) {
        $query = "SELECT id FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Vérifier si un email existe pour un autre utilisateur
     */
    public function emailExistsExceptId($email, $id) {
        $query = "SELECT id FROM {$this->table} WHERE email = :email AND id != :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET name = :name, email = :email";
        $params = [
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':id' => $id
        ];

        if (!empty($data['password'])) {
            $query .= ", password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (!empty($data['photo'])) {
            $this->ensurePhotoColumn();
            $query .= ", photo = :photo";
            $params[':photo'] = $data['photo'];
        }

        $query .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($query);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        return $stmt->execute();
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}
?>
