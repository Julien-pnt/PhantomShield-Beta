<?php
require_once 'config/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<h2>Vérification de l'installation</h2>";
    
    // 1. Vérifier que la base de données est accessible
    echo "✅ <strong>Connexion à la base de données:</strong> Réussie<br>";
    
    // 2. Vérifier que la table users existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ <strong>Table 'users':</strong> Existe<br>";
        
        // 3. Vérifier l'utilisateur de test
        $stmt = $pdo->prepare("SELECT id, email, verified, account_status FROM users WHERE email = 'test@example.com'");
        $stmt->execute();
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "✅ <strong>Utilisateur de test:</strong> Existe (ID: {$user['id']})<br>";
            echo "- <strong>Email:</strong> {$user['email']}<br>";
            echo "- <strong>Vérifié:</strong> " . ($user['verified'] ? 'Oui' : 'Non') . "<br>";
            echo "- <strong>Statut:</strong> {$user['account_status']}<br>";
            
            // 4. Vérifier si le mot de passe fonctionne
            echo "<br><strong>Tester la connexion:</strong><br>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='test_login' value='1'>";
            echo "<button type='submit'>Essayer de se connecter comme utilisateur de test</button>";
            echo "</form>";
        } else {
            echo "❌ <strong>Utilisateur de test:</strong> Non trouvé. Exécutez d'abord le script add_test_user.php<br>";
        }
    } else {
        echo "❌ <strong>Table 'users':</strong> Non trouvée<br>";
    }
    
    // Tester la connexion
    if (isset($_POST['test_login'])) {
        $stmt = $pdo->prepare("SELECT id, email, password_hash FROM users WHERE email = 'test@example.com'");
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify('MotDePasse123!', $user['password_hash'])) {
            echo "<p>✅ <strong>Test de connexion:</strong> Réussi! L'authentification fonctionne.</p>";
        } else {
            echo "<p>❌ <strong>Test de connexion:</strong> Échec. Le mot de passe ne correspond pas.</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Erreur:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
