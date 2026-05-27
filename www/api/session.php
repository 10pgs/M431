<?php
declare(strict_types=1);

session_set_cookie_params([
    'httponly' => true,
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax'
]);
session_start();

header('Content-Type: application/json; charset=utf-8');

$user = $_SESSION['user'] ?? null;
$authenticated = is_array($user) && !empty($user['id']);

echo json_encode([
    'ok' => true,
    'authenticated' => $authenticated,
    'user' => $authenticated ? [
        'id' => $user['id'],
        'name' => $user['name'] ?? '',
        'email' => $user['email'] ?? '',
        'auth' => $user['auth'] ?? '',
    ] : null,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
