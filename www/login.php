<?php
require_once 'auth-view.php';
require_once 'mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit();
}

session_set_cookie_params([
    'httponly' => true,
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax'
]);
session_start();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    render_auth_error_page(
        'Connexion impossible - Game Store',
        'Connexion impossible',
        'Veuillez saisir votre nom d\'utilisateur et votre mot de passe.',
        'login.html'
    );
    exit();
}

try {
    $pdo = new PDO('mysql:host=db;dbname=gamestore;charset=utf8mb4', 'user', 'userpassword');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $stmt = $pdo->prepare(
        "SELECT id_utilisateur, username, email, password_hash
         FROM utilisateur
         WHERE username = ? AND auth_provider = 'local'
         LIMIT 1"
    );
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    render_auth_error_page(
        'Erreur connexion - Game Store',
        'Erreur de connexion',
        'Impossible de verifier votre connexion pour le moment.',
        'login.html'
    );
    exit();
}

if (!$user || !password_verify($password, $user['password_hash'] ?? '')) {
    render_auth_error_page(
        'Identifiants invalides - Game Store',
        'Identifiants invalides',
        'Le nom d\'utilisateur ou le mot de passe ne correspond pas.',
        'login.html'
    );
    exit();
}

$_SESSION['user'] = [
    'id'    => $user['id_utilisateur'],
    'name'  => $user['username'],
    'email' => $user['email'] ?? '',
    'auth'  => 'local',
];

if (!empty($user['email'])) {
    send_auth_email($user['email'], $user['username'], 'login');
}

$safeUser = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');

echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion reussie - Game Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>
<body class="success-shell">
    <div class="card success-card">
        <div class="check">✓</div>
        <h1>Connexion reussie</h1>
        <p class="muted">Bienvenue, <strong>{$safeUser}</strong>.</p>
        <div class="actions">
            <a class="btn btn-primary" href="index.html">Retour a l'accueil</a>
        </div>
    </div>
</body>
</html>
HTML;
