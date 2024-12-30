document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll(".book-table tbody tr");
    const borrowedBooks = rows.length; // Nombre total de livres empruntés
    const reservedBooks = 2; // Extrait statiquement ou d'une source
    const returnedBooks = 12; // Extrait statiquement ou d'une source

    document.querySelector("#nbrLivreEmprunte p").textContent = borrowedBooks;
    document.querySelector("#nbrLivreReserve p").textContent = reservedBooks;
    document.querySelector("#nbrLivreRendu p").textContent = returnedBooks;
});

document.getElementById("searchInput").addEventListener("input", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll(".book-table tbody tr");

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

document.getElementById("exportBtn").addEventListener("click", function () {
    const rows = document.querySelectorAll(".book-table tr");
    let csvContent = "";

    rows.forEach(row => {
        const cells = Array.from(row.cells).map(cell => cell.textContent.trim());
        csvContent += cells.join(",") + "\\n";
    });

    const blob = new Blob([csvContent], { type: "text/csv" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "user_books.csv";
    link.click();
});

document.addEventListener("DOMContentLoaded", function () {
    const rowsPerPage = 5;
    const rows = Array.from(document.querySelectorAll(".book-table tbody tr"));
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    const pagination = document.createElement("div");
    pagination.id = "pagination";
    pagination.style.textAlign = "center";
    pagination.style.marginTop = "20px";

    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement("button");
        button.textContent = i;
        button.style.margin = "0 5px";
        button.style.padding = "5px 10px";
        button.style.border = "1px solid #ccc";
        button.style.cursor = "pointer";
        button.addEventListener("click", () => showPage(i));
        pagination.appendChild(button);
    }

    document.querySelector("#user-books").appendChild(pagination);

    function showPage(page) {
        rows.forEach((row, index) => {
            row.style.display = (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) ? "" : "none";
        });
    }

    showPage(1); // Afficher la première page par défaut
});

const ctx = document.getElementById("booksChart").getContext("2d");
new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["Empruntés", "Réservés", "Rendus"],
        datasets: [{
            label: "Statistiques des livres",
            data: [3, 2, 12], // Dynamisez ces données si possible
            backgroundColor: ["#4caf50", "#ff9800", "#2196f3"]
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

document.querySelectorAll(".book-table th").forEach((header, index) => {
    header.addEventListener("click", () => {
        const rows = Array.from(document.querySelectorAll(".book-table tbody tr"));
        const sortedRows = rows.sort((a, b) => {
            const aText = a.cells[index].textContent.trim();
            const bText = b.cells[index].textContent.trim();
            return aText.localeCompare(bText);
        });
        const tbody = document.querySelector(".book-table tbody");
        tbody.innerHTML = "";
        sortedRows.forEach(row => tbody.appendChild(row));
    });
});
