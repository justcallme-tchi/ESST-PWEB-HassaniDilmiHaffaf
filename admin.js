// admin.js
// JavaScript pour la page admin.htm

// Simuler des données dynamiques (par exemple, ces données peuvent être récupérées à partir d'une API)
const dynamicData = {
    totalBooks: 300,
    availableBooks: 120,
    reservedBooks: 60,
    registeredUsers: 250,
    monthlyData: {
        availableBooks: [20, 40, 60, 80, 100, 120],
        reservations: [10, 20, 30, 40, 50, 60],
        newUsers: [5, 10, 15, 20, 25, 30]
    }
};

// Mise à jour des valeurs des cartes avec des données dynamiques
document.getElementById("totalBooksCount").textContent = dynamicData.totalBooks;
document.getElementById("availableBooksCount").textContent = dynamicData.availableBooks;
document.getElementById("reservedBooksCount").textContent = dynamicData.reservedBooks;
document.getElementById("registeredUsersCount").textContent = dynamicData.registeredUsers;

// Initialisation du graphique des Livres Disponibles avec des données dynamiques
const availableBooksCtx = document.getElementById('availableBooksChart').getContext('2d');
new Chart(availableBooksCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Livres Disponibles',
            data: dynamicData.monthlyData.availableBooks,
            backgroundColor: '#4c51bf',
            borderColor: '#5a67d8',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Initialisation du graphique des Réservations avec des données dynamiques
const reservationsCtx = document.getElementById('reservationsChart').getContext('2d');
new Chart(reservationsCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Réservations',
            data: dynamicData.monthlyData.reservations,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Initialisation du graphique des Nouveaux Utilisateurs avec des données dynamiques
const newUsersCtx = document.getElementById('newUsersChart').getContext('2d');
new Chart(newUsersCtx, {
    type: 'doughnut',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Nouveaux Utilisateurs',
            data: dynamicData.monthlyData.newUsers,
            backgroundColor: ['#4c51bf', '#5a67d8', '#667eea', '#9f7aea', '#b794f4', '#d6bcfa']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Gestion des Notifications (simulé)
document.querySelector('.notification').addEventListener('click', () => {
    const notificationCountElement = document.querySelector('.notification-count');
    let count = Math.floor(Math.random() * 5); // Générer des notifications aléatoires
    notificationCountElement.textContent = count;

    if (count > 0) {
        alert(`Vous avez ${count} nouvelles notifications.`);
    } else {
        alert("Vous n'avez pas de nouvelles notifications.");
    }
});

// Gestion de la recherche dans les Réservations Récentes
document.querySelector('.recent-reservations .search-bar input').addEventListener('input', (event) => {
    const searchValue = event.target.value.toLowerCase();
    const rows = document.querySelectorAll('.reservation-table tbody tr');

    rows.forEach(row => {
        // Vérifier toutes les cellules de la ligne pour la valeur recherchée
        const cells = Array.from(row.cells);
        const matches = cells.some(cell => cell.textContent.toLowerCase().includes(searchValue));
        row.style.display = matches ? '' : 'none';
    });
});

// Fonction de délai (debounce) pour améliorer la performance de recherche
function debounce(func, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}

// Améliorer la recherche avec un délai
document.querySelector('.recent-reservations .search-bar input').addEventListener('input', debounce((event) => {
    const searchValue = event.target.value.toLowerCase();
    const rows = document.querySelectorAll('.reservation-table tbody tr');

    rows.forEach(row => {
        const cells = Array.from(row.cells);
        const matches = cells.some(cell => cell.textContent.toLowerCase().includes(searchValue));
        row.style.display = matches ? '' : 'none';
    });
}, 300));
