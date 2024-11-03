const boutonInscription = document.getElementById('signUp');
const boutonConnexion = document.getElementById('signIn');
const conteneur = document.getElementById('container');

boutonInscription.addEventListener('click', () => {
    conteneur.classList.add("right-panel-active");
});

boutonConnexion.addEventListener('click', () => {
    conteneur.classList.remove("right-panel-active");
});

function loginWithFacebook() {
    window.location.href = `https://www.facebook.com/v13.0/dialog/oauth?client_id=VOTRE_IDENTIFIANT_CLIENT_FACEBOOK&redirect_uri=https://votresite.com/facebook_callback.php&scope=email,public_profile`;
}

function loginWithGoogle() {
    window.location.href = `https://accounts.google.com/o/oauth2/auth?client_id=VOTRE_IDENTIFIANT_CLIENT_GOOGLE&redirect_uri=https://votresite.com/google_callback.php&response_type=code&scope=email profile`;
}

function loginWithLinkedIn() {
    window.location.href = `https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=VOTRE_IDENTIFIANT_CLIENT_LINKEDIN&redirect_uri=https://votresite.com/linkedin_callback.php&scope=r_liteprofile%20r_emailaddress`;
}
document.querySelector('.sign-up-container form').addEventListener('submit', function (event) {
    const roleSelectedSignup = document.querySelector('input[name="role-signup"]:checked');
    const roleErrorSignup = document.getElementById('roleErrorSignup');

    if (!roleSelectedSignup) {
        event.preventDefault(); // Empêche l'envoi du formulaire
        roleErrorSignup.style.display = 'block'; // Affiche le message d'erreur
    } else {
        roleErrorSignup.style.display = 'none'; // Cache le message si un rôle est sélectionné
    }
});





