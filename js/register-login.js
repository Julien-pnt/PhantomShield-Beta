document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const formTabs = document.querySelectorAll('.form-tab');
    const formContents = document.querySelectorAll('.form-content');
    const registerOptions = document.querySelectorAll('.register-option');
    const registerForms = document.querySelectorAll('.register-form');
    const passwordToggles = document.querySelectorAll('.password-toggle');
    const loginForm = document.getElementById('login-form');
    const particulierForm = document.getElementById('particulier-form');
    const professionnelForm = document.getElementById('professionnel-form');
    const messageContainer = document.getElementById('messageContainer');
    
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
    registerOptions.forEach(option => {
        option.addEventListener('click', function() {
            registerOptions.forEach(o => o.classList.remove('active'));
            registerForms.forEach(f => f.classList.remove('active'));
            
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
    
    // Évaluation de la force du mot de passe
    const passwordInputs = document.querySelectorAll('input[type="password"][name="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const container = this.parentElement;
            const strengthBar = container.querySelector('.strength-bar');
            const strengthLabel = container.querySelector('.strength-label span');
            
            if(strengthBar && strengthLabel) {
                const password = this.value;
                let strength = 0;
                
                // Critères d'évaluation de la force
                if(password.length >= 8) strength += 1;
                if(/[A-Z]/.test(password)) strength += 1;
                if(/[a-z]/.test(password)) strength += 1;
                if(/[0-9]/.test(password)) strength += 1;
                if(/[^A-Za-z0-9]/.test(password)) strength += 1;
                
                // Mise à jour de l'indicateur de force
                switch(strength) {
                    case 0:
                    case 1:
                        strengthBar.style.width = '20%';
                        strengthBar.style.backgroundColor = '#ff4d4d';
                        strengthLabel.textContent = 'Très faible';
                        break;
                    case 2:
                        strengthBar.style.width = '40%';
                        strengthBar.style.backgroundColor = '#ff8533';
                        strengthLabel.textContent = 'Faible';
                        break;
                    case 3:
                        strengthBar.style.width = '60%';
                        strengthBar.style.backgroundColor = '#ffcc00';
                        strengthLabel.textContent = 'Moyen';
                        break;
                    case 4:
                        strengthBar.style.width = '80%';
                        strengthBar.style.backgroundColor = '#99cc33';
                        strengthLabel.textContent = 'Fort';
                        break;
                    case 5:
                        strengthBar.style.width = '100%';
                        strengthBar.style.backgroundColor = '#33cc33';
                        strengthLabel.textContent = 'Très fort';
                        break;
                }
            }
        });
    });
    
    // Vérification de la correspondance des mots de passe
    const confirmPasswords = document.querySelectorAll('input[name="confirm_password"]');
    confirmPasswords.forEach(input => {
        input.addEventListener('input', function() {
            const form = this.closest('form');
            const password = form.querySelector('input[name="password"]').value;
            const confirmPassword = this.value;
            const feedbackElement = this.parentElement.querySelector('.invalid-feedback');
            
            if(feedbackElement) {
                if(confirmPassword && password !== confirmPassword) {
                    feedbackElement.style.display = 'block';
                    this.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    feedbackElement.style.display = 'none';
                    this.setCustomValidity('');
                }
            }
        });
    });
    
    // Fonction pour afficher des messages
    function showMessage(message, type) {
        messageContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        messageContainer.style.display = 'block';
        
        // Faire défiler vers le message si nécessaire
        messageContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Masquer le message après 5 secondes
        setTimeout(() => {
            messageContainer.innerHTML = '';
            messageContainer.style.display = 'none';
        }, 5000);
    }
    
    // Soumission du formulaire de connexion
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        
        // Validation basique côté client
        if(!email || !password) {
            showMessage('Veuillez remplir tous les champs', 'error');
            return;
        }
        
        // Créer l'objet FormData pour l'envoi
        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        formData.append('remember', document.getElementById('remember').checked ? '1' : '0');
        
        // Envoyer les données au serveur
        fetch('/public/api/login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showMessage('Connexion réussie! Redirection...', 'success');
                setTimeout(() => {
                    window.location.href = '/dashboard/index.php';
                }, 1500);
            } else {
                showMessage(data.message || 'Identifiants incorrects', 'error');
            }
        })
        .catch(error => {
            showMessage('Une erreur est survenue. Veuillez réessayer.', 'error');
            console.error('Erreur:', error);
        });
    });
    
    // Soumission du formulaire d'inscription particulier
    particulierForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation des champs
        const password = document.getElementById('part-password').value;
        const confirmPassword = document.getElementById('part-confirm').value;
        
        if(password.length < 8) {
            showMessage('Le mot de passe doit contenir au moins 8 caractères', 'error');
            return;
        }
        
        if(password !== confirmPassword) {
            showMessage('Les mots de passe ne correspondent pas', 'error');
            return;
        }
        
        if(!document.getElementById('part-terms').checked) {
            showMessage('Vous devez accepter les conditions d\'utilisation', 'error');
            return;
        }
        
        // Collecte et envoi des données
        const formData = new FormData(particulierForm);
        
        fetch('/public/api/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showMessage('Inscription réussie! Veuillez vérifier votre email.', 'success');
                particulierForm.reset();
            } else {
                showMessage(data.message || 'Erreur lors de l\'inscription', 'error');
            }
        })
        .catch(error => {
            showMessage('Une erreur est survenue. Veuillez réessayer.', 'error');
            console.error('Erreur:', error);
        });
    });
    
    // Soumission du formulaire d'inscription professionnel
    professionnelForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation des champs
        const password = document.getElementById('pro-password').value;
        const confirmPassword = document.getElementById('pro-confirm').value;
        const siret = document.getElementById('siret').value;
        
        if(password.length < 8) {
            showMessage('Le mot de passe doit contenir au moins 8 caractères', 'error');
            return;
        }
        
        if(password !== confirmPassword) {
            showMessage('Les mots de passe ne correspondent pas', 'error');
            return;
        }
        
        // Validation du SIRET (14 chiffres en France)
        if(!/^\d{14}$/.test(siret)) {
            showMessage('Le numéro SIRET doit contenir 14 chiffres', 'error');
            return;
        }
        
        if(!document.getElementById('pro-terms').checked) {
            showMessage('Vous devez accepter les conditions d\'utilisation', 'error');
            return;
        }
        
        // Collecte et envoi des données
        const formData = new FormData(professionnelForm);
        
        fetch('/public/api/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showMessage('Inscription réussie! Veuillez vérifier votre email.', 'success');
                professionnelForm.reset();
            } else {
                showMessage(data.message || 'Erreur lors de l\'inscription', 'error');
            }
        })
        .catch(error => {
            showMessage('Une erreur est survenue. Veuillez réessayer.', 'error');
            console.error('Erreur:', error);
        });
    });
});
