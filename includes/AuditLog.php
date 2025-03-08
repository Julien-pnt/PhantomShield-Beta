<?php
class AuditLog {
    private $conn;
    private $table_name = "audit_logs";
    
    // Propriétés du journal d'audit
    public $id;
    public $user_id;
    public $action;
    public $details;
    public $ip_address;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Journalisation d'une action
    public function log($user_id, $action, $details = "") {
        try {
            $query = "INSERT INTO " . $this->table_name . "
                    (user_id, action, details, ip_address)
                    VALUES
                    (:user_id, :action, :details, :ip_address)";
            
            $stmt = $this->conn->prepare($query);
            
            $this->user_id = $user_id;
            $this->action = htmlspecialchars(strip_tags($action));
            $this->details = htmlspecialchars(strip_tags($details));
            $this->ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            
            // Liaison des paramètres
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":action", $this->action);
            $stmt->bindParam(":details", $this->details);
            $stmt->bindParam(":ip_address", $this->ip_address);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur lors de la journalisation: " . $e->getMessage());
            return false;
        }
    }
}
?>
