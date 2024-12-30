// Gestion de la validation de la réservation avec toutes les validations
document.querySelector("#reservation-form form").addEventListener("submit", function (e) {
    e.preventDefault(); // Empêche le rechargement par défaut du formulaire

    const titreInput = document.getElementById("titre");
    const auteurInput = document.getElementById("auteur");
    const dateInput = document.getElementById("date");

    const reservationDate = new Date(dateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Supprime les heures pour comparer uniquement les dates

    // Expressions régulières pour valider les noms
    const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/;

    // Collecte des erreurs
    let errors = [];

    // Validation du titre
    if (!titreInput.value.trim()) {
        errors.push("Le titre est obligatoire.");
    } else if (!nameRegex.test(titreInput.value)) {
        errors.push("Le titre ne peut contenir que des lettres, espaces, apostrophes ou tirets.");
    } else if (titreInput.value.trim().length < 2 || titreInput.value.trim().length > 100) {
        errors.push("Le titre doit contenir entre 2 et 100 caractères.");
    }

    // Validation de l'auteur
    if (!auteurInput.value.trim()) {
        errors.push("Le nom de l'auteur est obligatoire.");
    } else if (!nameRegex.test(auteurInput.value)) {
        errors.push("Le nom de l'auteur ne peut contenir que des lettres, espaces, apostrophes ou tirets.");
    } else if (auteurInput.value.trim().length < 2 || auteurInput.value.trim().length > 100) {
        errors.push("Le nom de l'auteur doit contenir entre 2 et 100 caractères.");
    }

    // Nettoyage des espaces inutiles
    titreInput.value = titreInput.value.replace(/\s+/g, ' ').trim();
    auteurInput.value = auteurInput.value.replace(/\s+/g, ' ').trim();

    // Validation de la date
    if (!dateInput.value) {
        errors.push("La date de réservation est obligatoire.");
    } else if (reservationDate < today) {
        errors.push("La date de réservation ne peut pas être antérieure à aujourd'hui.");
    } else if (reservationDate > today.setFullYear(today.getFullYear() + 1)) {
        errors.push("La date de réservation est trop éloignée (plus d'un an).");
    } else if (reservationDate.getDay() === 0 || reservationDate.getDay() === 6) {
        errors.push("Les réservations ne sont pas autorisées les week-ends.");
    }

    // Affichage des erreurs
    if (errors.length > 0) {
        alert(errors.join("\n"));
        return;
    }

    // Message de confirmation
    const confirmReservation = confirm(`Confirmez-vous la réservation pour le livre "${titreInput.value}" écrit par "${auteurInput.value}" le ${reservationDate.toLocaleDateString()} ?`);
    if (confirmReservation) {
        alert("Réservation confirmée avec succès !");
        // Vous pouvez soumettre le formulaire ici si tout est valide
        const submitButton = document.querySelector(".btn-submit");
        submitButton.disabled = true;
        submitButton.textContent = "Réservation en cours...";

        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.textContent = "Réserver";
            this.submit();
        }, 3000); // Réactivez après 3 secondes
    } else {
        alert("Réservation annulée.");
    }
});
