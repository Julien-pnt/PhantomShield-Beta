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
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validation des données
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email et mot de passe sont obligatoires']);
            exit;
        }
        
        // Rechercher l'utilisateur dans la base de données
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier si l'utilisateur existe et si le mot de passe correspond
        if (!$user || !password_verify($password, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
            exit;
        }
        
        // Vérifier si le compte est actif
        if ($user['account_status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Votre compte n\'est pas actif. Veuillez vérifier votre email ou contacter le support.']);
            exit;
        }
        
        // Mettre à jour la date de dernière connexion
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Enregistrer les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        
        // Si l'option "Se souvenir de moi" est cochée, créer un cookie
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + 60*60*24*30; // 30 jours
            
            // Stocker le token dans la base de données
            // Vous devrez ajouter une table ou une colonne pour stocker ces tokens
            // Pour l'exemple, nous allons simplement le mettre dans un cookie
            
            setcookie('remember_token', $token, $expires, '/', '', false, true);
            setcookie('user_id', $user['id'], $expires, '/', '', false, true);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Connexion réussie! Redirection...'
        ]);
        
    } catch (PDOException $e) {
        // En cas d'erreur avec la base de données
        error_log('Erreur de base de données: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Une erreur s\'est produite lors de la connexion']);
    }
} else {
    // Si ce n'est pas une requête POST
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>
