<?php
// Vérification d'authentification pour inclure dans les pages protégées
session_start();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/Session.php";
require_once __DIR__ . "/User.php";

function is_authenticated() {
    // Vérification du cookie de session
    if(isset($_COOKIE['session_id'])) {
        $session_id = $_COOKIE['session_id'];
        
        // Connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();
        
        // Validation de la session
        $session = new Session($db);
        $user_id = $session->validate($session_id);
        
        if($user_id) {
            // Récupération des informations utilisateur
            $user = new User($db);
            if($user->getUserById($user_id)) {
                // Stockage des informations utilisateur en session
                $_SESSION['user_id'] = $user->id;
                $_SESSION['email'] = $user->email;
                $_SESSION['first_name'] = $user->first_name;
                $_SESSION['last_name'] = $user->last_name;
                $_SESSION['user_type'] = $user->user_type;
                
                return true;
            }
        }
    }
    
    // Nettoyage de la session et des cookies si l'authentification échoue
    session_unset();
    session_destroy();
    setcookie('session_id', '', time() - 3600, '/');
    
    return false;
}

// Redirection si non authentifié
function require_authentication($redirect_url = "/html/register-login.html") {
    if(!is_authenticated()) {
        header("Location: " . $redirect_url);
        exit;
    }
}
?>
