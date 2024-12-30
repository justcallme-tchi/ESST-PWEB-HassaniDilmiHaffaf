document.addEventListener('DOMContentLoaded', function () {
    const userTableBody = document.querySelector('.bookTableBody');
    const addUserForm = document.getElementById('addBookForm');
    const etatEtudiantSelect = document.getElementById('etatEtudiant');
    const searchInput = document.getElementById("searchInput");


    // Charger la liste des utilisateurs
    function loadUsers() {
        fetch('../php/manage_users.php?action=list')
            .then(response => response.json())
            .then(data => {
                userTableBody.innerHTML = '';
                data.forEach(user => {
                    const row = `
                        <tr>
                            <td>${user.MatriculeEtudiant}</td>
                            <td>${user.Nom}</td>
                            <td>${user.Prenom}</td>
                            <td>${user.DateNaissance}</td>
                            <td>${user.email}</td>
                            <td>${user.EtatEtudiant}</td>
                            <td>
                                <button class="btn-edit" data-id="${user.MatriculeEtudiant}">Modifier</button>
                                <button class="btn-delete" data-id="${user.MatriculeEtudiant}">Supprimer</button>
                            </td>
                        </tr>
                    `;
                    userTableBody.insertAdjacentHTML('beforeend', row);
                });
            });
    }

    // Gérer la soumission du formulaire (ajout ou modification)
    addUserForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(addUserForm);
        const userData = Object.fromEntries(formData.entries());
        const editId = addUserForm.getAttribute('data-edit-id');

        if (editId) {
            // Modification d'un utilisateur
            userData.MatriculeEtudiant = editId;

            fetch('../php/manage_users.php?action=update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Utilisateur modifié avec succès');
                        addUserForm.reset();
                        addUserForm.removeAttribute('data-edit-id');
                        loadUsers();
                    } else {
                        alert('Erreur lors de la modification : ' + data.error);
                    }
                });
        } else {
            // Ajout d'un nouvel utilisateur
            fetch('../php/manage_users.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Utilisateur ajouté avec succès');
                        addUserForm.reset();
                        loadUsers();
                    } else {
                        alert('Erreur lors de l\'ajout : ' + data.error);
                    }
                });
        }
    });

    // Gérer les clics pour modifier ou supprimer un utilisateur
    userTableBody.addEventListener('click', function (event) {
        const target = event.target;
        const userId = target.getAttribute('data-id');

        if (target.classList.contains('btn-delete')) {
            fetch(`../php/manage_users.php?action=delete&MatriculeEtudiant=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUsers();
                    } else {
                        alert('Erreur lors de la suppression : ' + data.error);
                    }
                });
        } else if (target.classList.contains('btn-edit')) {
            fetch(`../php/manage_users.php?action=get_student&MatriculeEtudiant=${userId}`)
    .then(response => response.json())
    .then(data => {
        if (data) {
            addUserForm.elements['Nom'].value = data.Nom;
            addUserForm.elements['Prenom'].value = data.Prenom;
            addUserForm.elements['DateNaissance'].value = data.DateNaissance;
            addUserForm.elements['email'].value = data.email;

            // Supprimer 'required' du champ mot de passe
            const motDePasseField = addUserForm.elements['mot_de_passe'];
            motDePasseField.value = ''; // Laisser vide
            motDePasseField.removeAttribute('required');

            etatEtudiantSelect.value = data.EtatEtudiant;

            addUserForm.setAttribute('data-edit-id', userId);
        }
    });

        }
    });

    // Rechercher des utilisateurs
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('.book-table tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
// Déclenche la recherche à chaque frappe
searchInput.addEventListener("input", function () {
    const query = searchInput.value;

    // Envoi de la requête AJAX
    fetch("../php/searchUsers.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `query=${encodeURIComponent(query)}`,
    })
        .then((response) => response.json())
        .then((data) => {
            userTableBody.innerHTML = ""; // Vide la table avant d'ajouter les résultats

            if (data.length > 0) {
                data.forEach((student) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${student.MatriculeEtudiant}</td>
                        <td>${student.Nom}</td>
                        <td>${student.Prenom}</td>
                        <td>${student.DateNaissance}</td>
                        <td>${student.email}</td>
                        <td>${student.EtatEtudiant}</td>
                        <td>
                            <button class="btn-edit" data-id="${student.MatriculeEtudiant}">Modifier</button>
                            <button class="btn-delete" data-id="${student.MatriculeEtudiant}">Supprimer</button>
                        </td>
                    `;
                    userTableBody.appendChild(row);
                });
            } else {
                userTableBody.innerHTML = "<tr><td colspan='7'>Aucun étudiant trouvé.</td></tr>";
            }
        })
        .catch((error) => console.error("Erreur :", error));
});

    // Charger les utilisateurs au chargement de la page
    loadUsers();
});
