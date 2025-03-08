<?php
// Vérification du statut d'authentification
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Inclure le fichier de vérification d'authentification
require_once "../../includes/auth_check.php";

// Vérification de l'authentification
$authenticated = is_authenticated();

if($authenticated) {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "authenticated" => true,
        "user" => [
            "id" => $_SESSION['user_id'],
            "email" => $_SESSION['email'],
            "first_name" => $_SESSION['first_name'],
            "last_name" => $_SESSION['last_name'],
            "user_type" => $_SESSION['user_type']
        ]
    ]);
} else {
    http_response_code(200); // 200 OK but not authenticated
    echo json_encode([
        "status" => "success",
        "authenticated" => false
    ]);
}
?>
