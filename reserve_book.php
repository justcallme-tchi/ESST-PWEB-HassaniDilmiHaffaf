<?php
// Inclure la connexion à la base de données et la session utilisateur
require_once "db.php";

if (!isset($_SESSION['email'])) {
    echo $_SESSION['email'];
    echo json_encode(["message" => "Utilisateur non connecté."]);
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projetweb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer le matricule de l'étudiant connecté
$sqlEtudiant = "SELECT MatriculeEtudiant FROM Etudiant WHERE email = ?";
$stmtEtudiant = $conn->prepare($sqlEtudiant);
$stmtEtudiant->bind_param("s", $_SESSION['email']);
$stmtEtudiant->execute();
$resultEtudiant = $stmtEtudiant->get_result();

if ($resultEtudiant->num_rows > 0) {
    $etudiant = $resultEtudiant->fetch_assoc();
    $matriculeEtudiant = $etudiant['MatriculeEtudiant'];
} else {
    echo json_encode(["message" => "Étudiant non trouvé."]);
    exit();
}

// Récupérer les données envoyées depuis le frontend
$data = json_decode(file_get_contents("php://input"), true);
$codeOuvrage = $data['codeOuvrage'];
$dateEmprunt = date("Y-m-d");

// Trouver un exemplaire disponible
$sqlExemplaire = "SELECT IDExemplaire 
                  FROM Exemplaire 
                  WHERE CodeOuvrage = ? AND EtatExemplaire = 'Disponible' LIMIT 1";
$stmt = $conn->prepare($sqlExemplaire);
$stmt->bind_param("i", $codeOuvrage);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $exemplaire = $result->fetch_assoc();
    $idExemplaire = $exemplaire['IDExemplaire'];

    // Vérifiez si la date existe dans la table référencée
$sqlCheckDate = "SELECT * FROM `date` WHERE DateEmprunt = ?";
$stmtCheckDate = $conn->prepare($sqlCheckDate);
$stmtCheckDate->bind_param("s", $dateEmprunt);
$stmtCheckDate->execute();
$resultDate = $stmtCheckDate->get_result();

if ($resultDate->num_rows === 0) {
    $sqlCheckDate = "INSERT INTO date (DateEmprunt) VALUES (?)";
$stmtCheckDate = $conn->prepare($sqlCheckDate);

if (!$stmtCheckDate) {
    die("Erreur de préparation de la requête : " . $conn->error);
}

// Lier le paramètre de la requête
$stmtCheckDate->bind_param("s", $dateEmprunt);

// Exécuter la requête
if ($stmtCheckDate->execute()) {
    echo "La date a été insérée avec succès.";
} else {
    echo "Erreur lors de l'insertion de la date : " . $stmtCheckDate->error;
}

// Fermer la déclaration
$stmtCheckDate->close();

}

    // Insérer dans la table Emprunter
    $sqlInsert = "INSERT INTO Emprunter (MatriculeEtudiant, IDExemplaire, EtatEmprunt, DateEmprunt, DateRestitutionPrevue) 
                  VALUES (?, ?, 'Attente', ?, DATE_ADD(?, INTERVAL 1 MONTH))";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("iiss", $matriculeEtudiant, $idExemplaire, $dateEmprunt, $dateEmprunt);

    if ($stmtInsert->execute()) {
        // Mettre à jour l'état de l'exemplaire à 'En cours'
        $sqlUpdate = "UPDATE Exemplaire SET EtatExemplaire = 'non disponible' WHERE IDExemplaire = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $idExemplaire);
        $stmtUpdate->execute();

        echo json_encode(["message" => "Réservation réussie !"]);
    } else {
        echo json_encode(["message" => "Erreur lors de la réservation."]);
    }
} else {
    echo json_encode(["message" => "Aucun exemplaire disponible."]);
}

$conn->close();
?>
