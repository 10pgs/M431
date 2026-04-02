<?php
// Simple registration handler (no real validation or database for demo)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    $creditcard = htmlspecialchars($_POST['creditcard'] ?? '');
    // Here you would hash the password and store the user in a database
    echo "<h2>Inscription réussie !</h2>";
    echo "<p>Bienvenue, <strong>$username</strong> !</p>";
    echo "<p>Votre carte: **** **** **** " . substr($creditcard, -4) . "</p>";
    echo '<a href="index.html">Retour à l\'accueil</a>';
} else {
    header('Location: form.html');
    exit();
}
