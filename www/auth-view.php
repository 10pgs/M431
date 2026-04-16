<?php

function render_auth_success_page(
    string $documentTitle,
    string $heading,
    string $message,
    array $rows,
    array $actions
): void {
    $safeDocumentTitle = htmlspecialchars($documentTitle, ENT_QUOTES, 'UTF-8');
    $safeHeading = htmlspecialchars($heading, ENT_QUOTES, 'UTF-8');
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    $gridRows = '';
    foreach ($rows as $label => $value) {
        $gridRows .= '<span class="label">' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</span>';
        $gridRows .= '<span class="value">' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    $actionLinks = '';
    foreach ($actions as $action) {
        $href = htmlspecialchars((string) ($action['href'] ?? '#'), ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars((string) ($action['label'] ?? 'Continuer'), ENT_QUOTES, 'UTF-8');
        $class = htmlspecialchars((string) ($action['class'] ?? 'btn-secondary'), ENT_QUOTES, 'UTF-8');
        $actionLinks .= '<a class="btn ' . $class . '" href="' . $href . '">' . $label . '</a>';
    }

    echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{$safeDocumentTitle}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>
<body class="success-shell">
    <div class="card">
        <div class="check">✓</div>
        <h1>{$safeHeading}</h1>
        <p class="muted">{$safeMessage}</p>
        <div class="grid">{$gridRows}</div>
        <div class="actions">{$actionLinks}</div>
    </div>
</body>
</html>
HTML;
}

function render_auth_error_page(
    string $documentTitle,
    string $heading,
    string $message,
    string $backHref,
    string $backLabel = 'Retour'
): void {
    $safeDocumentTitle = htmlspecialchars($documentTitle, ENT_QUOTES, 'UTF-8');
    $safeHeading = htmlspecialchars($heading, ENT_QUOTES, 'UTF-8');
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $safeBackHref = htmlspecialchars($backHref, ENT_QUOTES, 'UTF-8');
    $safeBackLabel = htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$safeDocumentTitle}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>
<body class="success-shell">
    <div class="card error-card">
        <div class="status-icon error-icon">!</div>
        <h1>{$safeHeading}</h1>
        <p class="muted">{$safeMessage}</p>
        <div class="actions">
            <a class="btn btn-primary" href="{$safeBackHref}">{$safeBackLabel}</a>
        </div>
    </div>
</body>
</html>
HTML;
}
