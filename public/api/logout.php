<?php
// Traitement des requêtes de déconnexion
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification de la méthode de requête
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit;
}

// Inclure les fichiers nécessaires
require_once "../../config/db.php";
require_once "../../includes/Session.php";
require_once "../../includes/AuditLog.php";

// Démarrage de la session
session_start();

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Initialisation des objets
$session = new Session($db);
$audit = new AuditLog($db);

// Récupération de l'ID de session depuis le cookie
$session_id = $_COOKIE['session_id'] ?? null;

if($session_id) {
    // Destruction de la session
    $session->destroy($session_id);
    
    // Journalisation de la déconnexion
    if(isset($_SESSION['user_id'])) {
        $audit->log($_SESSION['user_id'], "logout", "Déconnexion de l'utilisateur");
    }
}

// Destruction de la session PHP
session_unset();
session_destroy();

// Réponse
http_response_code(200);
echo json_encode([
    "status" => "success",
    "message" => "Déconnexion réussie"
]);
?>
