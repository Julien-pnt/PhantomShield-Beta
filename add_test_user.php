<?php
require_once 'config/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Hash du mot de passe pour le test
    $password = password_hash("MotDePasse123!", PASSWORD_DEFAULT);
    
    // Génération d'un token de vérification
    $verificationToken = bin2hex(random_bytes(32));
    
    // Préparation de la requête avec les noms de colonnes corrects
    $stmt = $pdo->prepare("
        INSERT INTO users 
        (first_name, last_name, email, password_hash, phone, verification_token, verified, account_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    // Exécution de la requête
    $stmt->execute([
        "Utilisateur", 
        "Test", 
        "test@example.com", 
        $password,
        "0612345678", 
        $verificationToken,
        1, // verified = 1 (vrai)
        "active" // account_status
    ]);
    
    $userId = $pdo->lastInsertId();
    
    echo "<h2>✅ Utilisateur de test créé avec succès!</h2>";
    echo "<p>ID: " . $userId . "</p>";
    echo "<p>Email: test@example.com</p>";
    echo "<p>Mot de passe: MotDePasse123!</p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Erreur lors de la création de l'utilisateur test:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Si l'erreur est une clé dupliquée (utilisateur existe déjà)
    if ($e->getCode() == 23000) {
        echo "<p>L'adresse email 'test@example.com' est déjà utilisée. L'utilisateur de test existe probablement déjà.</p>";
        
        // Option pour réinitialiser le mot de passe si l'utilisateur existe déjà
        echo "<h3>Voulez-vous réinitialiser le mot de passe de cet utilisateur?</h3>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='reset_password' value='1'>";
        echo "<button type='submit'>Réinitialiser le mot de passe</button>";
        echo "</form>";
    }
}

// Si une demande de réinitialisation est reçue
if (isset($_POST['reset_password'])) {
    try {
        $password = password_hash("MotDePasse123!", PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            UPDATE users 
            SET password_hash = ?, 
                account_status = 'active',
                verified = 1
            WHERE email = 'test@example.com'
        ");
        
        $stmt->execute([$password]);
        
        echo "<h2>✅ Mot de passe réinitialisé avec succès!</h2>";
        echo "<p>Email: test@example.com</p>";
        echo "<p>Nouveau mot de passe: MotDePasse123!</p>";
        
    } catch (PDOException $e) {
        echo "<h2>❌ Erreur lors de la réinitialisation du mot de passe:</h2>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
