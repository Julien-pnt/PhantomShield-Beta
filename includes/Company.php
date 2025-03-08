<?php
class Company {
    private $conn;
    private $table_name = "companies";
    
    // Propriétés de l'entreprise
    public $id;
    public $user_id;
    public $company_name;
    public $siret;
    public $sector;
    public $company_size;
    public $address;
    public $contact_name;
    public $contact_position;
    public $website;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Création d'une nouvelle entreprise
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . "
                    (user_id, company_name, siret, sector, contact_name)
                    VALUES
                    (:user_id, :company_name, :siret, :sector, :contact_name)";
            
            $stmt = $this->conn->prepare($query);
            
            // Sécurisation des données
            $this->company_name = htmlspecialchars(strip_tags($this->company_name));
            $this->siret = htmlspecialchars(strip_tags($this->siret));
            $this->sector = htmlspecialchars(strip_tags($this->sector));
            $this->contact_name = htmlspecialchars(strip_tags($this->contact_name));
            
            // Liaison des paramètres
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":company_name", $this->company_name);
            $stmt->bindParam(":siret", $this->siret);
            $stmt->bindParam(":sector", $this->sector);
            $stmt->bindParam(":contact_name", $this->contact_name);
            
            // Exécution de la requête
            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la création de l'entreprise: " . $e->getMessage());
            return false;
        }
    }
    
    // Vérification si un SIRET existe déjà
    public function siretExists($siret) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE siret = :siret LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":siret", $siret);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Obtention des informations d'une entreprise par l'ID utilisateur
    public function getCompanyByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->company_name = $row['company_name'];
            $this->siret = $row['siret'];
            $this->sector = $row['sector'];
            $this->company_size = $row['company_size'];
            $this->address = $row['address'];
            $this->contact_name = $row['contact_name'];
            $this->contact_position = $row['contact_position'];
            $this->website = $row['website'];
            
            return true;
        }
        
        return false;
    }
}
?>
