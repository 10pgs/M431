<?php
// Simple login handler (demo only, no real authentication)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    // Here you would check credentials against a database
    echo "<h2>Connexion réussie !</h2>";
    echo "<p>Bienvenue, <strong>$username</strong> !</p>";
    echo '<a href="index.html">Retour à l\'accueil</a>';
} else {
    header('Location: login.html');
    exit();
}
