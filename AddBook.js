document.addEventListener('DOMContentLoaded', function () {
    const bookTableBody = document.querySelector('.bookTableBody');
    const addBookForm = document.getElementById('addBookForm');
    const numEditionSelect = document.getElementById('numEdition');
    const numCategorieSelect = document.getElementById('numCategorie');
    const auteurSelect = document.getElementById('auteur');
    const localisationSelect = document.getElementById('localisation');
    const searchInput = document.getElementById("searchInput");


    // Charger la liste des livres
    function loadBooks() {
        fetch('../php/manage_books.php?action=list')
            .then(response => response.json())
            .then(data => {
                bookTableBody.innerHTML = '';
                data.forEach(book => {
                    const row = `
                        <tr>
                            <td>${book.Titre}</td>
                            <td>${book.DateAcquisition}</td>
                            <td>${book.DateEdition}</td>
                            <td>${book.AnneeEdition}</td>
                            <td>${book.NomCategorie}</td>
                            <td>${book.NomAuteur}</td>
                            <td>${book.NomLocalisation}</td>
                            <td>
                                <button class="btn-edit" data-id="${book.CodeOuvrage}">Modifier</button>
                                <button class="btn-delete" data-id="${book.CodeOuvrage}">Supprimer</button>
                            </td>
                        </tr>
                    `;
                    bookTableBody.insertAdjacentHTML('beforeend', row);
                });
            });
    }

    // Charger les options des champs de sélection
    function loadSelectOptions() {
        fetch('../php/manage_books.php?action=get_editions')
            .then(response => response.json())
            .then(data => {
                data.forEach(edition => {
                    const option = document.createElement('option');
                    option.value = edition.NumEdition;
                    option.textContent = edition.AnneeEdition;
                    numEditionSelect.appendChild(option);
                });
            });

        fetch('../php/manage_books.php?action=get_categories')
            .then(response => response.json())
            .then(data => {
                data.forEach(categorie => {
                    const option = document.createElement('option');
                    option.value = categorie.NumCategorie;
                    option.textContent = categorie.NomCategorie;
                    numCategorieSelect.appendChild(option);
                });
            });

        fetch('../php/manage_books.php?action=get_auteurs')
            .then(response => response.json())
            .then(data => {
                data.forEach(auteur => {
                    const option = document.createElement('option');
                    option.value = auteur.IDAuteur;
                    option.textContent = auteur.NomAuteur;
                    auteurSelect.appendChild(option);
                });
            });

        fetch('../php/manage_books.php?action=get_localisations')
            .then(response => response.json())
            .then(data => {
                data.forEach(localisation => {
                    const option = document.createElement('option');
                    option.value = localisation.IDLocalisation;
                    option.textContent = localisation.NomLocalisation;
                    localisationSelect.appendChild(option);
                });
            });
    }

    // Gérer la soumission du formulaire (ajout ou modification)
    addBookForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(addBookForm);
        const bookData = Object.fromEntries(formData.entries());
        bookData.Auteur = auteurSelect.value;

        const editId = addBookForm.getAttribute('data-edit-id');

        if (editId) {
            // Modification d'un ouvrage
            bookData.CodeOuvrage = editId;

            fetch('../php/manage_books.php?action=update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(bookData),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Livre modifié avec succès');
                        addBookForm.reset();
                        addBookForm.removeAttribute('data-edit-id');
                        loadBooks();
                    } else {
                        alert('Erreur lors de la modification : ' + data.error);
                    }
                });
        } else {
            // Ajout d'un nouvel ouvrage
            fetch('../php/manage_books.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(bookData),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Livre ajouté avec succès');
                        addBookForm.reset();
                        loadBooks();
                    } else {
                        alert('Erreur lors de l\'ajout du livre : ' + data.error);
                    }
                });
        }
    });

    // Gérer les clics pour modifier ou supprimer un livre
    bookTableBody.addEventListener('click', function (event) {
        const target = event.target;
        const bookId = target.getAttribute('data-id');

        if (target.classList.contains('btn-delete')) {
            fetch(`../php/manage_books.php?action=delete&CodeOuvrage=${bookId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadBooks();
                    } else {
                        alert('Erreur lors de la suppression : ' + data.error);
                    }
                });
        } else if (target.classList.contains('btn-edit')) {
            fetch(`../php/manage_books.php?action=get_book&CodeOuvrage=${bookId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        addBookForm.elements['Titre'].value = data.Titre;
                        addBookForm.elements['DateAcquisition'].value = data.DateAcquisition;
                        addBookForm.elements['DateEdition'].value = data.DateEdition;
                        numEditionSelect.value = data.NumEdition;
                        numCategorieSelect.value = data.NumCategorie;
                        auteurSelect.value = data.IDAuteur;
                        localisationSelect.value = data.IDLocalisation;

                        addBookForm.setAttribute('data-edit-id', bookId);
                    }
                });
        }
    });
    
        // Déclenche la recherche à chaque frappe
        searchInput.addEventListener("input", function () {
            const query = searchInput.value;
    
            // Envoi de la requête AJAX
            fetch("../php/searchBooks.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `query=${encodeURIComponent(query)}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    const tableBody = document.querySelector(".bookTableBody");
                    tableBody.innerHTML = ""; // Vide la table avant d'ajouter les résultats
    
                    if (data.length > 0) {
                        data.forEach((book) => {
                            const row = document.createElement("tr");
                            row.innerHTML = `
                                <td>${book.Titre}</td>
                                <td>${book.DateAcquisition}</td>
                                <td>${book.DateEdition}</td>
                                <td>${book.AnneeEdition || "N/A"}</td>
                                <td>${book.NomCategorie || "N/A"}</td>
                                <td>${book.NomAuteur || "N/A"}</td>
                                <td>${book.NomLocalisation || "N/A"}</td>
                                <td>
                                    <button class="btn-edit" data-id="${book.CodeOuvrage}">Modifier</button>
                                    <button class="btn-delete" data-id="${book.CodeOuvrage}">Supprimer</button>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = "<tr><td colspan='8'>Aucun livre trouvé.</td></tr>";
                    }
                })
                .catch((error) => console.error("Erreur :", error));
        });    

    loadSelectOptions();
    loadBooks();
});
