<?php
require_once 'auth-view.php';
require_once 'mail.php';
require_once 'db.php';

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
    $pdo = db_connection();

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

render_auth_success_page(
    'Connexion reussie - Game Store',
    'Connexion reussie',
    'Bienvenue, ' . $user['username'] . '.',
    [],
    [
        ['href' => 'profile.php', 'label' => 'Voir mon profil', 'class' => 'btn-primary'],
    ]
);
