<?php
$dsn = 'mysql:host=localhost;dbname=projetweb;charset=utf8';
$username = 'root';
$password = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'fetch':
        $query = $pdo->query('SELECT * FROM Categorie');
        $categories = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories);
        break;

    case 'add':
        $NomCategorie = $_POST['NomCategorie'] ?? '';
        if ($NomCategorie) {
            $stmt = $pdo->prepare('INSERT INTO Categorie (NomCategorie) VALUES (:NomCategorie)');
            $stmt->execute([':NomCategorie' => $NomCategorie]);
            echo json_encode(['status' => 'success', 'message' => 'Catégorie ajoutée avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nom de la catégorie requis']);
        }
        break;

    case 'update':
        $NumCategorie = $_POST['NumCategorie'] ?? '';
        $NomCategorie = $_POST['NomCategorie'] ?? '';
        if ($NumCategorie && $NomCategorie) {
            $stmt = $pdo->prepare('UPDATE Categorie SET NomCategorie = :NomCategorie WHERE NumCategorie = :NumCategorie');
            $stmt->execute([':NomCategorie' => $NomCategorie, ':NumCategorie' => $NumCategorie]);
            echo json_encode(['status' => 'success', 'message' => 'Catégorie modifiée avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Action non valide']);
        break;
}
?>
