<?php
// Page du tableau de bord (requiert authentification)
require_once "../includes/auth_check.php";
require_authentication();

// Connexion à la base de données
require_once "../config/db.php";
$database = new Database();
$db = $database->getConnection();

// Obtention des informations utilisateur
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhantomShield - Tableau de bord</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="../img/logo-casque.png" type="image/x-icon">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="dashboard-sidebar">
            <div class="sidebar-logo">
                <img src="../img/logo-casque.png" alt="PhantomShield Logo">
                <h2>PhantomShield</h2>
            </div>
            <div class="sidebar-user">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-info">
                    <h3><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h3>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="active">
                    <a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                </li>
                <li>
                    <a href="services.php"><i class="fas fa-shield-alt"></i> Mes services</a>
                </li>
                <li>
                    <a href="reports.php"><i class="fas fa-file-alt"></i> Rapports</a>
                </li>
                <?php if ($user_type === 'professionnel'): ?>
                <li>
                    <a href="company.php"><i class="fas fa-building"></i> Mon entreprise</a>
                </li>
                <?php endif; ?>
                <li>
                    <a href="profile.php"><i class="fas fa-user-cog"></i> Mon profil</a>
                </li>
                <li>
                    <a href="support.php"><i class="fas fa-headset"></i> Support</a>
                </li>
                <li class="sidebar-logout">
                    <a href="#" id="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="dashboard-content">
            <div class="dashboard-header">
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="header-actions">
                    <a href="#" class="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </a>
                    <a href="#" class="messages">
                        <i class="fas fa-envelope"></i>
                        <span class="message-badge">5</span>
                    </a>
                </div>
            </div>
            
            <!-- Dashboard Overview -->
            <div class="dashboard-overview">
                <h1>Bienvenue dans votre espace PhantomShield, <?php echo htmlspecialchars($first_name); ?></h1>
                <p>Consultez les informations sur vos services et les résultats de sécurité.</p>
                
                <div class="overview-cards">
                    <!-- Services actifs -->
                    <div class="overview-card">
                        <div class="card-icon purple-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="card-info">
                            <h3>Services actifs</h3>
                            <p class="card-value">2</p>
                        </div>
                    </div>
                    
                    <!-- Vulnérabilités -->
                    <div class="overview-card">
                        <div class="card-icon red-icon">
                            <i class="fas fa-bug"></i>
                        </div>
                        <div class="card-info">
                            <h3>Vulnérabilités</h3>
                            <p class="card-value">8</p>
                            <p class="card-sublabel">3 critiques</p>
                        </div>
                    </div>
                    
                    <!-- Rapports -->
                    <div class="overview-card">
                        <div class="card-icon blue-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="card-info">
                            <h3>Rapports récents</h3>
                            <p class="card-value">4</p>
                        </div>
                    </div>
                    
                    <!-- Score de sécurité -->
                    <div class="overview-card">
                        <div class="card-icon green-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="card-info">
                            <h3>Score de sécurité</h3>
                            <p class="card-value">75<span class="percentage">%</span></p>
                            <div class="security-score-bar">
                                <div class="score-fill" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="recent-activity">
                    <div class="section-header">
                        <h2>Activité récente</h2>
                        <a href="#" class="view-all">Voir tout</a>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon blue-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="activity-details">
                                <h4>Nouveau rapport disponible</h4>
                                <p>Rapport de test d'intrusion - Avril 2023</p>
                                <span class="activity-time">Il y a 2 jours</span>
                            </div>
                            <div class="activity-action">
                                <a href="#" class="btn-outline">Voir</a>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon red-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="activity-details">
                                <h4>Alerte de sécurité</h4>
                                <p>Vulnérabilité critique détectée sur votre serveur web</p>
                                <span class="activity-time">Il y a 4 jours</span>
                            </div>
                            <div class="activity-action">
                                <a href="#" class="btn-outline">Détails</a>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon purple-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="activity-details">
                                <h4>Service activé</h4>
                                <p>Monitoring de sécurité 24/7 activé avec succès</p>
                                <span class="activity-time">Il y a 1 semaine</span>
                            </div>
                            <div class="activity-action">
                                <a href="#" class="btn-outline">Gérer</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../js/auth.js"></script>
    <script>
        // Toggle du menu latéral
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.dashboard-container').classList.toggle('sidebar-collapsed');
        });
        
        // Gestion de la déconnexion
        document.getElementById('logout-btn').addEventListener('click', async function(e) {
            e.preventDefault();
            
            const logoutResult = await AuthManager.logout();
            
            if (logoutResult.success) {
                window.location.href = '/index.html';
            }
        });
    </script>
</body>
</html>
