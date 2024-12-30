document.querySelector('.password-form').addEventListener('submit', function (e) {
    const newPassword = document.getElementById('new_password').value.trim();
    const confirmPassword = document.getElementById('confirm_password').value.trim();

    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert("Les nouveaux mots de passe ne correspondent pas.");
    }
});
