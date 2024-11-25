// ManageUsers.js
// JavaScript for ManageUsers.htm page

function confirmAction(action, row) {
    if (action === 'accept') {
        if (confirm("Voulez-vous accepter cet utilisateur ?")) {
            row.cells[4].textContent = "Accepté"; // Mise à jour du statut à "Accepté"
            row.querySelector('.btn-accept').disabled = true;
            row.querySelector('.btn-reject').disabled = true;
            alert("Utilisateur accepté avec succès.");
        }
    } else if (action === 'reject') {
        if (confirm("Voulez-vous rejeter cet utilisateur ?")) {
            row.cells[4].textContent = "Rejeté"; // Mise à jour du statut à "Rejeté"
            row.querySelector('.btn-accept').disabled = true;
            row.querySelector('.btn-reject').disabled = true;
            alert("Utilisateur rejeté avec succès.");
        }
    }
}

document.querySelectorAll('.btn-accept').forEach(button => {
    button.addEventListener('click', function() {
        const row = button.closest('tr');
        confirmAction('accept', row);
    });
});

document.querySelectorAll('.btn-reject').forEach(button => {
    button.addEventListener('click', function() {
        const row = button.closest('tr');
        confirmAction('reject', row);
    });
});
