<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: Login.htm");
    exit();
}

// Connexion à la base de données
$servername = "localhost"; // Remplacez par votre hôte
$username = "root";        // Remplacez par votre utilisateur
$password = "";            // Remplacez par votre mot de passe
$dbname = "projetweb";     // Nom de votre base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les informations de l'utilisateur connecté
$email = $_SESSION['email'];
$sql = "SELECT Nom, Prenom, email, EtatEtudiant FROM Etudiant WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si l'utilisateur existe
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Aucun utilisateur trouvé.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Bibliothèque ESST</title>
    <link rel="stylesheet" href="../css/StyleUser.css"> 
    <link rel="stylesheet" href="../css/user_profile.css">
    <style>
.btn {
    display: inline-block;
    background-color: #4CAF50; /* Couleur de fond */
    color: white; /* Couleur du texte */
    padding: 10px 20px; /* Espacement interne */
    font-size: 16px; /* Taille du texte */
    font-weight: bold; /* Texte en gras */
    border: none; /* Pas de bordure */
    border-radius: 5px; /* Coins arrondis */
    cursor: pointer; /* Curseur pointeur */
    justify-content: center; /* Centre horizontalement */
    align-items: center; /* Centre verticalement (si nécessaire) */
    transition: background-color 0.3s, transform 0.2s; /* Effet de transition */
}

.btn:hover {
    background-color: #45a049; /* Couleur de fond au survol */
    transform: scale(1.05); /* Agrandissement léger au survol */
}

.btn:active {
    transform: scale(0.95); /* Réduction légère lors du clic */
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
                <li><a href="user.php" >Dashboard</a></li>
                <li><a href="SearchBooks.php">Rechercher des Livres</a></li>
                <li><a href="Profile.php" class="active">Mon Profil</a></li>
                <li><a href="../php/logout.php">Deconnexion</a></li>
            </ul>
        </nav> 
    </aside>

    <!-- Main Profile Content -->
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1 style="text-align: center;">Mon Profil</h1>
            <p style="text-align: center;">Consultez les informations de votre compte.</p>
        </header>

        <!-- Profile Details Section -->
        <section id="profile-details">
    <div class="profile-card">
        <h2>Modifier mes informations personnelles</h2>
        <form action="../php/update_profile.php" method="post" class="profile-form">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['Nom']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['Prenom']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="old_password">Ancien mot de passe:</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le nouveau mot de passe:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="etat">État:</label>
                <input type="text" id="etat" name="etat" value="<?php echo htmlspecialchars($user['EtatEtudiant']); ?>" readonly>
            </div>
            <div class="form-group">
    <button type="submit" class="btn">Modifier le mot de passe</button>
</div>

        </form>
    </div>
</section>

    </main> 
    <script src="../js/profile.js"></script>
</body>
</html>
