<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $dateNaissance = $_POST['dateNaissance'];
    $etatEtudiant = $_POST['etatEtudiant'];
    $password = password_hash('defaultpassword', PASSWORD_BCRYPT);  // Mot de passe par défaut

    $sql = "INSERT INTO Etudiant (Nom, Prenom, DateNaissance, email, mot_de_passe, EtatEtudiant) 
            VALUES ('$nom', '$prenom', '$dateNaissance', '$email', '$password', '$etatEtudiant')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("status" => "success", "message" => "Utilisateur ajouté avec succès"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Erreur lors de l'ajout de l'utilisateur : " . $conn->error));
    }

    $conn->close();
}
?>
