<?php
declare(strict_types=1);

session_set_cookie_params([
    'httponly' => true,
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax'
]);
session_start();

require_once 'mail.php';
require_once 'db.php';

if (empty($_GET['state']) || !hash_equals((string) ($_SESSION['oauth_state'] ?? ''), (string) $_GET['state'])) {
    http_response_code(400);
    exit('Erreur de securite : etat OAuth invalide.');
}
unset($_SESSION['oauth_state']);

if (isset($_GET['error'])) {
    header('Location: login.html?error=' . urlencode((string) $_GET['error']));
    exit();
}

if (empty($_GET['code'])) {
    header('Location: login.html');
    exit();
}

$tokenData = oauth2_post('https://oauth2.googleapis.com/token', [
    'code'          => (string) $_GET['code'],
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code',
]);

if (empty($tokenData['access_token'])) {
    error_log('Google OAuth token exchange failed: ' . json_encode($tokenData));
    http_response_code(500);
    exit('Erreur lors de la connexion Google. Verifie les identifiants OAuth locaux.');
}

$googleUser = oauth2_get('https://www.googleapis.com/oauth2/v3/userinfo', (string) $tokenData['access_token']);
$googleSub = (string) ($googleUser['sub'] ?? '');
$googleEmail = (string) ($googleUser['email'] ?? '');
$googleName = trim((string) ($googleUser['name'] ?? ''));
$googlePicture = (string) ($googleUser['picture'] ?? '');
$emailVerified = filter_var($googleUser['email_verified'] ?? false, FILTER_VALIDATE_BOOL);

if ($googleSub === '' || $googleEmail === '' || !$emailVerified) {
    http_response_code(500);
    exit('Impossible de recuperer un compte Google valide avec email verifie.');
}

try {
    $pdo = db_connection();
    $pdo->beginTransaction();

    $user = find_google_user($pdo, $googleSub, $googleEmail);
    $isNewUser = $user === null;

    if ($user === null) {
        $username = make_unique_username($pdo, $googleName, $googleEmail);

        $insert = $pdo->prepare(
            "INSERT INTO utilisateur (username, email, auth_provider, google_sub)
             VALUES (?, ?, 'google', ?)"
        );
        $insert->execute([$username, $googleEmail, $googleSub]);

        $user = [
            'id_utilisateur' => (int) $pdo->lastInsertId(),
            'username' => $username,
            'email' => $googleEmail,
            'auth_provider' => 'google',
        ];
    } else {
        $update = $pdo->prepare(
            "UPDATE utilisateur
             SET google_sub = ?,
                 email = ?,
                 username = CASE
                    WHEN auth_provider = 'google' THEN ?
                    ELSE username
                 END
             WHERE id_utilisateur = ?"
        );
        $update->execute([
            $googleSub,
            $googleEmail,
            make_unique_username($pdo, $googleName, $googleEmail, (int) $user['id_utilisateur']),
            (int) $user['id_utilisateur'],
        ]);

        $user = load_user_by_id($pdo, (int) $user['id_utilisateur']);
    }

    $pdo->commit();

    if ($user === null) {
        throw new RuntimeException('Google user was not persisted.');
    }

    $_SESSION['user'] = [
        'id'         => (int) $user['id_utilisateur'],
        'google_sub' => $googleSub,
        'name'       => $user['username'] ?: ($googleName !== '' ? $googleName : 'Utilisateur'),
        'email'      => $googleEmail,
        'picture'    => $googlePicture,
        'auth'       => 'google',
    ];

    send_auth_email($googleEmail, $_SESSION['user']['name'], $isNewUser ? 'signup' : 'login');

    header('Location: profile.php');
    exit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Google OAuth callback failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Connexion Google impossible pour le moment.');
}

function find_google_user(PDO $pdo, string $googleSub, string $email): ?array
{
    $stmt = $pdo->prepare(
        "SELECT id_utilisateur, username, email, auth_provider, google_sub
         FROM utilisateur
         WHERE google_sub = ?
         LIMIT 1"
    );
    $stmt->execute([$googleSub]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return $user;
    }

    $stmt = $pdo->prepare(
        "SELECT id_utilisateur, username, email, auth_provider, google_sub
         FROM utilisateur
         WHERE email = ?
         LIMIT 1"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

function load_user_by_id(PDO $pdo, int $userId): ?array
{
    $stmt = $pdo->prepare(
        "SELECT id_utilisateur, username, email, auth_provider, google_sub
         FROM utilisateur
         WHERE id_utilisateur = ?
         LIMIT 1"
    );
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

function make_unique_username(PDO $pdo, string $name, string $email, ?int $currentUserId = null): string
{
    $base = $name !== '' ? $name : strstr($email, '@', true);
    $base = strtolower(trim((string) $base));
    $base = preg_replace('/[^a-z0-9_-]+/', '-', $base) ?: 'google-user';
    $base = trim($base, '-_') ?: 'google-user';
    $base = substr($base, 0, 42);

    for ($i = 0; $i < 100; $i++) {
        $candidate = $i === 0 ? $base : substr($base, 0, 42 - strlen((string) $i) - 1) . '-' . $i;

        $stmt = $pdo->prepare(
            "SELECT id_utilisateur
             FROM utilisateur
             WHERE username = ?
             LIMIT 1"
        );
        $stmt->execute([$candidate]);
        $existingId = $stmt->fetchColumn();

        if ($existingId === false || (int) $existingId === $currentUserId) {
            return $candidate;
        }
    }

    return 'google-user-' . bin2hex(random_bytes(4));
}

function oauth2_post(string $url, array $data): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT        => 15,
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['error' => $error ?: 'curl_error'];
    }

    return json_decode($response, true) ?: [];
}

function oauth2_get(string $url, string $accessToken): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        CURLOPT_TIMEOUT        => 15,
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['error' => $error ?: 'curl_error'];
    }

    return json_decode($response, true) ?: [];
}
