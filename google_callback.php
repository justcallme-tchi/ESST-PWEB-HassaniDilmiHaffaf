<?php
// Assurez-vous que le code de retour est présent dans l'URL
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $client_id = 'VOTRE_IDENTIFIANT_CLIENT_GOOGLE';
    $client_secret = 'VOTRE_SECRET_CLIENT_GOOGLE';
    $redirect_uri = 'VOTRE_URI_DE_REDIRECTION_GOOGLE';

    // Échange du code contre un jeton d'accès
    $url = 'https://oauth2.googleapis.com/token';
    $data = [
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $response = json_decode($response, true);

    if (isset($response['access_token'])) {
        $access_token = $response['access_token'];

        // Récupération des informations de profil de l'utilisateur
        $user_info = file_get_contents("https://www.googleapis.com/oauth2/v1/userinfo?access_token=$access_token");
        $user_info = json_decode($user_info, true);

        // Utilisez $user_info pour obtenir les informations de l'utilisateur
        echo "<h1>Bienvenue, " . htmlspecialchars($user_info['name']) . "</h1>";
    } else {
        echo "Échec de l'obtention du jeton d'accès.";
    }
} else {
    echo "Code d'autorisation non présent.";
}
?>
