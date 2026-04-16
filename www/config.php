<?php
/**
 * Identifiants OAuth Google.
 * Pour les obtenir :
 * 1. https://console.cloud.google.com/
 * 2. APIs & Services → Credentials → Create OAuth 2.0 Client ID (type Web)
 * 3. URI de redirection autorisée : http://localhost/google-callback.php
 * 4. Renseigner le Client ID et le Client Secret ci-dessous.
 */
if (!function_exists('app_env')) {
    function app_env(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return (string) $value;
    }
}

define('GOOGLE_CLIENT_ID',     '28340098458-eudnbtqaf7t8rf1iudoun59dc5723gvk.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI',  'http://localhost/google-callback.php');
define('SMTP_HOST',            app_env('SMTP_HOST', ''));
define('SMTP_PORT',            (int) app_env('SMTP_PORT', '587'));
define('SMTP_ENCRYPTION',      strtolower(app_env('SMTP_ENCRYPTION', 'tls') ?? 'tls'));
define('SMTP_USERNAME',        app_env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD',        app_env('SMTP_PASSWORD', ''));
define('MAIL_FROM_ADDRESS',    app_env('MAIL_FROM_ADDRESS', ''));
define('MAIL_FROM_NAME',       app_env('MAIL_FROM_NAME', 'Game Store'));
