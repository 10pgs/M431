<?php
// Register user in the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $creditcard = preg_replace('/\D/', '', $_POST['creditcard'] ?? '');

    // Basic validation
    if (!$username || !$password || strlen($creditcard) !== 16) {
        echo "<h2>Erreur : Veuillez remplir tous les champs correctement.</h2>";
        echo '<a href="form.html">Retour</a>';
        exit();
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $card_last4 = substr($creditcard, -4);

    try {
        $pdo = new PDO('mysql:host=db;dbname=gamestore;charset=utf8mb4', 'user', 'userpassword');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO utilisateur (username, password_hash, card_last4) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password_hash, $card_last4]);

        echo "<h2>Inscription réussie !</h2>";
        echo "<p>Bienvenue, <strong>" . htmlspecialchars($username) . "</strong> !</p>";
        echo "<p>Votre carte: **** **** **** " . htmlspecialchars($card_last4) . "</p>";
        echo '<a href="login.html">Se connecter</a>';
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'uq_utilisateur_username')) {
            echo "<h2>Erreur : Ce nom d'utilisateur existe déjà.</h2>";
        } else {
            echo "<h2>Erreur lors de l'inscription.</h2><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        }
        echo '<a href="form.html">Retour</a>';
    }
} else {
    header('Location: form.html');
    exit();
}
