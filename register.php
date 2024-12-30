<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = 'admin';
    $nom = $_POST['nameI'];
    $email = $_POST['emailI'];
    $password = password_hash($_POST['passwordI'], PASSWORD_BCRYPT);

    if ($role == 'admin') {
        $sql = "INSERT INTO Administrateur (nom, prenom, email, mot_de_passe) VALUES ('$nom', '', '$email', '$password')";
    } elseif ($role == 'user') {
        $sql = "INSERT INTO Etudiant (Nom, Prenom, DateNaissance, email, mot_de_passe, EtatEtudiant) VALUES ('$nom', '', '2000-01-01', '$email', '$password', 'Inscrit')";
    }

    if ($conn->query($sql) === TRUE) {
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
        echo json_encode(array("status" => "success", "message" => "Inscription réussie"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Erreur d'inscription: " . $conn->error));
    }

    $conn->close();
}
?>
