document.addEventListener('DOMContentLoaded', function() {
    // Toggle entre les formulaires de connexion et d'inscription
    const formTabs = document.querySelectorAll('.form-tabs .form-tab');
    formTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Gestion des onglets login/register
            if(this.dataset.tab) {
                document.querySelectorAll('.form-tabs .form-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.form-content').forEach(content => content.classList.remove('active'));
                document.getElementById(this.dataset.tab + '-form').classList.add('active');
            }
        });
    });
    
    // Toggle entre particulier et professionnel
    const accountTabs = document.querySelectorAll('[data-account]');
    accountTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Gestion des onglets particulier/professionnel
            if(this.dataset.account) {
                document.querySelectorAll('[data-account]').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.account-form').forEach(content => content.classList.remove('active'));
                document.getElementById(this.dataset.account + '-form').classList.add('active');
            }
        });
    });
    
    // Toggle pour afficher/masquer le mot de passe
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordField = this.previousElementSibling;
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });
    
    // Validation de formulaire pour l'inscription particulier
    const particulierForm = document.getElementById('particulier-form');
    particulierForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Valider les mots de passe
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm').value;
        
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
        fetch('/api/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => {
                    window.location.href = 'login-success.html';
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
    
    // Validation de formulaire pour l'inscription professionnelle
    const proForm = document.getElementById('professionnel-form');
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
        const siret = document.getElementById('siret').value;
        if (!/^\d{14}$/.test(siret)) {
            showMessage('Veuillez saisir un numéro SIRET valide (14 chiffres)', 'error');
            return;
        }
        
        // Collecte des données du formulaire
        const formData = new FormData(proForm);
        
        // Envoyer les données au serveur
        fetch('/api/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => {
                    window.location.href = 'login-success.html';
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
    
    // Gestion du formulaire de connexion
    const loginForm = document.getElementById('login-form');
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Collecte des données du formulaire
        const formData = new FormData(loginForm);
        
        // Envoyer les données au serveur
        fetch('/api/login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                // Redirection vers l'espace client
                setTimeout(() => {
                    window.location.href = '/dashboard/index.php';
                }, 1000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('Une erreur est survenue. Veuillez réessayer plus tard.', 'error');
            console.error('Erreur:', error);
        });
    });
    
    // Fonction pour afficher les messages
    function showMessage(message, type) {
        const messageContainer = document.getElementById('messageContainer');
        messageContainer.innerHTML = '';
        
        const messageElement = document.createElement('div');
        messageElement.classList.add('alert');
        messageElement.classList.add(type === 'error' ? 'alert-danger' : 'alert-success');
        messageElement.textContent = message;
        
        messageContainer.appendChild(messageElement);
        
        // Faire défiler vers le haut pour voir le message
        messageContainer.scrollIntoView({ behavior: 'smooth' });
    }
});
