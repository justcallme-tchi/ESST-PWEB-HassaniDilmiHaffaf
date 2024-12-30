document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('.bookTableBody');

    const fetcheditions = () => {
        fetch('../php/edition.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'fetch' }),
        })
        .then((response) => response.json())
        .then((data) => {
            if (!Array.isArray(data)) {
                console.error('Erreur serveur :', data);
                alert('Erreur serveur : ' + (data.message || 'Réponse inattendue'));
                return;
            }
            const tableBody = document.querySelector('.bookTableBody');
            tableBody.innerHTML = '';
            data.forEach((edition) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${edition.AnneeEdition}</td>
                    <td>
                        <button class="btn-edit" onclick="editedition(${edition.NumEdition}, '${edition.AnneeEdition}')">Modifier</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch((error) => console.error('Erreur de fetch :', error));
    };
    

    const addeditionForm = document.getElementById('addBookForm');
    addeditionForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(addeditionForm);
        formData.append('action', 'add');

        fetch('../php/edition.php', {
            method: 'POST',
            body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            fetcheditions();
        });
    });

    window.editedition = (id, nom) => {
        const newNom = prompt('Modifier l\'année de l\'edition', nom);
        if (newNom) {
            fetch('../php/edition.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'update', NumEdition: id, AnneeEdition: newNom }),
            })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message);
                fetcheditions();
            });
        }
    };

    searchInput.addEventListener("input", function () {
        const query = searchInput.value;
    
        fetch("../php/searchEditions.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `query=${encodeURIComponent(query)}`,
        })
            .then((response) => response.json())
            .then((data) => {
                tableBody.innerHTML = ""; // Vide la table avant d'ajouter les résultats
    
                if (data.length > 0) {
                    data.forEach((author) => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${author.AnneeEdition}</td>
                    <td>
                        <button class="btn-edit" onclick="editAuteur(${author.NumEdition}, '${author.AnneeEdition}')">Modifier</button>
                    </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    tableBody.innerHTML = "<tr><td colspan='2'>Aucun auteur trouvé.</td></tr>";
                }
            })
            .catch((error) => console.error("Erreur :", error));
    });

    
    fetcheditions();
});
