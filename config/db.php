<?php
// Configuration de la base de données
class Database {
    private $host = "localhost";
    private $db_name = "phantomshield_db";
    private $username = "root";
    private $password = ""; // À remplacer par un mot de passe fort en production
    private $conn;

    // Méthode pour se connecter à la base de données
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
        }

        return $this->conn;
    }
}
?>
