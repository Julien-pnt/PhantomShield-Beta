/**
 * Script pour gérer les fonctionnalités de connexion et d'inscription
 */
document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments du DOM
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const formTabs = document.querySelectorAll('.form-tab');
    const accountTabs = document.querySelectorAll('[data-account]');
    
    // Vérifier si l'utilisateur est déjà connecté
    AuthManager.checkAuth().then(result => {
        if (result.isLoggedIn) {
            window.location.href = '/dashboard/index.php'; // Redirection vers le tableau de bord
        }
    });
    
    // Gestion de la soumission du formulaire de connexion
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Désactivation du bouton pendant la soumission
            const submitButton = this.querySelector('.btn-login-register');
            submitButton.disabled = true;
            submitButton.innerText = 'Connexion en cours...';
            
            // Récupération des données du formulaire
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            // Validation basique côté client
            if (!email || !password) {
                showMessage('Veuillez remplir tous les champs', 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Se connecter';
                return;
            }
            
            // Appel à l'API de connexion
            const loginResult = await AuthManager.login(email, password);
            
            if (loginResult.success) {
                showMessage('Connexion réussie, redirection...', 'success');
                setTimeout(() => {
                    window.location.href = '/dashboard/index.php';
                }, 1500);
            } else {
                showMessage(loginResult.message, 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Se connecter';
            }
        });
    }
    
    // Gestion de la soumission du formulaire d'inscription (particulier)
    const particulierForm = document.getElementById('particulier-form');
    if (particulierForm) {
        particulierForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Désactivation du bouton pendant la soumission
            const submitButton = this.querySelector('.btn-login-register');
            submitButton.disabled = true;
            submitButton.innerText = 'Inscription en cours...';
            
            // Récupération des données du formulaire
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm').value;
            const firstName = document.getElementById('register-firstname').value;
            const lastName = document.getElementById('register-lastname').value;
            const termsAccepted = document.getElementById('terms').checked;
            
            // Validation basique côté client
            if (!email || !password || !firstName || !lastName) {
                showMessage('Veuillez remplir tous les champs obligatoires', 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Créer mon compte';
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Les mots de passe ne correspondent pas', 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Créer mon compte';
                return;
            }
            
            if (!termsAccepted) {
                showMessage('Vous devez accepter les conditions d\'utilisation', 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Créer mon compte';
                return;
            }
            
            // Préparation des données pour l'inscription
            const userData = {
                email: email,
                password: password,
                first_name: firstName,
                last_name: lastName,
                user_type: 'particulier'
            };
            
            // Appel à l'API d'inscription
            const registerResult = await AuthManager.register(userData);
            
            if (registerResult.success) {
                showMessage(registerResult.message, 'success');
                
                // Redirection vers l'onglet de connexion après un délai
                setTimeout(() => {
                    document.querySelector('[data-tab="login"]').click();
                }, 3000);
            } else {
                showMessage(registerResult.message, 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Créer mon compte';
            }
        });
    }
    
    // Gestion de la soumission du formulaire d'inscription (professionnel)
    const proForm = document.getElementById('professionnel-form');
    if (proForm) {
        proForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Désactivation du bouton pendant la soumission
            const submitButton = this.querySelector('.btn-login-register');
            submitButton.disabled = true;
            submitButton.innerText = 'Inscription en cours...';
            
            // Récupération des données du formulaire
            const companyName = document.getElementById('company-name').value;
            const siret = document.getElementById('siret').value;
            const contactName = document.getElementById('contact-name').value;
            const email = document.getElementById('company-email').value;
            const phone = document.getElementById('company-tel').value;
            const sector = document.getElementById('secteur').value;
            const password = document.getElementById('pro-password').value;
            const confirmPassword = document.getElementById('pro-confirm').value;
            const termsAccepted = document.getElementById('pro-terms').checked;
            
            // Validation basique côté client
            if (!companyName || !siret || !contactName || !email || !password || !sector) {
                showMessage('Veuillez remplir tous les champs obligatoires', 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Créer un compte professionnel';
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Les mots de passe ne correspondent pas', 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Créer un compte professionnel';
                return;
            }
            
            if (!termsAccepted) {
                showMessage('Vous devez accepter les conditions d\'utilisation', 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Créer un compte professionnel';
                return;
            }
            
            // Préparation des données pour l'inscription
            const userData = {
                email: email,
                password: password,
                company_name: companyName,
                siret: siret,
                contact_name: contactName,
                phone: phone,
                sector: sector,
                user_type: 'professionnel'
            };
            
            // Appel à l'API d'inscription
            const registerResult = await AuthManager.register(userData);
            
            if (registerResult.success) {
                showMessage(registerResult.message, 'success');
                
                // Redirection vers l'onglet de connexion après un délai
                setTimeout(() => {
                    document.querySelector('[data-tab="login"]').click();
                }, 3000);
            } else {
                showMessage(registerResult.message, 'error');
                submitButton.disabled = false;
                submitButton.innerText = 'Créer un compte professionnel';
            }
        });
    }
    
    // Fonction pour afficher des messages à l'utilisateur
    function showMessage(message, type) {
        // Création ou récupération de l'élément message
        let messageElement = document.querySelector('.message-container');
        
        if (!messageElement) {
            messageElement = document.createElement('div');
            messageElement.className = 'message-container';
            document.querySelector('.login-register-form').prepend(messageElement);
        }
        
        messageElement.innerHTML = `<div class="message ${type}">${message}</div>`;
        
        // Auto-disparition du message après 5 secondes
        setTimeout(() => {
            messageElement.innerHTML = '';
        }, 5000);
    }
});
