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

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    // Liste les livres avec les auteurs et la localisation
    $stmt = $pdo->query("SELECT 
        o.CodeOuvrage, 
        o.Titre, 
        o.DateAcquisition, 
        o.DateEdition, 
        e.AnneeEdition, 
        c.NomCategorie, 
        a.NomAuteur, 
        l.NomLocalisation 
    FROM Ouvrage o
    JOIN Edition e ON o.NumEdition = e.NumEdition
    JOIN Categorie c ON o.NumCategorie = c.NumCategorie
    JOIN ecrit ec ON o.CodeOuvrage = ec.CodeOuvrage
    JOIN auteur a ON ec.IDAuteur = a.IDAuteur
    LEFT JOIN Exemplaire ex ON o.CodeOuvrage = ex.CodeOuvrage
    LEFT JOIN localisation l ON ex.IDLocalisation = l.IDLocalisation
    GROUP BY o.CodeOuvrage"); // Utilisation de GROUP BY pour éviter les doublons
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
}

 elseif ($action === 'add') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['Titre'], $data['DateAcquisition'], $data['DateEdition'], $data['NumEdition'], $data['NumCategorie'], $data['Auteur'], $data['numExemplaires'], $data['Localisation'])) {
        echo json_encode(['success' => false, 'error' => 'Données Manquantes']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Insérer l'ouvrage
        $stmt = $pdo->prepare("INSERT INTO Ouvrage (Titre, DateAcquisition, DateEdition, NumEdition, NumCategorie) 
                               VALUES (:Titre, :DateAcquisition, :DateEdition, :NumEdition, :NumCategorie)");
        $stmt->execute([
            'Titre' => $data['Titre'],
            'DateAcquisition' => $data['DateAcquisition'],
            'DateEdition' => $data['DateEdition'],
            'NumEdition' => $data['NumEdition'],
            'NumCategorie' => $data['NumCategorie']
        ]);

        $ouvrageId = $pdo->lastInsertId();

        // Associer l'auteur
        $stmt = $pdo->prepare("INSERT INTO ecrit (CodeOuvrage, IDAuteur) VALUES (:CodeOuvrage, :IDAuteur)");
        $stmt->execute([
            'CodeOuvrage' => $ouvrageId,
            'IDAuteur' => $data['Auteur']
        ]);

        // Ajouter les exemplaires
        for ($i = 0; $i < $data['numExemplaires']; $i++) {
            $stmt = $pdo->prepare("INSERT INTO Exemplaire (EtatExemplaire, IDLocalisation, CodeOuvrage) 
                                   VALUES ('disponible', :IDLocalisation, :CodeOuvrage)");
            $stmt->execute([
                'IDLocalisation' => $data['Localisation'],
                'CodeOuvrage' => $ouvrageId
            ]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
 elseif ($action === 'delete') {
// Supprime un livre
$codeOuvrage = $_GET['CodeOuvrage'] ?? 0;

try {
    $pdo->beginTransaction();

    // Supprimer les entrées associées dans la table Emprunter
    $stmt = $pdo->prepare("
        DELETE e
        FROM Emprunter e
        JOIN Exemplaire ex ON e.IDExemplaire = ex.IDExemplaire
        WHERE ex.CodeOuvrage = ?
    ");
    $stmt->execute([$codeOuvrage]);

    // Supprimer les entrées associées dans la table Exemplaire
    $stmt = $pdo->prepare("DELETE FROM Exemplaire WHERE CodeOuvrage = ?");
    $stmt->execute([$codeOuvrage]);

    // Supprimer les entrées associées dans la table Ecrit
    $stmt = $pdo->prepare("DELETE FROM Ecrit WHERE CodeOuvrage = ?");
    $stmt->execute([$codeOuvrage]);

    // Supprimer le livre de la table Ouvrage
    $stmt = $pdo->prepare("DELETE FROM Ouvrage WHERE CodeOuvrage = ?");
    $stmt->execute([$codeOuvrage]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

}
elseif ($action === 'update') {
    // Mettre à jour un livre
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['Titre'], $data['DateAcquisition'], $data['DateEdition'], $data['NumEdition'], $data['NumCategorie'], $data['Auteur'], $data['CodeOuvrage'], $data['Localisation'])) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Mettre à jour la table Ouvrage
        $stmt = $pdo->prepare("UPDATE Ouvrage 
                               SET Titre = :Titre, 
                                   DateAcquisition = :DateAcquisition, 
                                   DateEdition = :DateEdition, 
                                   NumEdition = :NumEdition, 
                                   NumCategorie = :NumCategorie
                               WHERE CodeOuvrage = :CodeOuvrage");
        $stmt->execute([
            'Titre' => $data['Titre'],
            'DateAcquisition' => $data['DateAcquisition'],
            'DateEdition' => $data['DateEdition'],
            'NumEdition' => $data['NumEdition'],
            'NumCategorie' => $data['NumCategorie'],
            'CodeOuvrage' => $data['CodeOuvrage']
        ]);

        // Mettre à jour la table ecrit (association avec l'auteur)
        $stmt = $pdo->prepare("UPDATE ecrit 
                               SET IDAuteur = :IDAuteur 
                               WHERE CodeOuvrage = :CodeOuvrage");
        $stmt->execute([
            'IDAuteur' => $data['Auteur'],
            'CodeOuvrage' => $data['CodeOuvrage']
        ]);

        // Mettre à jour la table Exemplaire (localisation)
        $stmt = $pdo->prepare("UPDATE Exemplaire 
                               SET IDLocalisation = :IDLocalisation 
                               WHERE CodeOuvrage = :CodeOuvrage");
        $stmt->execute([
            'IDLocalisation' => $data['Localisation'],
            'CodeOuvrage' => $data['CodeOuvrage']
        ]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

 elseif ($action === 'get_editions') {
    // Charge les éditions dynamiquement
    $stmt = $pdo->query("SELECT NumEdition, AnneeEdition FROM Edition");
    $editions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($editions);

} elseif ($action === 'get_categories') {
    // Charge les catégories dynamiquement
    $stmt = $pdo->query("SELECT NumCategorie, NomCategorie FROM Categorie");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);

} elseif ($action === 'get_auteurs') {
    // Charge les catégories dynamiquement
    $stmt = $pdo->query("SELECT IDAuteur, NomAuteur FROM auteur");
    $auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($auteurs);

}  elseif ($action === 'get_localisations') {
    $stmt = $pdo->query("SELECT IDLocalisation, NomLocalisation FROM localisation");
    $localisations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($localisations);

} elseif ($action === 'get_book') {
    // Récupérer les détails d'un livre
    $codeOuvrage = $_GET['CodeOuvrage'] ?? 0;

    $stmt = $pdo->prepare("SELECT 
        o.CodeOuvrage, 
        o.Titre, 
        o.DateAcquisition, 
        o.DateEdition, 
        o.NumEdition, 
        o.NumCategorie, 
        ec.IDAuteur
    FROM Ouvrage o
    JOIN ecrit ec ON o.CodeOuvrage = ec.CodeOuvrage
    WHERE o.CodeOuvrage = ?");
    $stmt->execute([$codeOuvrage]);

    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($book);
}
else {
    echo json_encode(['error' => 'Invalid action']);
}
