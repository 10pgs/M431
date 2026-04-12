<?php
// Register user in the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $creditcard = preg_replace('/\D/', '', $_POST['creditcard'] ?? '');

    // Basic validation
    if (!$username || !$password || strlen($creditcard) !== 16) {
        echo "<h2>Erreur : Veuillez remplir tous les champs correctement.</h2>";
        echo '<a href="form.html">Retour</a>';
        exit();
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $card_last4 = substr($creditcard, -4);

    try {
        $pdo = new PDO('mysql:host=db;dbname=gamestore;charset=utf8mb4', 'user', 'userpassword');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO utilisateur (username, password_hash, card_last4) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password_hash, $card_last4]);

        $safeUser = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $safeLast4 = htmlspecialchars($card_last4, ENT_QUOTES, 'UTF-8');
        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription réussie – Game Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <footer class="footer-note">
        © Tout droit réservé à Thierry Tavares, Dan Zorev et Jason Haran.
    </footer>
    <div class="card">
        <div class="check">✓</div>
        <h1>Inscription réussie</h1>
        <p class="muted">Bienvenue dans Game Store, nous avons bien créé votre compte.</p>

        <div class="grid">
            <span class="label">Utilisateur</span>
            <span class="value">{$safeUser}</span>
            <span class="label">Carte</span>
            <span class="value">**** **** **** {$safeLast4}</span>
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="login.html">Se connecter</a>
            <a class="btn btn-secondary" href="index.html">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
HTML;
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'uq_utilisateur_username')) {
            echo "<h2>Erreur : Ce nom d'utilisateur existe déjà.</h2>";
        } else {
            echo "<h2>Erreur lors de l'inscription.</h2><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        }
        echo '<a href="form.html">Retour</a>';
    }
} else {
    header('Location: form.html');
    exit();
}
