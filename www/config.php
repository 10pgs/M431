<?php
/**
 * Configuration chargee depuis l'environnement Docker/local.
 * Renseigner les valeurs privees dans un fichier .env local ignore par Git.
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

define('DB_HOST',              app_env('DB_HOST', 'db'));
define('DB_NAME',              app_env('DB_NAME', 'gamestore'));
define('DB_USER',              app_env('DB_USER', 'user'));
define('DB_PASSWORD',          app_env('DB_PASSWORD', 'userpassword'));
define('DB_CHARSET',           app_env('DB_CHARSET', 'utf8mb4'));
