<?php
// Configuration globale de l'application - Version XAMPP localhost pour développement

// Mode de développement
define('DEBUG_MODE', true);

// Paramètres de la base de données pour XAMPP (valeurs par défaut)
define('DB_HOST', 'localhost');
define('DB_NAME', 'phantomshield_db');
define('DB_USER', 'root');  // Utilisateur par défaut de XAMPP
define('DB_PASS', '');      // Mot de passe vide par défaut pour XAMPP

// Paramètres de l'application
define('APP_NAME', 'PhantomShield');
define('APP_URL', 'http://localhost/phantomshield'); // URL locale
define('APP_VERSION', '1.0.0');

// Paramètres des emails (pour test local)
define('EMAIL_FROM', 'noreply@phantomshield.com');
define('EMAIL_NAME', 'PhantomShield Security');
define('EMAIL_SUPPORT', 'support@phantomshield.com');

// Paramètres de sécurité
define('SESSION_LIFETIME', 86400); // 24 heures
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
define('PASSWORD_RESET_EXPIRY', 3600); // 1 heure

// Chemins de l'application - adaptés pour XAMPP
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDE_PATH', ROOT_PATH . '/includes');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH); // Dans XAMPP, souvent le dossier public est à la racine
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('REPORT_PATH', ROOT_PATH . '/reports');

// En mode développement, on affiche toutes les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Journalisation des erreurs
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/error.log');

// S'assurer que le dossier logs existe
if (!is_dir(ROOT_PATH . '/logs')) {
    mkdir(ROOT_PATH . '/logs', 0755, true);
}

// Fuseaux horaires
date_default_timezone_set('Europe/Paris');

// Clé secrète pour les tokens - pour développement (remplacer en production)
define('TOKEN_SECRET', 'dev_token_secret_key_phantomshield');

// Configuration des fichiers autorisés pour les uploads
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 MB

// Initialisation de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}
?>
