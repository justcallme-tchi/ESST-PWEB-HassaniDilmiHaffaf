<?php

include 'db.php'; // Inclure votre fichier de configuration pour la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $matricule = $_POST['matricule'] ?? '';
    $idExemplaire = $_POST['idExemplaire'] ?? '';
    $date = $_POST['date'] ?? '';

    // Validation des paramètres
    if (empty($action) || empty($matricule) || empty($idExemplaire) || empty($date)) {
        echo "Paramètres manquants.";
        exit;
    }

    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    switch ($action) {
        case 'confirm':
            // Confirmer un emprunt
            $stmt = $conn->prepare("UPDATE Emprunter SET EtatEmprunt = 'Confirme' WHERE MatriculeEtudiant = ? AND IDExemplaire = ? AND DateEmprunt = ?");
            $stmt->bind_param('sis', $matricule, $idExemplaire, $date);
            if ($stmt->execute()) {
                // Mettre à jour l'état de l'exemplaire
                $stmtUpdate = $conn->prepare("UPDATE Exemplaire SET EtatExemplaire = 'non disponible' WHERE IDExemplaire = ?");
                $stmtUpdate->bind_param('i', $idExemplaire);
                if ($stmtUpdate->execute()) {
                    echo "Emprunt confirmé avec succès et état de l'exemplaire mis à jour.";
                } else {
                    echo "Erreur lors de la mise à jour de l'état de l'exemplaire.";
                }
                $stmtUpdate->close();
            } else {
                echo "Erreur lors de la confirmation de l'emprunt.";
            }
            $stmt->close();
            break;
        

            case 'cancel':
                // Annuler un emprunt
                $stmt = $conn->prepare("DELETE FROM Emprunter WHERE MatriculeEtudiant = ? AND IDExemplaire = ? AND DateEmprunt = ?");
                $stmt->bind_param('sis', $matricule, $idExemplaire, $date);
                if ($stmt->execute()) {
                    // Mettre à jour l'état de l'exemplaire
                    $stmtUpdate = $conn->prepare("UPDATE Exemplaire SET EtatExemplaire = 'Disponible' WHERE IDExemplaire = ?");
                    $stmtUpdate->bind_param('i', $idExemplaire);
                    if ($stmtUpdate->execute()) {
                        echo "Emprunt annulé avec succès et exemplaire mis à jour.";
                    } else {
                        echo "Erreur lors de la mise à jour de l'état de l'exemplaire.";
                    }
                    $stmtUpdate->close();
                } else {
                    echo "Erreur lors de l'annulation de l'emprunt.";
                }
                $stmt->close();
                break;
            
            case 'returned':
                // Marquer un emprunt comme rendu
                $stmt = $conn->prepare("UPDATE Emprunter SET EtatEmprunt = 'Rendu', DateRestitutionReelle = NOW() WHERE MatriculeEtudiant = ? AND IDExemplaire = ? AND DateEmprunt = ?");
                $stmt->bind_param('sis', $matricule, $idExemplaire, $date);
                if ($stmt->execute()) {
                    // Mettre à jour l'état de l'exemplaire
                    $stmtUpdate = $conn->prepare("UPDATE Exemplaire SET EtatExemplaire = 'Disponible' WHERE IDExemplaire = ?");
                    $stmtUpdate->bind_param('i', $idExemplaire);
                    if ($stmtUpdate->execute()) {
                        echo "Emprunt rendu avec succès et exemplaire mis à jour.";
                    } else {
                        echo "Erreur lors de la mise à jour de l'état de l'exemplaire.";
                    }
                    $stmtUpdate->close();
                } else {
                    echo "Erreur lors de la mise à jour de l'état de l'emprunt.";
                }
                $stmt->close();
                break;
            

        default:
            echo "Action non reconnue.";
            break;
    }

    $conn->close();
} else {
    echo "Requête non autorisée.";
}
?>
