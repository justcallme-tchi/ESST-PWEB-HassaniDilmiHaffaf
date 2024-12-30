<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: Login.htm");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projetweb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les données du formulaire
$email = $_SESSION['email'];
$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Vérifier si le nouveau mot de passe correspond à la confirmation
if ($new_password !== $confirm_password) {
    echo "Les nouveaux mots de passe ne correspondent pas.";
    exit();
}

// Récupérer l'ancien mot de passe de la base de données
$sql = "SELECT mot_de_passe FROM Etudiant WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Vérifier si l'ancien mot de passe est correct
    if (!password_verify($old_password, $user['mot_de_passe'])) {
        echo "L'ancien mot de passe est incorrect.";
        exit();
    }
} else {
    echo "Utilisateur introuvable.";
    exit();
}

// Hacher le nouveau mot de passe
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Mettre à jour le mot de passe dans la base de données
$sql = "UPDATE Etudiant SET mot_de_passe = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo "Mot de passe mis à jour avec succès.";
    header("Location: ../html/Profile.php");
} else {
    echo "Erreur lors de la mise à jour du mot de passe : " . $conn->error;
}

$stmt->close();
$conn->close();
?>
