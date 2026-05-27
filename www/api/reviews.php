<?php
declare(strict_types=1);

session_set_cookie_params([
    'httponly' => true,
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax'
]);
session_start();

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function current_user_id(): ?int
{
    $user = $_SESSION['user'] ?? null;
    if (!is_array($user) || empty($user['id'])) {
        return null;
    }

    return (int) $user['id'];
}

try {
    $pdo = db_connection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $slug = trim((string) ($_GET['slug'] ?? ''));
        $params = [];
        $where = '';

        if ($slug !== '') {
            $where = 'WHERE j.slug = ?';
            $params[] = $slug;
        }

        $stmt = $pdo->prepare(
            "SELECT
                a.id_avis,
                a.note,
                a.commentaire,
                a.created_at,
                a.updated_at,
                u.username,
                j.slug,
                j.titre AS jeu
             FROM avis a
             INNER JOIN utilisateur u ON u.id_utilisateur = a.id_utilisateur
             INNER JOIN jeu j ON j.id_jeu = a.id_jeu
             {$where}
             ORDER BY a.updated_at DESC, a.created_at DESC
             LIMIT 50"
        );
        $stmt->execute($params);

        json_response([
            'ok' => true,
            'authenticated' => current_user_id() !== null,
            'reviews' => $stmt->fetchAll(),
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['ok' => false, 'error' => 'method_not_allowed'], 405);
    }

    $userId = current_user_id();
    if ($userId === null) {
        json_response([
            'ok' => false,
            'error' => 'not_authenticated',
            'message' => 'Connecte-toi pour publier un avis.',
        ], 401);
    }

    $slug = trim((string) ($_POST['slug'] ?? ''));
    $note = filter_var($_POST['note'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5],
    ]);
    $commentaire = trim((string) ($_POST['commentaire'] ?? ''));

    if ($slug === '' || $note === false || $commentaire === '') {
        json_response([
            'ok' => false,
            'error' => 'invalid_input',
            'message' => 'Choisis un jeu, une note entre 1 et 5, puis écris ton avis.',
        ], 422);
    }

    if (mb_strlen($commentaire) > 1000) {
        json_response([
            'ok' => false,
            'error' => 'comment_too_long',
            'message' => 'Ton avis doit faire 1000 caractères maximum.',
        ], 422);
    }

    $gameStmt = $pdo->prepare('SELECT id_jeu FROM jeu WHERE slug = ? AND actif = 1 LIMIT 1');
    $gameStmt->execute([$slug]);
    $game = $gameStmt->fetch();

    if (!$game) {
        json_response([
            'ok' => false,
            'error' => 'game_not_found',
            'message' => 'Ce jeu est introuvable dans le catalogue.',
        ], 404);
    }

    $stmt = $pdo->prepare(
        "INSERT INTO avis (id_utilisateur, id_jeu, note, commentaire)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            note = VALUES(note),
            commentaire = VALUES(commentaire),
            updated_at = CURRENT_TIMESTAMP"
    );
    $stmt->execute([$userId, (int) $game['id_jeu'], $note, $commentaire]);

    json_response([
        'ok' => true,
        'message' => 'Ton avis a été publié.',
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'error' => 'server_error',
        'message' => 'Impossible de charger ou publier les avis pour le moment.',
    ], 500);
}
