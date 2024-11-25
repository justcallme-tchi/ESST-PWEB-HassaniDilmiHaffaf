// Gestion de la soumission du formulaire d'ajout/modification de livre
document.getElementById("addBookForm").onsubmit = function(event) {
    event.preventDefault();

    const year = document.querySelector('select#year').value.trim();
    const filiere = document.querySelector('select#filiere').value.trim();
    const specialite = document.querySelector('select#specialite').value.trim();
    const module = document.querySelector('select#module').value.trim();
    const titre = document.querySelector('input#titre').value.trim();
    const auteur = document.querySelector('input#auteur').value.trim();
    const edition = document.querySelector('input#edition').value.trim();
    const exemplaires = document.querySelector('input#exemplaires').value.trim();

    // Regular Expressions pour validation
    const yearRegex = /^(L1|L2|L3|M1|M2)$/;
    const filiereRegex = /^(ST|SM|MI|ISSIL|GENERALE)$/;
    const specialiteRegex = /^(Électronique|Chimie|Informatique)$/i;
    const moduleRegex = /^(PFE|PHYSIQUE|CHIMIE|ANGLAIS|FRANCAIS|CHROMATOGRAPHIE|CINETIQUE|CHIMIE MINERALE|INORGANIQUE|THERMODYNAMIQUE|PHARMACOLOGIE|BIOCHIMIE|CRYPTOGRAPHIE|MANAGEMENT|PROGRAMMATION|POO|ALGEBRE|RESEAUX|BDD|ADO|THL|TG|CRI|PROBABILITE|STRUCTURE MACHINE|SYSTEME D'EXPLOITATION|ELECTRONIQUE|DECOUVERTE)$/i;
    const editionRegex = /^(19[6-9]\d|20[0-2]\d|2024)$/;
    const exemplaireRegex = /^[1-9]|[1-4][0-9]|50$/;

    // Validation des champs du formulaire
    if (!yearRegex.test(year)) {
        alert("L'année doit être parmi : L1, L2, L3, M1, M2.");
        return;
    }
    if (!filiereRegex.test(filiere)) {
        alert("La filière doit être parmi : ST, SM, MI, ISSIL, GENERALE.");
        return;
    }
    if (!specialiteRegex.test(specialite)) {
        alert("La spécialité doit être parmi : Électronique, Chimie, Informatique.");
        return;
    }
    if (!moduleRegex.test(module)) {
        alert("Le module doit être parmi les valeurs valides spécifiées.");
        return;
    }
    if (!titre || titre.length < 3) {
        alert("Le titre doit comporter au moins 3 caractères.");
        return;
    }
    if (!auteur) {
        alert("L'auteur est obligatoire.");
        return;
    }
    if (!editionRegex.test(edition)) {
        alert("L'édition doit être une année valide (entre 1965 et 2024).");
        return;
    }
    if (!exemplaireRegex.test(exemplaires)) {
        alert("Le nombre d'exemplaires doit être entre 1 et 50.");
        return;
    }

    const tableBody = document.querySelector("#book-list tbody");

    if (event.submitter.classList.contains("btn-update")) {
        // Mise à jour d'une ligne existante
        const rowToUpdate = document.querySelector("#book-list tbody tr.selected");
        if (rowToUpdate) {
            rowToUpdate.innerHTML = `
                <td>${year}</td>
                <td>${filiere}</td>
                <td>${specialite}</td>
                <td>${module}</td>
                <td>${titre}</td>
                <td>${auteur}</td>
                <td>${edition}</td>
                <td>${exemplaires}</td>
                <td>
                    <button class="btn-edit">Modifier</button>
                    <button class="btn-delete">Supprimer</button>
                </td>
            `;
            rowToUpdate.classList.remove("selected");
            alert("Livre modifié avec succès.");
        }
    } else {
        // Ajout d'une nouvelle ligne
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td>${year}</td>
            <td>${filiere}</td>
            <td>${specialite}</td>
            <td>${module}</td>
            <td>${titre}</td>
            <td>${auteur}</td>
            <td>${edition}</td>
            <td>${exemplaires}</td>
            <td>
                <button class="btn-edit">Modifier</button>
                <button class="btn-delete">Supprimer</button>
            </td>
        `;
        tableBody.appendChild(newRow);
        alert("Livre ajouté avec succès.");
    }

    // Réinitialisation du formulaire
    document.getElementById("addBookForm").reset();
    const submitButton = document.querySelector("#addBookForm button[type='submit']");
    submitButton.textContent = "Ajouter";
    submitButton.classList.remove("btn-update");
    
    // Ré-attacher les événements après modification ou ajout
    addEventListeners();
};

// Fonction pour supprimer un livre
function confirmDelete(event) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce livre ?")) {
        event.target.closest("tr").remove();
        alert("Livre supprimé avec succès.");
    }
}

// Fonction pour modifier un livre
function handleModification(event) {
    const row = event.target.closest("tr");

    // Assurez-vous qu'aucune autre ligne n'est sélectionnée
    document.querySelectorAll("#book-list tbody tr").forEach(r => r.classList.remove("selected"));

    row.classList.add("selected");

    // Remplissage du formulaire avec les données existantes
    document.querySelector('select#year').value = row.cells[0].textContent;
    document.querySelector('select#filiere').value = row.cells[1].textContent;
    document.querySelector('select#specialite').value = row.cells[2].textContent;
    document.querySelector('select#module').value = row.cells[3].textContent;
    document.querySelector('input#titre').value = row.cells[4].textContent;
    document.querySelector('input#auteur').value = row.cells[5].textContent;
    document.querySelector('input#edition').value = row.cells[6].textContent;
    document.querySelector('input#exemplaires').value = row.cells[7].textContent;

    // Modification du bouton de soumission pour "Mettre à jour"
    const submitButton = document.querySelector("#addBookForm button[type='submit']");
    submitButton.textContent = "Mettre à jour";
    submitButton.classList.add("btn-update");
}

// Fonction pour ajouter des écouteurs d'événements
function addEventListeners() {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.removeEventListener('click', confirmDelete);
        button.addEventListener('click', confirmDelete);
    });

    document.querySelectorAll('.btn-edit').forEach(button => {
        button.removeEventListener('click', handleModification);
        button.addEventListener('click', handleModification);
    });
}

// Ajout des écouteurs au chargement initial
addEventListeners();

// Gestion de la recherche dans la Liste des Livres
document.getElementById("searchInput").addEventListener("input", function(event) {
    const searchValue = event.target.value.toLowerCase();
    const rows = document.querySelectorAll(".book-table tbody tr");

    rows.forEach(row => {
        // Extraire les valeurs de chaque colonne de la ligne
        const annee = row.cells[0].textContent.toLowerCase();
        const filiere = row.cells[1].textContent.toLowerCase();
        const specialite = row.cells[2].textContent.toLowerCase();
        const module = row.cells[3].textContent.toLowerCase();
        const titre = row.cells[4].textContent.toLowerCase();
        const auteur = row.cells[5].textContent.toLowerCase();
        const edition = row.cells[6].textContent.toLowerCase();
        const exemplaire = row.cells[7].textContent.toLowerCase();

        // Vérifier si l'une des valeurs des cellules contient la valeur recherchée
        const matches = annee.includes(searchValue) ||
                        filiere.includes(searchValue) ||
                        specialite.includes(searchValue) ||
                        module.includes(searchValue) ||
                        titre.includes(searchValue) ||
                        auteur.includes(searchValue) ||
                        edition.includes(searchValue) ||
                        exemplaire.includes(searchValue);

        // Afficher ou masquer la ligne en fonction de la correspondance
        row.style.display = matches ? "" : "none";
    });
});
