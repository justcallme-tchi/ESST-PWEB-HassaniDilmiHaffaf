// JavaScript for Notify.htm page

document.querySelectorAll('.notify-btn').forEach(button => {
    button.addEventListener('click', function() {
        const row = button.closest('tr');
        const userName = row.cells[1].textContent;
        const bookTitle = row.cells[2].textContent;

        // Envoi de la notification (simulé par une alerte ici)
        alert(`Notification envoyée à ${userName} pour le livre "${bookTitle}".`);

        // Mise à jour de l'interface après la notification
        row.cells[4].textContent = "Notifié"; // Mise à jour du statut à "Notifié"
        button.textContent = "Notifié";
        button.disabled = true; // Désactiver le bouton pour éviter de notifier à nouveau
    });
});
