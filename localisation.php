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
        $query = $pdo->query('SELECT * FROM Localisation');
        $localisations = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($localisations);
        break;

    case 'add':
        $NomLocalisation = $_POST['NomLocalisation'] ?? '';
        if ($NomLocalisation) {
            $stmt = $pdo->prepare('INSERT INTO Localisation (NomLocalisation) VALUES (:NomLocalisation)');
            $stmt->execute([':NomLocalisation' => $NomLocalisation]);
            echo json_encode(['status' => 'success', 'message' => 'Localisation ajoutée avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nom de la localisation requis']);
        }
        break;

    case 'update':
        $IDLocalisation = $_POST['IDLocalisation'] ?? '';
        $NomLocalisation = $_POST['NomLocalisation'] ?? '';
        if ($IDLocalisation && $NomLocalisation) {
            $stmt = $pdo->prepare('UPDATE Localisation SET NomLocalisation = :NomLocalisation WHERE IDLocalisation = :IDLocalisation');
            $stmt->execute([':NomLocalisation' => $NomLocalisation, ':IDLocalisation' => $IDLocalisation]);
            echo json_encode(['status' => 'success', 'message' => 'Localisation modifiée avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Action non valide']);
        break;
}
?>
