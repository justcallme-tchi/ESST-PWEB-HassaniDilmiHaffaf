<?php
require_once "../php/db.php"; // Connexion à la base de données
if (!isset($_SESSION['email'])) {
    header("Location: Login.htm");
    exit();
}

// Récupérer le matricule de l'utilisateur connecté
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT MatriculeEtudiant FROM Etudiant WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $matriculeEtudiant = $user['MatriculeEtudiant'];
} else {
    echo "Utilisateur non trouvé.";
    exit();
}

// Supprimer un emprunt si une requête POST est reçue
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_emprunt'])) {
    $idExemplaire = $_POST['id_exemplaire'];
    $dateEmprunt = $_POST['date_emprunt'];
    
    $deleteStmt = $conn->prepare("DELETE FROM Emprunter WHERE MatriculeEtudiant = ? AND IDExemplaire = ? AND DateEmprunt = ?");
    $deleteStmt->bind_param('iis', $matriculeEtudiant, $idExemplaire, $dateEmprunt);
    
    if ($deleteStmt->execute()) {
        // Mettre à jour l'état de l'exemplaire à 'En cours'
        $sqlUpdate = "UPDATE Exemplaire SET EtatExemplaire = 'disponible' WHERE IDExemplaire = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $idExemplaire);
        $stmtUpdate->execute();

        echo json_encode(["message" => "emprunt annulé"]);
    } else {
        echo "<script>alert('Erreur lors de l\'annulation de l\'emprunt.');</script>";
    }
}

// Récupérer les livres empruntés par l'utilisateur connecté
$sql = "SELECT o.Titre, em.DateEmprunt, em.DateRestitutionPrevue, em.EtatEmprunt, e.IDExemplaire 
        FROM Emprunter em
        JOIN Exemplaire e ON em.IDExemplaire = e.IDExemplaire
        JOIN Ouvrage o ON e.CodeOuvrage = o.CodeOuvrage
        WHERE em.MatriculeEtudiant = ?";
$stmtBooks = $conn->prepare($sql);
$stmtBooks->bind_param('i', $matriculeEtudiant);
$stmtBooks->execute();
$resultBooks = $stmtBooks->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page - Bibliothèque ESST</title>
    <link rel="stylesheet" href="../css/StyleUser.css">
    <link rel="stylesheet" href="../css/user_books_table.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .cancel-button {
    background-color: #ff4c4c; /* Couleur rouge attrayante */
    color: #fff; /* Texte blanc */
    border: none; /* Pas de bordure */
    border-radius: 5px; /* Coins arrondis */
    padding: 8px 16px; /* Espacement interne */
    font-size: 14px; /* Taille de la police */
    cursor: pointer; /* Curseur pointer */
    transition: background-color 0.3s ease; /* Animation pour le survol */
}

.cancel-button:disabled {
    background-color: #cccccc; /* Couleur grise pour bouton désactivé */
    color: #666666; /* Texte grisé */
    cursor: not-allowed; /* Curseur non autorisé */
}

.cancel-button:hover:not(:disabled) {
    background-color: #ff0000; /* Rouge vif au survol si activé */
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
                <li><a href="user.php" class="active">Dashboard</a></li>
                <li><a href="SearchBooks.php">Rechercher des Livres</a></li>
                <li><a href="Profile.php">Mon Profil</a></li>
                <li><a href="../php/logout.php">Deconnexion</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main User Content -->
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1 style="text-align: center;">Bienvenue sur votre espace utilisateur</h1>
            <p id="nomUser" style="text-align: center;">Gérez vos emprunts et découvrez de nouveaux livres à la bibliothèque.</p>
        </header>

        <!-- User Information Section -->
        <section class="dashboard-cards">
            <div class="card" id="nbrLivreEmprunte">
                <h3>Livres emprunter et en Attente</h3>
                <p><?php echo $resultBooks->num_rows; ?></p>
            </div>
        </section>

        <!-- User Books Section -->
        <section id="user-books">
            <h2 style="text-align: center;">Mes Livres</h2>
            <table class="book-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date Emprunt</th>
                        <th>Date Restitution Prévue</th>
                        <th>État Emprunt</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
    <?php while ($row = $resultBooks->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['Titre']); ?></td>
            <td><?php echo htmlspecialchars($row['DateEmprunt']); ?></td>
            <td><?php echo htmlspecialchars($row['DateRestitutionPrevue']); ?></td>
            <td><?php echo htmlspecialchars($row['EtatEmprunt']); ?></td>
            <td>
    <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cet emprunt ?');">
        <input type="hidden" name="id_exemplaire" value="<?php echo $row['IDExemplaire']; ?>">
        <input type="hidden" name="date_emprunt" value="<?php echo $row['DateEmprunt']; ?>">
        <button 
            type="submit" 
            name="delete_emprunt" 
            class="cancel-button" 
            <?php echo $row['EtatEmprunt'] !== 'Attente' ? 'disabled' : ''; ?>
            style="<?php echo $row['EtatEmprunt'] !== 'Attente' ? 'cursor:not-allowed;' : ''; ?>"
        >
            Annuler
        </button>
    </form>
</td>

        </tr>
    <?php endwhile; ?>
</tbody>

            </table>
        </section>            
    </main>
    <script src="../js/user.js"></script>
</body>
</html>
