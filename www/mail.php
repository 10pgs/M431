<?php
require_once 'config.php';

function auth_mail_enabled(): bool
{
    return SMTP_HOST !== '' && MAIL_FROM_ADDRESS !== '';
}

function send_auth_email(string $toEmail, string $toName, string $event): bool
{
    if ($toEmail === '' || !auth_mail_enabled()) {
        return false;
    }

    [$subject, $headline, $message] = auth_mail_content($event, $toName);

    return smtp_send_mail($toEmail, $toName, $subject, build_auth_html($headline, $message), build_auth_text($headline, $message));
}

function auth_mail_content(string $event, string $name): array
{
    $safeName = $name !== '' ? $name : 'joueur';

    if ($event === 'signup') {
        return [
            'Compte Game Store cree',
            'Compte cree',
            "Bonjour {$safeName},\n\nTon compte Game Store vient d'etre cree avec succes."
        ];
    }

    return [
        'Connexion Game Store',
        'Connexion detectee',
        "Bonjour {$safeName},\n\nUne connexion a ton compte Game Store vient d'etre detectee."
    ];
}

function build_auth_html(string $headline, string $message): string
{
    $headlineHtml = htmlspecialchars($headline, ENT_QUOTES, 'UTF-8');
    $messageHtml = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

    return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<body style="margin:0;padding:24px;background:#f4f4f5;font-family:Arial,sans-serif;color:#111827;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:16px;padding:32px;border:1px solid #e5e7eb;">
        <h1 style="margin:0 0 16px;font-size:28px;">{$headlineHtml}</h1>
        <p style="margin:0 0 16px;line-height:1.6;">{$messageHtml}</p>
        <p style="margin:0;color:#6b7280;line-height:1.6;">Si ce n'etait pas toi, change ton mot de passe et verifie tes acces.</p>
    </div>
</body>
</html>
HTML;
}

function build_auth_text(string $headline, string $message): string
{
    return $headline . "\n\n" . $message . "\n\nSi ce n'etait pas toi, change ton mot de passe et verifie tes acces.\n";
}

function smtp_send_mail(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody): bool
{
    $transport = SMTP_ENCRYPTION === 'ssl' ? 'ssl://' : '';
    $socket = @stream_socket_client(
        $transport . SMTP_HOST . ':' . SMTP_PORT,
        $errorCode,
        $errorMessage,
        15
    );

    if (!$socket) {
        error_log('SMTP connection failed: ' . $errorMessage . ' (' . $errorCode . ')');
        return false;
    }

    stream_set_timeout($socket, 15);

    if (!smtp_expect($socket, [220])) {
        fclose($socket);
        return false;
    }

    if (!smtp_command($socket, 'EHLO localhost', [250])) {
        fclose($socket);
        return false;
    }

    if (SMTP_ENCRYPTION === 'tls') {
        if (!smtp_command($socket, 'STARTTLS', [220])) {
            fclose($socket);
            return false;
        }

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log('SMTP STARTTLS failed');
            fclose($socket);
            return false;
        }

        if (!smtp_command($socket, 'EHLO localhost', [250])) {
            fclose($socket);
            return false;
        }
    }

    if (SMTP_USERNAME !== '') {
        if (!smtp_command($socket, 'AUTH LOGIN', [334])) {
            fclose($socket);
            return false;
        }

        if (!smtp_command($socket, base64_encode(SMTP_USERNAME), [334])) {
            fclose($socket);
            return false;
        }

        if (!smtp_command($socket, base64_encode(SMTP_PASSWORD), [235])) {
            fclose($socket);
            return false;
        }
    }

    if (!smtp_command($socket, 'MAIL FROM:<' . MAIL_FROM_ADDRESS . '>', [250])) {
        fclose($socket);
        return false;
    }

    if (!smtp_command($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251])) {
        fclose($socket);
        return false;
    }

    if (!smtp_command($socket, 'DATA', [354])) {
        fclose($socket);
        return false;
    }

    $boundary = 'gs_' . bin2hex(random_bytes(8));
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $encodedFromName = '=?UTF-8?B?' . base64_encode(MAIL_FROM_NAME) . '?=';
    $encodedToName = $toName !== '' ? '=?UTF-8?B?' . base64_encode($toName) . '?= ' : '';

    $headers = [
        'Date: ' . date(DATE_RFC2822),
        'From: ' . $encodedFromName . ' <' . MAIL_FROM_ADDRESS . '>',
        'To: ' . $encodedToName . '<' . $toEmail . '>',
        'Subject: ' . $encodedSubject,
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"'
    ];

    $message = implode("\r\n", $headers) . "\r\n\r\n"
        . '--' . $boundary . "\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n"
        . "Content-Transfer-Encoding: 8bit\r\n\r\n"
        . smtp_escape_body($textBody) . "\r\n"
        . '--' . $boundary . "\r\n"
        . "Content-Type: text/html; charset=UTF-8\r\n"
        . "Content-Transfer-Encoding: 8bit\r\n\r\n"
        . smtp_escape_body($htmlBody) . "\r\n"
        . '--' . $boundary . "--\r\n.\r\n";

    fwrite($socket, $message);

    if (!smtp_expect($socket, [250])) {
        fclose($socket);
        return false;
    }

    smtp_command($socket, 'QUIT', [221]);
    fclose($socket);

    return true;
}

function smtp_command($socket, string $command, array $validCodes): bool
{
    fwrite($socket, $command . "\r\n");
    return smtp_expect($socket, $validCodes);
}

function smtp_expect($socket, array $validCodes): bool
{
    $response = '';

    while (($line = fgets($socket, 512)) !== false) {
        $response .= $line;
        if (strlen($line) < 4 || $line[3] !== '-') {
            break;
        }
    }

    $code = (int) substr($response, 0, 3);
    if (!in_array($code, $validCodes, true)) {
        error_log('SMTP unexpected response: ' . trim($response));
        return false;
    }

    return true;
}

function smtp_escape_body(string $body): string
{
    $normalized = str_replace(["\r\n", "\r"], "\n", $body);
    $escaped = preg_replace('/(?m)^\./', '..', $normalized);

    return str_replace("\n", "\r\n", $escaped ?? $normalized);
}
