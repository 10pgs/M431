<?php
// Traitement de connexion simplifié (exemple, sans vraie auth)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit();
}

$username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');

echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion réussie – Game Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>
<body class="success-shell">
    <div class="card success-card">
        <div class="check">✓</div>
        <h1>Connexion réussie</h1>
        <p class="muted">Bienvenue, <strong>{$username}</strong>.</p>
        <div class="actions">
            <a class="btn btn-primary" href="index.html">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
HTML;
