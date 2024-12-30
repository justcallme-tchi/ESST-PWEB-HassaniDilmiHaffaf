<?php
// Affiche les erreurs pour déboguer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// En-tête pour indiquer que le contenu est en JSON
header('Content-Type: application/json');

// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = ''; // Remplacez par votre mot de passe
$dbname = 'projetweb';
$conn = new mysqli($host, $user, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(["error" => "Erreur de connexion : " . $conn->connect_error]));
}

// Récupérer la requête de recherche
$query = isset($_POST['query']) ? $_POST['query'] : '';

// Requête SQL pour rechercher des étudiants
$sql = "SELECT MatriculeEtudiant, Nom, Prenom, DateNaissance, email, EtatEtudiant
        FROM Etudiant
        WHERE Nom LIKE ? OR Prenom LIKE ? OR email LIKE ? OR EtatEtudiant LIKE ?";

// Préparation et exécution de la requête
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $query . '%';
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Convertir les résultats en JSON
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Retourner les résultats sous forme JSON
echo json_encode($students, JSON_UNESCAPED_UNICODE);

// Fermer la connexion
$stmt->close();
$conn->close();
?>
