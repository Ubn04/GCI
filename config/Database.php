<?php
/**
 * Classe de configuration et connexion à la base de données MySQL
 * Utilisation : $db = new Database(); $pdo = $db->connect();
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'chantier_ai';
    private $user = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $pdo;

    /**
     * Établir la connexion à la base de données
     */
    public function connect() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=' . $this->charset;
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
            return $this->pdo;
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    /**
     * Obtenir la connexion PDO
     */
    public function getPDO() {
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }
}
?>
