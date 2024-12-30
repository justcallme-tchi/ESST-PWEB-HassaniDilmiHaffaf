document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('.bookTableBody');
    const fetchAuteurs = () => {
        fetch('../php/auteur.php', {
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
            
            tableBody.innerHTML = '';
            data.forEach((auteur) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${auteur.NomAuteur}</td>
                    <td>
                        <button class="btn-edit" onclick="editAuteur(${auteur.IDAuteur}, '${auteur.NomAuteur}')">Modifier</button>
                        <button class="btn-delete" onclick="deleteAuteur(${auteur.IDAuteur})">Supprimer</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch((error) => console.error('Erreur de fetch :', error));
    };
    

    const addAuteurForm = document.getElementById('addBookForm');
    addAuteurForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(addAuteurForm);
        formData.append('action', 'add');

        fetch('../php/auteur.php', {
            method: 'POST',
            body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            fetchAuteurs();
        });
    });

    window.editAuteur = (id, nom) => {
        const newNom = prompt('Modifier le nom de l\'auteur', nom);
        if (newNom) {
            fetch('../php/auteur.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'update', idAuteur: id, nomAuteur: newNom }),
            })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message);
                fetchAuteurs();
            });
        }
    };

    window.deleteAuteur = (id) => {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet auteur ?')) {
            fetch('../php/auteur.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'delete', idAuteur: id }),
            })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message);
                fetchAuteurs();
            });
        }
    };

    // Déclenche la recherche à chaque frappe
    searchInput.addEventListener("input", function () {
        const query = searchInput.value;
    
        fetch("../php/searchAuteurs.php", {
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
                            <td>${author.NomAuteur}</td>
                    <td>
                        <button class="btn-edit" onclick="editAuteur(${author.IDAuteur}, '${author.NomAuteur}')">Modifier</button>
                        <button class="btn-delete" onclick="deleteAuteur(${author.IDAuteur})">Supprimer</button>
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

    fetchAuteurs();
});
