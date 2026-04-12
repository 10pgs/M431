<?php
/**
 * Identifiants OAuth Google.
 * Pour les obtenir :
 * 1. https://console.cloud.google.com/
 * 2. APIs & Services → Credentials → Create OAuth 2.0 Client ID (type Web)
 * 3. URI de redirection autorisée : http://localhost/google-callback.php
 * 4. Renseigner le Client ID et le Client Secret ci-dessous.
 */
define('GOOGLE_CLIENT_ID',     '28340098458-eudnbtqaf7t8rf1iudoun59dc5723gvk.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI',  'http://localhost/google-callback.php');
