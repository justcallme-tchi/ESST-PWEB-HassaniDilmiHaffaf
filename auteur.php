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
        $query = $pdo->query('SELECT * FROM Auteur');
        $auteurs = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($auteurs);
        break;

    case 'add':
        $nomAuteur = $_POST['nomAuteur'] ?? '';
        if ($nomAuteur) {
            $stmt = $pdo->prepare('INSERT INTO Auteur (NomAuteur) VALUES (:nomAuteur)');
            $stmt->execute([':nomAuteur' => $nomAuteur]);
            echo json_encode(['status' => 'success', 'message' => 'Auteur ajouté avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nom de l\'auteur est requis']);
        }
        break;

    case 'update':
        $idAuteur = $_POST['idAuteur'] ?? '';
        $nomAuteur = $_POST['nomAuteur'] ?? '';
        if ($idAuteur && $nomAuteur) {
            $stmt = $pdo->prepare('UPDATE Auteur SET NomAuteur = :nomAuteur WHERE IDAuteur = :idAuteur');
            $stmt->execute([':nomAuteur' => $nomAuteur, ':idAuteur' => $idAuteur]);
            echo json_encode(['status' => 'success', 'message' => 'Auteur modifié avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
        }
        break;

    case 'delete':
        $idAuteur = $_POST['idAuteur'] ?? '';
        if ($idAuteur) {
            // Supprimer les entrées associées dans la table ecrit
        $stmt = $pdo->prepare("DELETE FROM ecrit WHERE IDAuteur = :idAuteur");
        $stmt->execute([':idAuteur' => $idAuteur]);
            $stmt = $pdo->prepare('DELETE FROM Auteur WHERE IDAuteur = :idAuteur');
            $stmt->execute([':idAuteur' => $idAuteur]);
            echo json_encode(['status' => 'success', 'message' => 'Auteur supprimé avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de l\'auteur requis']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Action non valide']);
        break;
}
?>
