<?php
class Session {
    private $conn;
    private $table_name = "sessions";
    
    // Propriétés de la session
    public $id;
    public $user_id;
    public $ip_address;
    public $user_agent;
    public $created_at;
    public $expires_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Création d'une nouvelle session
    public function create($user_id, $duration = 3600) {
        try {
            // Génération d'un ID de session sécurisé
            $this->id = bin2hex(random_bytes(32));
            $this->user_id = $user_id;
            $this->ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // Date d'expiration
            $expires = date('Y-m-d H:i:s', time() + $duration);
            $this->expires_at = $expires;
            
            $query = "INSERT INTO " . $this->table_name . "
                    (id, user_id, ip_address, user_agent, expires_at)
                    VALUES
                    (:id, :user_id, :ip_address, :user_agent, :expires_at)";
            
            $stmt = $this->conn->prepare($query);
            
            // Liaison des paramètres
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":ip_address", $this->ip_address);
            $stmt->bindParam(":user_agent", $this->user_agent);
            $stmt->bindParam(":expires_at", $this->expires_at);
            
            // Exécution de la requête
            if($stmt->execute()) {
                // Stockage de l'ID de session dans un cookie
                $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
                $httponly = true;
                setcookie('session_id', $this->id, time() + $duration, '/', '', $secure, $httponly);
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la création de la session: " . $e->getMessage());
            return false;
        }
    }
    
    // Vérification de la validité de la session
    public function validate($session_id) {
        try {
            $now = date('Y-m-d H:i:s');
            
            $query = "SELECT s.id, s.user_id, u.account_status 
                     FROM " . $this->table_name . " s
                     JOIN users u ON s.user_id = u.id
                     WHERE s.id = :session_id 
                     AND s.expires_at > :now 
                     AND u.account_status = 'active'
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":session_id", $session_id);
            $stmt->bindParam(":now", $now);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['user_id'];
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la validation de la session: " . $e->getMessage());
            return false;
        }
    }
    
    // Destruction d'une session
    public function destroy($session_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :session_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":session_id", $session_id);
            
            // Suppression du cookie
            setcookie('session_id', '', time() - 3600, '/');
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur lors de la destruction de la session: " . $e->getMessage());
            return false;
        }
    }
    
    // Nettoyage des sessions expirées
    public function cleanExpiredSessions() {
        try {
            $now = date('Y-m-d H:i:s');
            
            $query = "DELETE FROM " . $this->table_name . " WHERE expires_at < :now";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":now", $now);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur lors du nettoyage des sessions expirées: " . $e->getMessage());
            return false;
        }
    }
}
?>
