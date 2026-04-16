<?php
session_set_cookie_params([
    'httponly' => true,
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax'
]);
session_start();

require_once 'auth-view.php';
require_once 'mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form.html');
    exit();
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$creditcard = preg_replace('/\D/', '', $_POST['creditcard'] ?? '');

if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($creditcard) !== 16) {
    $_SESSION['auth_feedback'] = [
        'mode' => 'error',
        'document_title' => 'Inscription impossible - Game Store',
        'heading' => 'Inscription impossible',
        'message' => 'Veuillez remplir tous les champs correctement avant de reessayer.',
        'back_href' => 'form.html'
    ];
    header('Location: register-result.php');
    exit();
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$cardLast4 = substr($creditcard, -4);

try {
    $pdo = new PDO('mysql:host=db;dbname=gamestore;charset=utf8mb4', 'user', 'userpassword');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $stmt = $pdo->prepare(
        "INSERT INTO utilisateur (username, email, password_hash, card_last4)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$username, $email, $passwordHash, $cardLast4]);

    send_auth_email($email, $username, 'signup');
    $_SESSION['auth_feedback'] = [
        'mode' => 'success',
        'document_title' => 'Inscription reussie - Game Store',
        'heading' => 'Inscription reussie',
        'message' => 'Bienvenue dans Game Store, nous avons bien cree votre compte.',
        'rows' => [
            'Utilisateur' => $username,
            'Email' => $email,
            'Carte' => '**** **** **** ' . $cardLast4,
        ],
        'actions' => [
            ['href' => 'login.html', 'label' => 'Se connecter', 'class' => 'btn-primary'],
            ['href' => 'index.html', 'label' => 'Retour a l\'accueil', 'class' => 'btn-secondary'],
        ],
    ];
    header('Location: register-result.php');
    exit();
} catch (PDOException $e) {
    $message = 'Erreur lors de l\'inscription.';

    if (str_contains($e->getMessage(), 'uq_utilisateur_username')) {
        $message = "Erreur : Ce nom d'utilisateur existe deja.";
    } elseif (str_contains($e->getMessage(), 'uq_utilisateur_email')) {
        $message = 'Erreur : Cette adresse email est deja utilisee.';
    }

    $_SESSION['auth_feedback'] = [
        'mode' => 'error',
        'document_title' => 'Erreur inscription - Game Store',
        'heading' => 'Erreur lors de l\'inscription',
        'message' => $message,
        'back_href' => 'form.html'
    ];
    header('Location: register-result.php');
    exit();
}
