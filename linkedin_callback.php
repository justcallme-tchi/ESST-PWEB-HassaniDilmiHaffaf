<?php
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $client_id = 'VOTRE_IDENTIFIANT_CLIENT_LINKEDIN';
    $client_secret = 'VOTRE_SECRET_CLIENT_LINKEDIN';
    $redirect_uri = 'https://votresite.com/linkedin_callback.php';

    // Échange du code contre un jeton d'accès
    $token_url = "https://www.linkedin.com/oauth/v2/accessToken";
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
        'client_secret' => $client_secret
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($token_url, false, $context);
    $response = json_decode($response, true);

    if (isset($response['access_token'])) {
        $access_token = $response['access_token'];

        // Récupération des informations de profil de l'utilisateur
        $user_info_url = "https://api.linkedin.com/v2/me";
        $user_info_options = [
            "http" => [
                "header" => "Authorization: Bearer $access_token"
            ]
        ];
        $user_info_context = stream_context_create($user_info_options);
        $user_info = file_get_contents($user_info_url, false, $user_info_context);
        $user_info = json_decode($user_info, true);

        echo "<h1>Bienvenue, " . htmlspecialchars($user_info['localizedFirstName']) . " " . htmlspecialchars($user_info['localizedLastName']) . "</h1>";
    } else {
        echo "Échec de l'obtention du jeton d'accès.";
    }
} else {
    echo "Code d'autorisation non présent.";
}
?>
