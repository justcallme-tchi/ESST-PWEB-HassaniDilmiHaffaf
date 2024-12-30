document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('.categoryTableBody');

    const fetchCategories = () => {
        fetch('../php/categorie.php', {
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
            const tableBody = document.querySelector('.categoryTableBody');
            tableBody.innerHTML = '';
            data.forEach((categorie) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${categorie.NomCategorie}</td>
                    <td>
                        <button class="btn-edit" onclick="editCategory(${categorie.NumCategorie}, '${categorie.NomCategorie}')">Modifier</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch((error) => console.error('Erreur de fetch :', error));
    };

    const addCategoryForm = document.getElementById('addCategoryForm');
    addCategoryForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(addCategoryForm);
        formData.append('action', 'add');

        fetch('../php/categorie.php', {
            method: 'POST',
            body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            fetchCategories();
        });
    });

    window.editCategory = (id, name) => {
        const newName = prompt('Modifier le nom de la catégorie', name);
        if (newName) {
            fetch('../php/categorie.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'update', NumCategorie: id, NomCategorie: newName }),
            })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message);
                fetchCategories();
            });
        }
    };

    searchInput.addEventListener("input", function () {
        const query = searchInput.value;
    
        fetch("../php/searchCategories.php", {
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
                            <td>${author.NomCategorie}</td>
                    <td>
                        <button class="btn-edit" onclick="editAuteur(${author.NumCategorie}, '${author.NomCategorie}')">Modifier</button>
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

    fetchCategories();
});
