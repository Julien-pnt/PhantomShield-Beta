<?php
require_once 'config/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "<h2>✅ Connexion à la base de données réussie!</h2>";
    
    // Vérifier les tables créées
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tables trouvées dans la base de données " . DB_NAME . ":</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table) . "</li>";
    }
    echo "</ul>";

} catch (PDOException $e) {
    echo "<h2>❌ Erreur de connexion à la base de données:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        echo "<p>La base de données '" . DB_NAME . "' n'existe pas. Vous devez la créer.</p>";
        echo "<p>Commande SQL pour créer la base de données: <code>CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</code></p>";
    }
}

// Vérifier les chemins
echo "<h3>Vérification des chemins:</h3>";
echo "<ul>";
echo "<li>ROOT_PATH: " . ROOT_PATH . " - " . (is_dir(ROOT_PATH) ? "✅" : "❌") . "</li>";
echo "<li>INCLUDE_PATH: " . INCLUDE_PATH . " - " . (is_dir(INCLUDE_PATH) ? "✅" : "❌") . "</li>";
echo "<li>CONFIG_PATH: " . CONFIG_PATH . " - " . (is_dir(CONFIG_PATH) ? "✅" : "❌") . "</li>";
echo "<li>PUBLIC_PATH: " . PUBLIC_PATH . " - " . (is_dir(PUBLIC_PATH) ? "✅" : "❌") . "</li>";
echo "<li>UPLOAD_PATH: " . UPLOAD_PATH . " - " . (is_dir(UPLOAD_PATH) ? "✅" : "❌") . "</li>";
echo "<li>REPORT_PATH: " . REPORT_PATH . " - " . (is_dir(REPORT_PATH) ? "✅" : "❌") . "</li>";
echo "</ul>";

// Informations sur PHP et l'environnement
echo "<h3>Informations sur l'environnement:</h3>";
echo "<ul>";
echo "<li>Version PHP: " . phpversion() . "</li>";
echo "<li>Extensions PDO: " . (extension_loaded('pdo_mysql') ? "✅ PDO MySQL installée" : "❌ PDO MySQL manquante!") . "</li>";
echo "<li>Sessions activées: " . (session_status() === PHP_SESSION_ACTIVE ? "✅" : "❌") . "</li>";
echo "</ul>";
?>
