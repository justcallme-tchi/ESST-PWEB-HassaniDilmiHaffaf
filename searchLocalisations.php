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

// Requête SQL pour rechercher des auteurs
$sql = "SELECT IDLocalisation, NomLocalisation
        FROM localisation
        WHERE NomLocalisation LIKE ?";

// Préparation et exécution de la requête
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $query . '%';
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Convertir les résultats en JSON
$authors = [];
while ($row = $result->fetch_assoc()) {
    $authors[] = $row;
}

// Retourner les résultats sous forme JSON
echo json_encode($authors, JSON_UNESCAPED_UNICODE);

// Fermer la connexion
$stmt->close();
$conn->close();
?>
