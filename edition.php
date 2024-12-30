<?php
// Connexion à la base de données
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

// Gestion des requêtes
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'fetch':
        $query = $pdo->query('SELECT * FROM edition');
        $Editions = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($Editions);
        break;

    case 'add':
        $AnneeEdition = $_POST['AnneeEdition'] ?? '';
        if ($AnneeEdition) {
            $stmt = $pdo->prepare('INSERT INTO Edition (AnneeEdition) VALUES (:AnneeEdition)');
            $stmt->execute([':AnneeEdition' => $AnneeEdition]);
            echo json_encode(['status' => 'success', 'message' => 'Edition ajouté avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nom de l\'Edition est requis']);
        }
        break;

    case 'update':
        $NumEdition = $_POST['NumEdition'] ?? '';
        $AnneeEdition = $_POST['AnneeEdition'] ?? '';
        if ($NumEdition && $AnneeEdition) {
            $stmt = $pdo->prepare('UPDATE Edition SET AnneeEdition = :AnneeEdition WHERE NumEdition = :NumEdition');
            $stmt->execute([':AnneeEdition' => $AnneeEdition, ':NumEdition' => $NumEdition]);
            echo json_encode(['status' => 'success', 'message' => 'Edition modifié avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Action non valide']);
        break;
}
?>
