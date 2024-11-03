<?php
// Connexion à la base de données
// Remplacez les informations suivantes par vos paramètres de base de données
$serveur = "localhost";
$utilisateur = "root";
$motDePasse = "";
$nomBaseDeDonnees = "votre_base_de_donnees";

$connexion = new mysqli($serveur, $utilisateur, $motDePasse, $nomBaseDeDonnees);

if ($connexion->connect_error) {
    die("Échec de la connexion : " . $connexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $motDePasse = $_POST['password'];
    $role = $_POST['role'];

    // Requête pour vérifier les informations de connexion et le rôle
    $requete = "SELECT * FROM utilisateurs WHERE email = ? AND password = ? AND role = ?";
    $stmt = $connexion->prepare($requete);
    $stmt->bind_param("sss", $email, $motDePasse, $role);
    $stmt->execute();
    $resultat = $stmt->get_result();

    if ($resultat->num_rows > 0) {
        // L'utilisateur est authentifié
        if ($role === "admin") {
            // Rediriger l'administrateur vers le tableau de bord admin
            header("Location: tableau_de_bord_admin.php");
        } else {
            // Rediriger l'utilisateur vers sa page d'accueil
            header("Location: accueil_utilisateur.php");
        }
    } else {
        // Échec de la connexion
        echo "<p style='color: red;'>Identifiants ou rôle invalide.</p>";
    }
    $stmt->close();
}
$connexion->close();
?>
