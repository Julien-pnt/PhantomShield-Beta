<?php
// Page de profil utilisateur (requiert authentification)
require_once "../includes/auth_check.php";
require_authentication();

// Connexion à la base de données
require_once "../config/db.php";
require_once "../includes/User.php";
require_once "../includes/Company.php";

$database = new Database();
$db = $database->getConnection();

// Obtention des informations utilisateur
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Initialisation de l'objet utilisateur
$user = new User($db);
$user->getUserById($user_id);

// Si professionnel, obtenir les informations d'entreprise
$company = null;
if ($user_type === 'professionnel') {
    $company = new Company($db);
    $company->getCompanyByUserId($user_id);
}

// Traitement de la mise à jour du profil
$update_message = '';
$update_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // TODO: Implémentation de la mise à jour du profil
    $update_message = 'Profil mis à jour avec succès.';
    $update_status = 'success';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhantomShield - Mon Profil</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/auth-styles.css">
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
                    <h3><?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?></h3>
                    <p><?php echo htmlspecialchars($user->email); ?></p>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li>
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
                <li class="active">
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
            
            <!-- Profile Content -->
            <div class="dashboard-overview">
                <h1>Mon Profil</h1>
                <p>Gérez vos informations personnelles et vos paramètres de sécurité.</p>
                
                <?php if ($update_message): ?>
                <div class="message-container">
                    <div class="message <?php echo $update_status; ?>"><?php echo $update_message; ?></div>
                </div>
                <?php endif; ?>
                
                <div class="profile-container">
                    <div class="profile-sidebar">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle"></i>
                            <button class="change-avatar-btn">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h3><?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?></h3>
                        <p><?php echo htmlspecialchars($user->email); ?></p>
                        <span class="user-type"><?php echo $user->user_type === 'professionnel' ? 'Professionnel' : 'Particulier'; ?></span>
                        
                        <ul class="profile-menu">
                            <li class="active" data-target="personal">Informations personnelles</li>
                            <li data-target="security">Sécurité</li>
                            <?php if ($user_type === 'professionnel'): ?>
                            <li data-target="company">Informations entreprise</li>
                            <?php endif; ?>
                            <li data-target="preferences">Préférences</li>
                        </ul>
                    </div>
                    
                    <div class="profile-content">
                        <!-- Informations personnelles -->
                        <div class="profile-section active" id="personal-section">
                            <h2>Informations personnelles</h2>
                            <form method="post" class="profile-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="first_name">Prénom</label>
                                        <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user->first_name); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Nom</label>
                                        <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user->last_name); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user->email); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user->phone); ?>">
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn-primary">Mettre à jour</button>
                            </form>
                        </div>
                        
                        <!-- Sécurité -->
                        <div class="profile-section" id="security-section">
                            <h2>Sécurité</h2>
                            <form method="post" class="profile-form" id="password-form">
                                <div class="form-group">
                                    <label for="current_password">Mot de passe actuel</label>
                                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password">Nouveau mot de passe</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                                    <div class="password-strength">
                                        <div class="strength-bar">
                                            <div class="strength-fill" style="width: 0"></div>
                                        </div>
                                        <span class="strength-text">Force: Faible</span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                </div>
                                
                                <button type="submit" name="update_password" class="btn-primary">Changer le mot de passe</button>
                            </form>
                            
                            <div class="security-options">
                                <h3>Options de sécurité supplémentaires</h3>
                                
                                <div class="security-option">
                                    <div class="option-info">
                                        <h4>Authentification à deux facteurs</h4>
                                        <p>Ajoutez une couche de sécurité supplémentaire à votre compte.</p>
                                    </div>
                                    <div class="option-action">
                                        <label class="switch">
                                            <input type="checkbox" id="2fa-toggle">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="security-option">
                                    <div class="option-info">
                                        <h4>Sessions actives</h4>
                                        <p>Gérez vos sessions de connexion actives.</p>
                                    </div>
                                    <div class="option-action">
                                        <button class="btn-outline">Voir les sessions</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($user_type === 'professionnel' && $company): ?>
                        <!-- Informations entreprise -->
                        <div class="profile-section" id="company-section">
                            <h2>Informations entreprise</h2>
                            <form method="post" class="profile-form">
                                <div class="form-group">
                                    <label for="company_name">Nom de l'entreprise</label>
                                    <input type="text" id="company_name" name="company_name" class="form-control" value="<?php echo htmlspecialchars($company->company_name); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="siret">SIRET</label>
                                    <input type="text" id="siret" name="siret" class="form-control" value="<?php echo htmlspecialchars($company->siret); ?>" readonly>
                                    <small>Le numéro SIRET ne peut pas être modifié. Contactez le support si nécessaire.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="sector">Secteur d'activité</label>
                                    <select id="sector" name="sector" class="form-control">
                                        <option value="finance" <?php echo $company->sector === 'finance' ? 'selected' : ''; ?>>Finance & Banque</option>
                                        <option value="sante" <?php echo $company->sector === 'sante' ? 'selected' : ''; ?>>Santé</option>
                                        <option value="industrie" <?php echo $company->sector === 'industrie' ? 'selected' : ''; ?>>Industrie</option>
                                        <option value="commerce" <?php echo $company->sector === 'commerce' ? 'selected' : ''; ?>>Commerce</option>
                                        <option value="tech" <?php echo $company->sector === 'tech' ? 'selected' : ''; ?>>Technologie</option>
                                        <option value="autre" <?php echo $company->sector === 'autre' ? 'selected' : ''; ?>>Autre</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contact_name">Nom du contact principal</label>
                                    <input type="text" id="contact_name" name="contact_name" class="form-control" value="<?php echo htmlspecialchars($company->contact_name); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="website">Site web</label>
                                    <input type="url" id="website" name="website" class="form-control" value="<?php echo htmlspecialchars($company->website); ?>">
                                </div>
                                
                                <button type="submit" name="update_company" class="btn-primary">Mettre à jour</button>
                            </form>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Préférences -->
                        <div class="profile-section" id="preferences-section">
                            <h2>Préférences</h2>
                            <form method="post" class="profile-form">
                                <div class="form-group">
                                    <h3>Notifications</h3>
                                    
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="email_notif" name="preferences[email_notif]" checked>
                                        <label for="email_notif">Recevoir les notifications par email</label>
                                    </div>
                                    
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="security_alerts" name="preferences[security_alerts]" checked>
                                        <label for="security_alerts">Alertes de sécurité</label>
                                    </div>
                                    
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="report_notif" name="preferences[report_notif]" checked>
                                        <label for="report_notif">Nouveaux rapports</label>
                                    </div>
                                    
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="marketing" name="preferences[marketing]">
                                        <label for="marketing">Communications marketing et nouveautés</label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <h3>Langue et région</h3>
                                    <label for="language">Langue</label>
                                    <select id="language" name="preferences[language]" class="form-control">
                                        <option value="fr" selected>Français</option>
                                        <option value="en">English</option>
                                    </select>
                                </div>
                                
                                <button type="submit" name="update_preferences" class="btn-primary">Enregistrer les préférences</button>
                            </form>
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
        
        // Navigation dans le profil
        const profileMenuItems = document.querySelectorAll('.profile-menu li');
        const profileSections = document.querySelectorAll('.profile-section');
        
        profileMenuItems.forEach(item => {
            item.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                
                // Désactivation de tous les onglets et sections
                profileMenuItems.forEach(i => i.classList.remove('active'));
                profileSections.forEach(s => s.classList.remove('active'));
                
                // Activation de l'onglet et de la section sélectionnés
                this.classList.add('active');
                document.getElementById(target + '-section').classList.add('active');
            });
        });
        
        // Force du mot de passe
        const passwordInput = document.getElementById('new_password');
        const strengthFill = document.querySelector('.strength-fill');
        const strengthText = document.querySelector('.strength-text');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let status = '';
            
            // Calcul de la force du mot de passe
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            
            // Mise à jour de l'affichage
            strengthFill.style.width = strength + '%';
            
            if (strength <= 25) {
                status = 'Faible';
                strengthFill.style.backgroundColor = '#e71d36';
            } else if (strength <= 50) {
                status = 'Moyen';
                strengthFill.style.backgroundColor = '#ffb627';
            } else if (strength <= 75) {
                status = 'Bon';
                strengthFill.style.backgroundColor = '#20c997';
            } else {
                status = 'Fort';
                strengthFill.style.backgroundColor = '#28a745';
            }
            
            strengthText.textContent = 'Force: ' + status;
        });
    </script>
</body>
</html>
