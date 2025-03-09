// Affichage des messages
function showMessage(message, type = 'info') {
    const messageContainer = document.getElementById('messageContainer');
    const messageElement = document.createElement('div');
    messageElement.className = `message ${type}`;
    messageElement.textContent = message;
    
    messageContainer.innerHTML = '';
    messageContainer.appendChild(messageElement);
    
    // Auto-disparition après 5 secondes pour les messages de succès
    if (type === 'success') {
        setTimeout(() => {
            messageElement.classList.add('fade-out');
            setTimeout(() => messageContainer.innerHTML = '', 500);
        }, 5000);
    }
}

// Évaluation de la force du mot de passe
function evaluatePasswordStrength(password) {
    let strength = 0;
    
    // Longueur minimum
    if (password.length >= 8) strength += 25;
    
    // Présence de chiffres
    if (/\d/.test(password)) strength += 25;
    
    // Présence de lettres minuscules et majuscules
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
    
    // Présence de caractères spéciaux
    if (/[^a-zA-Z0-9]/.test(password)) strength += 25;
    
    return strength;
}

// Mise à jour de l'indicateur de force du mot de passe
function updatePasswordStrength(passwordInput) {
    const strength = evaluatePasswordStrength(passwordInput.value);
    const parent = passwordInput.parentElement;
    const strengthBar = parent.querySelector('.strength-fill');
    const strengthText = parent.querySelector('.strength-text');
    
    if (strengthBar && strengthText) {
        strengthBar.style.width = `${strength}%`;
        
        if (strength < 25) {
            strengthBar.style.backgroundColor = '#FF4B47';
            strengthText.textContent = 'Faible';
            strengthText.style.color = '#FF4B47';
        } else if (strength < 50) {
            strengthBar.style.backgroundColor = '#FFA500';
            strengthText.textContent = 'Moyen';
            strengthText.style.color = '#FFA500';
        } else if (strength < 75) {
            strengthBar.style.backgroundColor = '#FFDD00';
            strengthText.textContent = 'Bon';
            strengthText.style.color = '#FFDD00';
        } else {
            strengthBar.style.backgroundColor = '#00B894';
            strengthText.textContent = 'Fort';
            strengthText.style.color = '#00B894';
        }
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    // Sélections des éléments du DOM
    const formTabs = document.querySelectorAll('.form-tab');
    const formContents = document.querySelectorAll('.form-content');
    const accountOptions = document.querySelectorAll('.account-option');
    const accountForms = document.querySelectorAll('.account-form');
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    // Gestion des onglets (Connexion / Inscription)
    formTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            formTabs.forEach(t => t.classList.remove('active'));
            formContents.forEach(c => c.classList.remove('active'));
            
            this.classList.add('active');
            const targetTab = this.getAttribute('data-tab');
            
            if(targetTab === 'login') {
                document.getElementById('login-form').classList.add('active');
            } else if(targetTab === 'register') {
                document.getElementById('register-container').classList.add('active');
            }
        });
    });
    
    // Gestion des options d'inscription (Particulier / Professionnel)
    accountOptions.forEach(option => {
        option.addEventListener('click', function() {
            accountOptions.forEach(o => o.classList.remove('active'));
            accountForms.forEach(f => f.classList.remove('active'));
            
            this.classList.add('active');
            const targetForm = this.getAttribute('data-form');
            document.getElementById(targetForm).classList.add('active');
        });
    });
    
    // Affichage/masquage des mots de passe
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            
            if(passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });
    
    // Vérification de la force du mot de passe
    const passwordInputs = document.querySelectorAll('input[name="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            updatePasswordStrength(this);
        });
    });
    
    // Gestion du formulaire de connexion
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collecte des données du formulaire
            const formData = new FormData(loginForm);
            
            // Envoyer les données au serveur
            fetch('../public/api/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || '../dashboard/index.php';
                    }, 1500);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Une erreur est survenue. Veuillez réessayer plus tard.', 'error');
                console.error('Erreur:', error);
            });
        });
    }
    
    // Validation de formulaire pour l'inscription particulier
    const particulierForm = document.getElementById('particulier-form');
    if (particulierForm) {
        particulierForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Valider les mots de passe
            const password = document.getElementById('part-password').value;
            const confirmPassword = document.getElementById('part-confirm').value;
            
            if (password.length < 8) {
                showMessage('Le mot de passe doit contenir au moins 8 caractères', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Les mots de passe ne correspondent pas', 'error');
                return;
            }
            
            // Collecte des données du formulaire
            const formData = new FormData(particulierForm);
            
            // Envoyer les données au serveur
            fetch('../public/api/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || 'login-success.html';
                    }, 2000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Une erreur est survenue. Veuillez réessayer plus tard.', 'error');
                console.error('Erreur:', error);
            });
        });
    }
    
    // Validation de formulaire pour l'inscription professionnelle
    const proForm = document.getElementById('professionnel-form');
    if (proForm) {
        proForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Valider les mots de passe
            const password = document.getElementById('pro-password').value;
            const confirmPassword = document.getElementById('pro-confirm').value;
            
            if (password.length < 8) {
                showMessage('Le mot de passe doit contenir au moins 8 caractères', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Les mots de passe ne correspondent pas', 'error');
                return;
            }
            
            // Valider le SIRET
            const siret = document.getElementById('pro-siret').value;
            if (!/^\d{14}$/.test(siret)) {
                showMessage('Veuillez saisir un numéro SIRET valide (14 chiffres)', 'error');
                return;
            }
            
            // Collecte des données du formulaire
            const formData = new FormData(proForm);
            
            // Envoyer les données au serveur
            fetch('../public/api/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || 'login-success.html';
                    }, 2000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Une erreur est survenue. Veuillez réessayer plus tard.', 'error');
                console.error('Erreur:', error);
            });
        });
    }
});
