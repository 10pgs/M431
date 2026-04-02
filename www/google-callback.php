<?php
session_start();
require_once 'config.php';

// ── 1. Validate CSRF state ──────────────────────────────────────────────────
if (empty($_GET['state']) || $_GET['state'] !== ($_SESSION['oauth_state'] ?? '')) {
    http_response_code(400);
    exit('Erreur de sécurité : état OAuth invalide.');
}
unset($_SESSION['oauth_state']);

// ── 2. Handle user-denied consent ──────────────────────────────────────────
if (isset($_GET['error'])) {
    header('Location: login.html?error=' . urlencode($_GET['error']));
    exit();
}

if (empty($_GET['code'])) {
    header('Location: login.html');
    exit();
}

// ── 3. Exchange authorization code for tokens ───────────────────────────────
$tokenData = oauth2Post('https://oauth2.googleapis.com/token', [
    'code'          => $_GET['code'],
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code',
]);

if (empty($tokenData['access_token'])) {
    http_response_code(500);
    exit('Erreur lors de l\'échange du code OAuth. Vérifiez vos identifiants dans config.php.');
}

// ── 4. Fetch user info ──────────────────────────────────────────────────────
$userInfo = oauth2Get('https://www.googleapis.com/oauth2/v3/userinfo', $tokenData['access_token']);

if (empty($userInfo['sub'])) {
    http_response_code(500);
    exit('Impossible de récupérer les informations du compte Google.');
}

// ── 4.5. Insert or update user in database ───────────────────────────────
try {
    $pdo = new PDO('mysql:host=db;dbname=gamestore;charset=utf8mb4', 'user', 'userpassword');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Try to insert, or update if exists
    $stmt = $pdo->prepare("INSERT INTO utilisateur (username, email, auth_provider, google_sub) VALUES (?, ?, 'google', ?)
        ON DUPLICATE KEY UPDATE username=VALUES(username), email=VALUES(email), google_sub=VALUES(google_sub)");
    $stmt->execute([
        $userInfo['name'] ?? 'Utilisateur',
        $userInfo['email'] ?? null,
        $userInfo['sub']
    ]);
} catch (PDOException $e) {
    // Optionally log error
    // error_log($e->getMessage());
}

// ── 5. Store user in session and redirect ───────────────────────────────────
$_SESSION['user'] = [
    'id'      => $userInfo['sub'],
    'name'    => $userInfo['name']    ?? 'Utilisateur',
    'email'   => $userInfo['email']   ?? '',
    'picture' => $userInfo['picture'] ?? '',
    'auth'    => 'google',
];

header('Location: index.html');
exit();

// ── Helpers ─────────────────────────────────────────────────────────────────
function oauth2Post(string $url, array $data): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response ?: '{}', true) ?? [];
}

function oauth2Get(string $url, string $accessToken): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response ?: '{}', true) ?? [];
}
