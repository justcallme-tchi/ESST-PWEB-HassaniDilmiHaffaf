// Gestion des clics sur les boutons
document.querySelectorAll(".status").forEach((button) => {
    button.addEventListener("click", function () {
        const statusText = this.textContent;
        if (statusText === "Retour Approche") {
            alert("Prenez des mesures pour rendre le livre bientôt.");
        } else if (statusText === "En Retard") {
            alert("Veuillez rendre le livre immédiatement.");
        } else if (statusText === "En Temps") {
            alert("Continuez à respecter les délais.");
        }
    });
});
