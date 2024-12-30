<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
if (!isset($_SESSION['email'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

// Connexion à la base de données
include 'db.php'; // Assurez-vous que ce fichier contient votre connexion PDO ou MySQLi

try {
    $pdo = new PDO("mysql:host=localhost;dbname=projetweb", "root", ""); // Ajustez les informations
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Total des livres
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM ouvrage");
    $response['totalBooks'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Livres disponibles
    $stmt = $pdo->query("SELECT COUNT(*) AS available FROM exemplaire WHERE EtatExemplaire = 'disponible'");
    $response['availableBooks'] = $stmt->fetch(PDO::FETCH_ASSOC)['available'];

    // Livres réservés
    $stmt = $pdo->query("SELECT COUNT(*) AS reserved FROM livres WHERE statut != 'disponible'");
    $response['reservedBooks'] = $stmt->fetch(PDO::FETCH_ASSOC)['reserved'];

    // Utilisateurs inscrits
    $stmt = $pdo->query("SELECT COUNT(*) AS users FROM etudiant");
    $response['registeredUsers'] = $stmt->fetch(PDO::FETCH_ASSOC)['users'];
// Préparer la réponse en JSON
$response = [
    'totalBooks' => $totalBooks,
    'availableBooks' => $availableBooks,
    'reservedBooks' => $reservedBooks,
    'registeredUsers' => $registeredUsers
];
    // Envoyer la réponse au format JSON
    header("Content-Type: application/json");
    echo json_encode($response);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => $e->getMessage()]);
}
?>
