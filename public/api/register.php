<?php
// Initialiser la session si ce n'est pas déjà fait
session_start();

// Inclure la configuration de la base de données
require_once '../config/config.php';

// Définir le type de contenu pour la réponse en JSON
header('Content-Type: application/json');

// Traiter la soumission de formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se connecter à la base de données
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Récupérer les données du formulaire
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $phone = $_POST['phone'] ?? null;
        $user_type = $_POST['user_type'] ?? 'particulier';
        
        // Validation des données
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email et mot de passe sont obligatoires']);
            exit;
        }
        
        // Vérifier si l'email est déjà utilisé
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            exit;
        }
        
        // Hachage du mot de passe
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Génération du token de vérification
        $verification_token = bin2hex(random_bytes(32));
        
        // Insérer l'utilisateur dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO users 
            (first_name, last_name, email, password_hash, phone, user_type, verification_token, account_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $first_name,
            $last_name,
            $email,
            $password_hash,
            $phone,
            $user_type,
            $verification_token,
            'active'  // Définir comme actif par défaut
        ]);
        
        // Si nous sommes ici, l'insertion a réussi
        $userId = $pdo->lastInsertId();
        
        // TODO: Envoyer un email de vérification avec le token
        
        echo json_encode([
            'success' => true, 
            'message' => 'Compte créé avec succès! Veuillez consulter votre email pour activer votre compte.'
        ]);
        
    } catch (PDOException $e) {
        // En cas d'erreur avec la base de données
        error_log('Erreur de base de données: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Une erreur s\'est produite lors de l\'inscription']);
    }
} else {
    // Si ce n'est pas une requête POST
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>
