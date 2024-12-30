<?php
// Activer les erreurs pour le développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Définir le type de contenu pour JSON
header('Content-Type: application/json');

// Inclure la connexion à la base de données
include 'db.php';

try {
    // Exécuter la requête SQL
    $sql = "SELECT * FROM etudiant";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Erreur SQL : " . $conn->error);
    }

    $users = [];

    // Parcourir les résultats et remplir le tableau
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    // Retourner les données en JSON
    echo json_encode($users, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    // Retourner une erreur en JSON
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    // Fermer la connexion à la base de données
    $conn->close();
}
