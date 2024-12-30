<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'projetWeb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Connection failed: ' . $e->getMessage()]));
}

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    // Liste des étudiants
    $stmt = $pdo->query("SELECT * FROM Etudiant");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);

} elseif ($action === 'add') {
    // Ajouter un étudiant
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['Nom'], $data['Prenom'], $data['DateNaissance'], $data['email'], $data['mot_de_passe'], $data['EtatEtudiant'])) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO Etudiant (Nom, Prenom, DateNaissance, email, mot_de_passe, EtatEtudiant) 
                               VALUES (:Nom, :Prenom, :DateNaissance, :email, :mot_de_passe, :EtatEtudiant)");
        $stmt->execute([
            'Nom' => $data['Nom'],
            'Prenom' => $data['Prenom'],
            'DateNaissance' => $data['DateNaissance'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_BCRYPT),
            'EtatEtudiant' => $data['EtatEtudiant'],
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

} elseif ($action === 'delete') {

$matriculeEtudiant = $_GET['MatriculeEtudiant'] ?? 0;

try {
    // Suppression des références dans la table Emprunter
    $stmt = $pdo->prepare("DELETE FROM Emprunter WHERE MatriculeEtudiant = ?");
    $stmt->execute([$matriculeEtudiant]);

    // Suppression de l'étudiant dans la table Etudiant
    $stmt = $pdo->prepare("DELETE FROM Etudiant WHERE MatriculeEtudiant = ?");
    $stmt->execute([$matriculeEtudiant]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


} elseif ($action === 'update') {
    // Mettre à jour un étudiant
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['MatriculeEtudiant'], $data['Nom'], $data['Prenom'], $data['DateNaissance'], $data['email'], $data['EtatEtudiant'])) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE Etudiant 
                               SET Nom = :Nom, 
                                   Prenom = :Prenom, 
                                   DateNaissance = :DateNaissance, 
                                   email = :email, 
                                   EtatEtudiant = :EtatEtudiant 
                               WHERE MatriculeEtudiant = :MatriculeEtudiant");
        $stmt->execute([
            'Nom' => $data['Nom'],
            'Prenom' => $data['Prenom'],
            'DateNaissance' => $data['DateNaissance'],
            'email' => $data['email'],
            'EtatEtudiant' => $data['EtatEtudiant'],
            'MatriculeEtudiant' => $data['MatriculeEtudiant'],
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

} elseif ($action === 'get_student') {
    // Récupérer les détails d'un étudiant
    $matriculeEtudiant = $_GET['MatriculeEtudiant'] ?? 0;

    $stmt = $pdo->prepare("SELECT * FROM Etudiant WHERE MatriculeEtudiant = ?");
    $stmt->execute([$matriculeEtudiant]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($student);

} else {
    echo json_encode(['error' => 'Action invalide']);
}
