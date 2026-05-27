<?php
declare(strict_types=1);

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = db_connection()->query(
        "SELECT
            slug,
            titre,
            description_courte,
            description_longue,
            prix,
            date_sortie,
            image_url,
            download_url
         FROM jeu
         WHERE actif = 1
         ORDER BY date_sortie DESC, titre ASC"
    );

    $games = [];
    foreach ($stmt->fetchAll() as $row) {
        $price = (float) $row['prix'];
        $games[] = [
            'slug' => $row['slug'],
            'name' => $row['titre'],
            'price' => $price <= 0 ? 'Gratuit' : rtrim(rtrim(number_format($price, 2, '.', ''), '0'), '.') . 'EUR',
            'date' => $row['date_sortie'],
            'image' => $row['image_url'],
            'alt' => $row['titre'],
            'shortDesc' => $row['description_courte'],
            'downloadUrl' => $row['download_url'] ?: 'games.html',
            'longDesc' => $row['description_longue'] ?: $row['description_courte'],
        ];
    }

    echo json_encode([
        'ok' => true,
        'games' => $games,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode([
        'ok' => false,
        'error' => 'database_unavailable',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

