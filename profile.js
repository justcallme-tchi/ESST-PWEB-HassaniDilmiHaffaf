// Gestion des validations du formulaire de profil
document.querySelector(".profile-form").addEventListener("submit", function (e) {
    e.preventDefault(); // Empêche la soumission par défaut

    const nomInput = document.getElementById("nom");
    const emailInput = document.getElementById("email");
    const telInput = document.getElementById("tel");
    const adresseInput = document.getElementById("adresse");

    // Expressions régulières pour validation
    const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/; // Noms valides
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; // Emails valides
    const telRegex = /^\+?\d{10,15}$/; // Numéros de téléphone valides (ex. : +1234567890)
    const addressRegex = /^[A-Za-z0-9À-ÖØ-öø-ÿ\s,'-]+$/; // Adresses valides

    let errors = [];

    // Validation du nom
    if (!nomInput.value.trim()) {
        errors.push("Le champ 'Nom' est obligatoire.");
    } else if (!nameRegex.test(nomInput.value)) {
        errors.push("Le nom ne peut contenir que des lettres, espaces, apostrophes et tirets.");
    } else if (nomInput.value.length < 2 || nomInput.value.length > 100) {
        errors.push("Le nom doit contenir entre 2 et 100 caractères.");
    }

    // Validation de l'email
    if (!emailInput.value.trim()) {
        errors.push("Le champ 'Email' est obligatoire.");
    } else if (!emailRegex.test(emailInput.value)) {
        errors.push("L'adresse email est invalide.");
    }

    // Validation du téléphone
    if (!telInput.value.trim()) {
        errors.push("Le champ 'Téléphone' est obligatoire.");
    } else if (!telRegex.test(telInput.value)) {
        errors.push("Le numéro de téléphone doit contenir entre 10 et 15 chiffres et peut commencer par '+'.");
    }

    // Validation de l'adresse
    if (!adresseInput.value.trim()) {
        errors.push("Le champ 'Adresse' est obligatoire.");
    } else if (!addressRegex.test(adresseInput.value)) {
        errors.push("L'adresse ne peut contenir que des lettres, chiffres, espaces, apostrophes, virgules, et tirets.");
    } else if (adresseInput.value.length < 5 || adresseInput.value.length > 200) {
        errors.push("L'adresse doit contenir entre 5 et 200 caractères.");
    }

    // Affichage des erreurs
    if (errors.length > 0) {
        alert(errors.join("\n"));
        return;
    }

    // Si tout est valide
    alert("Votre profil a été mis à jour avec succès !");
    this.submit(); // Soumet le formulaire
});
