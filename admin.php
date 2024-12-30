<?php 
session_start(); 
if (!isset($_SESSION['email'])) { 
    header("Location: Login.htm"); 
    exit(); 
} 
// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = ''; // Remplacez par votre mot de passe
$dbname = 'projetweb';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bibliothèque ESST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="../css/style-admindashboard.css">
    <style>.card-link {
    text-decoration: none;
    color: inherit;
    display: block; /* Rend l'ensemble de la carte cliquable */
}

.card-link:hover .card {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Ajouter un effet de survol */
    transform: translateY(-2px);
    transition: transform 0.2s, box-shadow 0.2s;
}
</style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <img src="../img/ESST-logo-white-300x233.png" alt="ESST Logo">
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Page d'accueil</a></li>
                <li><a href="admin.php" class="active">Dashboard</a></li>
                <li><a href="AddBook.htm">Gérer les Livres</a></li>
                <li><a href="ManageUsers.htm">Gérer les Utilisateurs</a></li>
                <li><a href="../php/logout.php">Deconnexion</a></li>
                <li><a href="Auteur.html">Autre</a></li>
            </ul>
        </nav>
    </aside>
<!-- Main Dashboard Content -->
<main class="dashboard-content">
        <!-- Top Header with Welcome Text, Profile Picture, and Search Bar -->
        <header class="dashboard-header">
            <div class="header-left">
                <h1 style="text-align: center; padding-bottom: 1cm;">Admin Dashboard</h1>
                <p id="nomAdmin" style="text-align: center; padding-bottom: 1cm;">Bienvenue, Admin! Gérez les ressources de la bibliothèque facilement.</p>
            </div>
                <div class="notification">
                    <span class="bell-icon">&#128276;</span>
                    <span class="notification-count">0</span>
                </div>
                <div class="profile-pic">
                    <img src="../img/admin-profile.png" alt="Admin Profile">
                </div>
            </div>
        </header>

<?php
// Récupérer les données pour les cartes
$totalBooks = $conn->query("SELECT COUNT(*) as count FROM Ouvrage")->fetch_assoc()['count'];
$availableBooks = $conn->query("SELECT COUNT(*) as count FROM Exemplaire WHERE EtatExemplaire = 'Disponible'")->fetch_assoc()['count'];
$reservedBooks = $conn->query("SELECT COUNT(*) as count FROM Emprunter WHERE EtatEmprunt = 'Confirme'")->fetch_assoc()['count'];
$registeredUsers = $conn->query("SELECT COUNT(*) as count FROM Etudiant")->fetch_assoc()['count'];
?>

        <!-- Dashboard Cards Section -->
        <section class="dashboard-cards" style="padding-bottom: 1cm;">
    <a href="AddBook.htm" class="card-link">
        <div class="card" id="totalBooksCard">
            <div class="card-content">
                <h3>Total Livres</h3>
                <p id="totalBooksCount"><?php echo $totalBooks; ?></p>
            </div>
            <div class="card-icon">&#128214;</div>
        </div>
    </a>

        <div class="card" id="availableBooksCard">
            <div class="card-content">
                <h3>Livres Disponibles</h3>
                <p id="availableBooksCount"><?php echo $availableBooks; ?></p>
            </div>
            <div class="card-icon">&#128218;</div>
        </div>

        <div class="card" id="reservedBooksCard">
            <div class="card-content">
                <h3>Livres Réservés</h3>
                <p id="reservedBooksCount"><?php echo $reservedBooks; ?></p>
            </div>
            <div class="card-icon">&#128210;</div>
        </div>
    <a href="ManageUsers.htm" class="card-link">
        <div class="card" id="registeredUsersCard">
            <div class="card-content">
                <h3>Utilisateurs Inscrits</h3>
                <p id="registeredUsersCount"><?php echo $registeredUsers; ?></p>
            </div>
            <div class="card-icon">&#128100;</div>
        </div>
    </a>
</section>



        <section class="recent-reservations" style="padding-bottom: 1cm;">
            <h2>Réservations</h2>
            <table class="reservation-table">
                <thead>
                    <tr>
                        <th>Titre du Livre</th>
                        <th>Nom de l'Utilisateur</th>
                        <th>Date d'emprunt</th>
                        <th>Date de retour prevu</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT E.MatriculeEtudiant, E.IDExemplaire, O.Titre, E.DateEmprunt, E.DateRestitutionPrevue, E.EtatEmprunt, Et.Nom, Et.Prenom
                        FROM Emprunter E
                        JOIN Etudiant Et ON E.MatriculeEtudiant = Et.MatriculeEtudiant
                        JOIN Exemplaire Ex ON E.IDExemplaire = Ex.IDExemplaire
                        JOIN Ouvrage O ON Ex.CodeOuvrage = O.CodeOuvrage";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $actionButtons = '';
                        if ($row['EtatEmprunt'] === 'Attente') {
                            $actionButtons = "<button class='btn btn-success btn-sm confirm-btn' data-id='{$row['MatriculeEtudiant']}' data-idexemplaire='{$row['IDExemplaire']}' data-date='{$row['DateEmprunt']}'>
                                                <i class='fas fa-check'></i> Confirmer
                                              </button>
                                              <button class='btn btn-danger btn-sm cancel-btn' data-id='{$row['MatriculeEtudiant']}' data-idexemplaire='{$row['IDExemplaire']}' data-date='{$row['DateEmprunt']}'>
                                                <i class='fas fa-times'></i> Annuler
                                              </button>";
                        } elseif ($row['EtatEmprunt'] === 'Confirme') {
                            $actionButtons = "<button class='btn btn-primary btn-sm returned-btn' data-id='{$row['MatriculeEtudiant']}' data-idexemplaire='{$row['IDExemplaire']}' data-date='{$row['DateEmprunt']}'>
                                                <i class='fas fa-undo'></i> Marquer comme rendu
                                              </button>";
                        }
                        echo "<tr>
                                <td>{$row['Titre']}</td>
                                <td>{$row['Nom']} {$row['Prenom']}</td>
                                <td>{$row['DateEmprunt']}</td>
                                <td>{$row['DateRestitutionPrevue']}</td>
                                <td>{$row['EtatEmprunt']}</td>
                                <td>$actionButtons</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Aucune réservation trouvée.</td></tr>";
                }
                
                ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
       $(document).on('click', '.confirm-btn', function () {
    const matricule = $(this).data('id');
    const idExemplaire = $(this).data('idexemplaire');
    const date = $(this).data('date');
    $.post('../php/admin_actions.php', { action: 'confirm', matricule, idExemplaire, date }, function (response) {
        alert(response);
        location.reload();
    });
});


        $(document).on('click', '.cancel-btn', function () {
            const matricule = $(this).data('id');
            const idExemplaire = $(this).data('idexemplaire');
            const date = $(this).data('date');
            $.post('../php/admin_actions.php', { action: 'cancel', matricule, idExemplaire, date }, function (response) {
                alert(response);
                location.reload();
            });
        });

        $(document).on('click', '.returned-btn', function () {
            const matricule = $(this).data('id');
            const idExemplaire = $(this).data('idexemplaire');
            const date = $(this).data('date');
            $.post('../php/admin_actions.php', { action: 'returned', matricule, idExemplaire, date }, function (response) {
                alert(response);
                location.reload();
            });
        });
    </script>
</body>
</html>

