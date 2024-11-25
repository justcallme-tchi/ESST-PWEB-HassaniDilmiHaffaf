// Gestion des clics sur les boutons "Réserver"
document.querySelectorAll(".btn-reserve").forEach((button) => {
    button.addEventListener("click", function () {
        if (button.disabled) {
            alert("Ce livre est indisponible.");
        } else {
            const bookTitle = this.closest("tr").querySelector("td:first-child").textContent;
            const confirmReservation = confirm(`Voulez-vous vraiment réserver le livre : ${bookTitle} ?`);
            if (confirmReservation) {
                alert(`Réservation confirmée pour le livre : ${bookTitle}`);
            } else {
                alert("Réservation annulée.");
            }
        }
    });
});

/// Gestion de la recherche dynamique par un seul champ
document.querySelector(".search-books-form").addEventListener("submit", function (e) {
    e.preventDefault(); // Empêche le rechargement de la page

    // Récupérer les valeurs des champs de recherche
    const searchTitle = document.getElementById("searchTitle").value.toLowerCase().trim();
    const searchAuthor = document.getElementById("searchAuthor").value.toLowerCase().trim();
    const searchYear = document.getElementById("searchYear").value.toLowerCase().trim();

    // Récupérer toutes les lignes de la table
    const rows = document.querySelectorAll(".search-results-table tbody tr");

    // Filtrer les lignes selon les champs remplis
    rows.forEach((row) => {
        const title = row.children[0].textContent.toLowerCase();
        const author = row.children[1].textContent.toLowerCase();
        const year = row.children[2].textContent.toLowerCase();

        // Condition : une correspondance sur l'un des champs remplis
        if (
            (searchTitle && title.includes(searchTitle)) ||
            (searchAuthor && author.includes(searchAuthor)) ||
            (searchYear && year.includes(searchYear))
        ) {
            row.style.display = ""; // Afficher la ligne
        } else {
            row.style.display = "none"; // Masquer la ligne
        }
    });
});


