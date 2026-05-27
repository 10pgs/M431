<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/mail.php';

$toEmail = $argv[1] ?? app_env('TEST_MAIL_TO', '');

if (!auth_mail_enabled()) {
    fwrite(STDERR, "Configuration SMTP incomplete: verifie SMTP_HOST et MAIL_FROM_ADDRESS.\n");
    exit(1);
}

if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Indique un email destinataire valide: php tools/test-mail-smtp.php test@example.com\n");
    exit(1);
}

if (!send_auth_email($toEmail, 'Test SMTP', 'signup')) {
    fwrite(STDERR, "L'email SMTP n'a pas pu etre envoye. Regarde les logs Docker du service web.\n");
    exit(1);
}

echo "OK - email SMTP envoye vers {$toEmail}\n";
