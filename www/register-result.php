<?php
session_set_cookie_params([
    'httponly' => true,
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax'
]);
session_start();

require_once 'auth-view.php';

$feedback = $_SESSION['auth_feedback'] ?? null;
unset($_SESSION['auth_feedback']);

if (!is_array($feedback)) {
    header('Location: form.html');
    exit();
}

if (($feedback['mode'] ?? '') === 'success') {
    render_auth_success_page(
        (string) ($feedback['document_title'] ?? 'Inscription - Game Store'),
        (string) ($feedback['heading'] ?? 'Inscription reussie'),
        (string) ($feedback['message'] ?? ''),
        (array) ($feedback['rows'] ?? []),
        (array) ($feedback['actions'] ?? [])
    );
    exit();
}

render_auth_error_page(
    (string) ($feedback['document_title'] ?? 'Erreur inscription - Game Store'),
    (string) ($feedback['heading'] ?? 'Erreur lors de l\'inscription'),
    (string) ($feedback['message'] ?? ''),
    (string) ($feedback['back_href'] ?? 'form.html')
);
