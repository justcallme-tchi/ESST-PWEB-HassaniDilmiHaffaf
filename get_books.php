<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'projetWeb'; // Remplacez par le nom de votre base de données
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Connection failed: ' . $e->getMessage()]));
}

// Initialiser les critères de recherche
$criteria = [];
$params = [];

// Vérifier si des critères de recherche sont envoyés
if (!empty($_GET['title'])) {
    $criteria[] = "o.Titre LIKE :title";
    $params[':title'] = '%' . $_GET['title'] . '%';
}
if (!empty($_GET['author'])) {
    $criteria[] = "a.NomAuteur LIKE :author";
    $params[':author'] = '%' . $_GET['author'] . '%';
}
if (!empty($_GET['searchYear'])) {
    $criteria[] = "e.AnneeEdition LIKE :searchYear";
    $params[':searchYear'] = '%' . $_GET['searchYear'] . '%';
}
if (!empty($_GET['availability'])) {
    if ($_GET['availability'] === 'Disponible') {
        $criteria[] = "COUNT(CASE WHEN ex.EtatExemplaire = 'Disponible' THEN 1 END) > 0";
    }
}

// Construire la requête SQL avec les critères
$sql = "
    SELECT 
        o.CodeOuvrage, 
        o.Titre, 
        o.DateAcquisition, 
        o.DateEdition, 
        e.AnneeEdition, 
        c.NomCategorie, 
        a.NomAuteur, 
        l.NomLocalisation, 
        COUNT(CASE WHEN ex.EtatExemplaire = 'Disponible' THEN 1 END) AS ExemplairesDisponibles
    FROM Ouvrage o
    JOIN Edition e ON o.NumEdition = e.NumEdition
    JOIN Categorie c ON o.NumCategorie = c.NumCategorie
    JOIN ecrit ec ON o.CodeOuvrage = ec.CodeOuvrage
    JOIN auteur a ON ec.IDAuteur = a.IDAuteur
    LEFT JOIN Exemplaire ex ON o.CodeOuvrage = ex.CodeOuvrage
    LEFT JOIN localisation l ON ex.IDLocalisation = l.IDLocalisation
";

// Ajouter les critères de recherche à la requête SQL
if (!empty($criteria)) {
    $sql .= " WHERE " . implode(' AND ', $criteria);
}

$sql .= " GROUP BY o.CodeOuvrage, o.Titre, o.DateAcquisition, o.DateEdition, e.AnneeEdition, c.NomCategorie, a.NomAuteur, l.NomLocalisation;";

// Préparer et exécuter la requête
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Récupérer les résultats
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($books);
