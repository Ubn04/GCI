<?php
/**
 * Classe de configuration et connexion à la base de données MySQL
 * Utilisation : $db = new Database(); $pdo = $db->connect();
 */

class Database {
    private $host;
    private $port;
    private $db_name;
    private $user;
    private $password;
    private $charset = 'utf8mb4';
    private $pdo;

    public function __construct() {
        // Lire les valeurs depuis les constantes de configuration (qui lisent les variables d'environnement)
        $this->host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->port = defined('DB_PORT') ? DB_PORT : 3306;
        $this->db_name = defined('DB_NAME') ? DB_NAME : 'chantier_ai';
        $this->user = defined('DB_USER') ? DB_USER : 'root';
        $this->password = defined('DB_PASS') ? DB_PASS : '';
    }

    /**
     * Établir la connexion à la base de données
     */
    public function connect() {
        $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';charset=' . $this->charset;
        
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
