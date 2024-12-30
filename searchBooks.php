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

// Requête SQL pour rechercher des ouvrages
$sql = "SELECT O.CodeOuvrage, O.Titre, O.DateAcquisition, O.DateEdition, E.AnneeEdition, 
               C.NomCategorie, A.NomAuteur, L.NomLocalisation
        FROM Ouvrage O
        LEFT JOIN Edition E ON O.NumEdition = E.NumEdition
        LEFT JOIN Categorie C ON O.NumCategorie = C.NumCategorie
        LEFT JOIN Ecrit Ec ON O.CodeOuvrage = Ec.CodeOuvrage
        LEFT JOIN Auteur A ON Ec.IDAuteur = A.IDAuteur
        LEFT JOIN Exemplaire Ex ON O.CodeOuvrage = Ex.CodeOuvrage
        LEFT JOIN localisation L ON Ex.IDLocalisation = L.IDLocalisation
        WHERE O.Titre LIKE ? OR C.NomCategorie LIKE ? OR A.NomAuteur LIKE ?";

// Préparation et exécution de la requête
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $query . '%';
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Convertir les résultats en JSON
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Retourner les résultats sous forme JSON
echo json_encode($books, JSON_UNESCAPED_UNICODE);

// Fermer la connexion
$stmt->close();
$conn->close();
?>
