<?php
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $client_id = 'VOTRE_IDENTIFIANT_CLIENT_FACEBOOK';
    $client_secret = 'VOTRE_SECRET_CLIENT_FACEBOOK';
    $redirect_uri = 'https://votresite.com/facebook_callback.php';

    // Échange du code contre un jeton d'accès
    $token_url = "https://graph.facebook.com/v13.0/oauth/access_token?client_id=$client_id&redirect_uri=$redirect_uri&client_secret=$client_secret&code=$code";
    $response = file_get_contents($token_url);
    $response = json_decode($response, true);

    if (isset($response['access_token'])) {
        $access_token = $response['access_token'];

        // Récupération des informations de profil de l'utilisateur
        $user_info_url = "https://graph.facebook.com/me?access_token=$access_token&fields=id,name,email";
        $user_info = file_get_contents($user_info_url);
        $user_info = json_decode($user_info, true);

        echo "<h1>Bienvenue, " . htmlspecialchars($user_info['name']) . "</h1>";
        echo "<p>Email : " . htmlspecialchars($user_info['email']) . "</p>";
    } else {
        echo "Échec de l'obtention du jeton d'accès.";
    }
} else {
    echo "Code d'autorisation non présent.";
}
?>
