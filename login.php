<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $email = $_POST['emailC'];
    $password = $_POST['passwordC'];

    if ($role == 'admin') {
        $sql = "SELECT * FROM Administrateur WHERE email = '$email'";
    } elseif ($role == 'user') {
        $sql = "SELECT * FROM Etudiant WHERE email = '$email'";
    }
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['mot_de_passe'])) {
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            echo json_encode(array("status" => "success", "message" => "Connexion reussie"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Mot de passe incorrect"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Email incorrect"));
    }

    $conn->close();
}
?>
