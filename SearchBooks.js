// Fichier : SearchBooks.js

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".search-books-form");
    const resultsTable = document.querySelector(".search-results-table tbody");

    // Fonction pour charger tous les livres au chargement de la page
    function loadBooks() {
        fetch('../php/get_books.php')
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                displayBooks(data);
            })
            .catch((error) => {
                console.error("Erreur lors du chargement des livres :", error);
            });
    }
    

    // Fonction pour afficher les livres dans le tableau
    function displayBooks(data) {
        resultsTable.innerHTML = "";
        if (data.length > 0) {
            data.forEach((book) => {
                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${book.Titre}</td>
                    <td>${book.NomAuteur || "Inconnu"}</td>
                    <td>${book.AnneeEdition || "Inconnue"}</td>
                    <td>${book.NomCategorie || "Inconnue"}</td>
                    <td>${book.ExemplairesDisponibles > 0 ? "Disponible" : "Non Disponible"}</td>
                    <td>
                        <button class="btn-reserve" ${
                            book.ExemplairesDisponibles > 0 ? "" : "disabled"
                        }>${book.ExemplairesDisponibles > 0 ? "Réserver" : "Indisponible"}</button>
                    </td>
                `;

                const reserveButton = row.querySelector(".btn-reserve");
                if (book.ExemplairesDisponibles > 0) {
                    reserveButton.addEventListener("click", () => {
                        reserveBook(book.CodeOuvrage);
                    });
                }

                resultsTable.appendChild(row);
            });
        } else {
            resultsTable.innerHTML = "<tr><td colspan='5'>Aucun résultat trouvé.</td></tr>";
        }
    }

    // Gestion de la recherche
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const queryString = new URLSearchParams(formData).toString();

        fetch(`../php/get_books.php?${queryString}`)
            .then((response) => response.json())
            .then((data) => {
                displayBooks(data);
            })
            .catch((error) => {
                console.error("Erreur lors de la recherche des livres :", error);
            });
    });

    // Fonction pour gérer la réservation des livres
    function reserveBook(codeOuvrage) {
        fetch("../php/reserve_book.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ codeOuvrage }),
        })
            .then((response) => {response.json()})
            .then((result) => {
                loadBooks(); // Recharger la liste des livres après la réservation
            })
            .catch((error) => {
                
                console.error("Erreur Lors de la réservation :", error, );
            });
    }

    // Charger les livres au chargement de la page
    loadBooks();
});
