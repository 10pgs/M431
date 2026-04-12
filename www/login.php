<?php
// Traitement de connexion simplifié (exemple, sans vraie auth)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8');
    // Ici on vérifierait les identifiants en base
    echo "<h2>Connexion réussie !</h2>";
    echo "<p>Bienvenue, <strong>$username</strong> !</p>";
    echo '<a href="index.html">Retour à l\'accueil</a>';
} else {
    header('Location: login.html');
    exit();
}
