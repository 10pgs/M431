<?php
/**
 * Configuration chargee depuis l'environnement Docker/local.
 * Copier .env.example vers .env et y renseigner les valeurs privees.
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

define('GOOGLE_CLIENT_ID',     app_env('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', app_env('GOOGLE_CLIENT_SECRET', ''));
define('GOOGLE_REDIRECT_URI',  app_env('GOOGLE_REDIRECT_URI', 'http://localhost/google-callback.php'));
define('SMTP_HOST',            app_env('SMTP_HOST', ''));
define('SMTP_PORT',            (int) app_env('SMTP_PORT', '587'));
define('SMTP_ENCRYPTION',      strtolower(app_env('SMTP_ENCRYPTION', 'tls') ?? 'tls'));
define('SMTP_USERNAME',        app_env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD',        app_env('SMTP_PASSWORD', ''));
define('MAIL_FROM_ADDRESS',    app_env('MAIL_FROM_ADDRESS', ''));
define('MAIL_FROM_NAME',       app_env('MAIL_FROM_NAME', 'Game Store'));
