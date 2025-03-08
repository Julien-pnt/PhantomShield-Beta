<?php
class User {
    private $conn;
    private $table_name = "users";
    
    // Propriétés de l'utilisateur
    public $id;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $phone;
    public $user_type;
    public $verification_token;
    public $verified;
    public $created_at;
    public $account_status;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Création d'un nouvel utilisateur
    public function create() {
        try {
            // Hash du mot de passe avant stockage
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            
            // Génération d'un token de vérification
            $this->verification_token = bin2hex(random_bytes(32));
            
            $query = "INSERT INTO " . $this->table_name . "
                    (email, password_hash, first_name, last_name, phone, user_type, verification_token)
                    VALUES
                    (:email, :password_hash, :first_name, :last_name, :phone, :user_type, :verification_token)";
            
            $stmt = $this->conn->prepare($query);
            
            // Sécurisation des données
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->first_name = htmlspecialchars(strip_tags($this->first_name));
            $this->last_name = htmlspecialchars(strip_tags($this->last_name));
            $this->phone = htmlspecialchars(strip_tags($this->phone));
            $this->user_type = htmlspecialchars(strip_tags($this->user_type));
            
            // Liaison des paramètres
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password_hash", $password_hash);
            $stmt->bindParam(":first_name", $this->first_name);
            $stmt->bindParam(":last_name", $this->last_name);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":user_type", $this->user_type);
            $stmt->bindParam(":verification_token", $this->verification_token);
            
            // Exécution de la requête
            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    // Vérification des identifiants de connexion
    public function login($email, $password) {
        try {
            $query = "SELECT id, email, password_hash, verified, account_status FROM " 
                    . $this->table_name . " WHERE email = :email LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($row['account_status'] != 'active') {
                    return ["status" => "error", "message" => "Compte désactivé ou suspendu."];
                }
                
                if($row['verified'] != 1) {
                    return ["status" => "error", "message" => "Compte non vérifié. Veuillez vérifier votre e-mail."];
                }
                
                if(password_verify($password, $row['password_hash'])) {
                    // Mise à jour de la dernière connexion
                    $this->updateLastLogin($row['id']);
                    
                    // Journalisation de la connexion réussie
                    $this->logLoginAttempt($email, true);
                    
                    return [
                        "status" => "success",
                        "user_id" => $row['id'],
                        "email" => $row['email']
                    ];
                } else {
                    // Journalisation de la tentative de connexion échouée
                    $this->logLoginAttempt($email, false);
                    
                    return ["status" => "error", "message" => "Mot de passe incorrect."];
                }
            } else {
                // Journalisation de la tentative avec un e-mail inexistant
                $this->logLoginAttempt($email, false);
                
                return ["status" => "error", "message" => "Utilisateur non trouvé."];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la connexion: " . $e->getMessage());
            return ["status" => "error", "message" => "Une erreur est survenue lors de la connexion."];
        }
    }
    
    // Mise à jour de la dernière connexion
    private function updateLastLogin($user_id) {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
    }
    
    // Journalisation des tentatives de connexion
    private function logLoginAttempt($email, $success) {
        $query = "INSERT INTO login_attempts (email, ip_address, success) 
                 VALUES (:email, :ip, :success)";
        $stmt = $this->conn->prepare($query);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":ip", $ip);
        $stmt->bindParam(":success", $success, PDO::PARAM_BOOL);
        $stmt->execute();
    }
    
    // Vérifier si l'adresse email existe déjà
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Obtenir les informations d'un utilisateur
    public function getUserById($id) {
        $query = "SELECT id, email, first_name, last_name, phone, user_type, verified, 
                 created_at, account_status FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->phone = $row['phone'];
            $this->user_type = $row['user_type'];
            $this->verified = $row['verified'];
            $this->created_at = $row['created_at'];
            $this->account_status = $row['account_status'];
            
            return true;
        }
        
        return false;
    }
    
    // Vérifier un compte
    public function verifyAccount($token) {
        $query = "SELECT id FROM " . $this->table_name . " 
                 WHERE verification_token = :token AND verified = 0 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Mettre à jour le statut de vérification
            $update_query = "UPDATE " . $this->table_name . " 
                           SET verified = 1, verification_token = NULL 
                           WHERE id = :id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":id", $row['id']);
            
            if($update_stmt->execute()) {
                return true;
            }
        }
        
        return false;
    }
    
    // Demande de réinitialisation de mot de passe
    public function requestPasswordReset($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Génération d'un token de réinitialisation
            $reset_token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Mise à jour du token de réinitialisation
            $update_query = "UPDATE " . $this->table_name . " 
                           SET reset_token = :token, reset_token_expires = :expires 
                           WHERE id = :id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":token", $reset_token);
            $update_stmt->bindParam(":expires", $expires);
            $update_stmt->bindParam(":id", $row['id']);
            
            if($update_stmt->execute()) {
                return $reset_token;
            }
        }
        
        return false;
    }
    
    // Réinitialisation du mot de passe
    public function resetPassword($token, $new_password) {
        $now = date('Y-m-d H:i:s');
        
        $query = "SELECT id FROM " . $this->table_name . " 
                 WHERE reset_token = :token AND reset_token_expires > :now LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":now", $now);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Hash du nouveau mot de passe
            $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            
            // Mise à jour du mot de passe
            $update_query = "UPDATE " . $this->table_name . " 
                           SET password_hash = :password_hash, reset_token = NULL, reset_token_expires = NULL 
                           WHERE id = :id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":password_hash", $password_hash);
            $update_stmt->bindParam(":id", $row['id']);
            
            if($update_stmt->execute()) {
                return true;
            }
        }
        
        return false;
    }
}
?>
