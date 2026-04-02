<?php
/**
 * Google OAuth 2.0 credentials
 *
 * HOW TO GET YOUR CREDENTIALS:
 * 1. Go to https://console.cloud.google.com/
 * 2. Create a project (or select an existing one)
 * 3. APIs & Services → Credentials → Create Credentials → OAuth 2.0 Client ID
 * 4. Application type: Web Application
 * 5. Authorised redirect URIs: http://localhost/google-callback.php
 * 6. Copy the Client ID and Client Secret below
 */
define('GOOGLE_CLIENT_ID',     '28340098458-eudnbtqaf7t8rf1iudoun59dc5723gvk.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI',  'http://localhost/google-callback.php');
