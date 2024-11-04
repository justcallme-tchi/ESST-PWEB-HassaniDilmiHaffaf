<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Vérifiez si l'email existe dans votre base de données
    // Si l'email est valide, générez un jeton unique
    $token = bin2hex(random_bytes(50)); // Jeton unique pour réinitialisation

    // Enregistrez le jeton et l'email dans la base de données avec une expiration (facultatif)

    // Envoyer le lien de réinitialisation par email
    $lienReinitialisation = "https://votresite.com/reset-password.php?token=$token";
    $sujet = "Demande de Réinitialisation de Mot de Passe";
    $message = "Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe : $lienReinitialisation";
    $entetes = "From: noreply@votresite.com";
    
    if (mail($email, $sujet, $message, $entetes)) {
        echo "Un lien de réinitialisation a été envoyé à votre adresse e-mail.";
    } else {
        echo "Échec de l'envoi de l'e-mail.";
    }
}
?>
