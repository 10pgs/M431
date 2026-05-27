<?php
session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax'
]);
session_start();

require_once 'db.php';

$user = $_SESSION['user'] ?? null;
$profile = null;
$purchases = [];
$dbNotice = '';

if ($user) {
    try {
        $pdo = db_connection();

        if (($user['auth'] ?? '') === 'google') {
            $stmt = $pdo->prepare(
                "SELECT id_utilisateur, username, email, auth_provider, card_last4, created_at
                 FROM utilisateur
                 WHERE id_utilisateur = ? OR google_sub = ? OR email = ?
                 LIMIT 1"
            );
            $stmt->execute([$user['id'] ?? 0, $user['google_sub'] ?? '', $user['email'] ?? '']);
        } else {
            $stmt = $pdo->prepare(
                "SELECT id_utilisateur, username, email, auth_provider, card_last4, created_at
                 FROM utilisateur
                 WHERE id_utilisateur = ?
                 LIMIT 1"
            );
            $stmt->execute([$user['id'] ?? 0]);
        }

        $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($profile) {
            $buyStmt = $pdo->prepare(
                "SELECT j.titre, a.date_achat, a.prix_achat, a.statut
                 FROM achat a
                 INNER JOIN jeu j ON j.id_jeu = a.id_jeu
                 WHERE a.id_utilisateur = ?
                 ORDER BY a.date_achat DESC
                 LIMIT 4"
            );
            $buyStmt->execute([$profile['id_utilisateur']]);
            $purchases = $buyStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $dbNotice = 'Les details du compte sont momentanement indisponibles.';
    }
}

$displayName = htmlspecialchars((string) ($profile['username'] ?? $user['name'] ?? 'Invite'), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars((string) ($profile['email'] ?? $user['email'] ?? 'Non renseigne'), ENT_QUOTES, 'UTF-8');
$authLabel = (($profile['auth_provider'] ?? $user['auth'] ?? '') === 'google') ? 'Google' : 'Compte local';
$cardLast4 = $profile['card_last4'] ?? null;
$createdAt = !empty($profile['created_at'])
    ? date('d.m.Y', strtotime((string) $profile['created_at']))
    : 'Non disponible';
$picture = htmlspecialchars((string) ($user['picture'] ?? ''), ENT_QUOTES, 'UTF-8');
$initial = strtoupper(substr((string) ($profile['username'] ?? $user['name'] ?? 'G'), 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Sora:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/logo.css">
    <link rel="stylesheet" href="css/profile.css?v=20260527-2">
    <title>Game Store - Profil</title>
</head>

<body>
    <header>
        <a href="index.html" class="brand-mark">
            <img src="img/logo.png" alt="Logo Game Store" class="brand-logo">
            <span>Game Store</span>
        </a>
        <div class="header-actions">
            <nav>
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="about.html">À propos</a></li>
                    <li><a href="reviews.html">Avis</a></li>
                    <li><a href="games.html">Jeux</a></li>
                    <li><a href="profile.php" class="active">Profil</a></li>
                </ul>
            </nav>
            <form id="search-form" class="search-form" action="javascript:void(0);">
                <input class="search-input" type="text" id="search-bar" placeholder="Rechercher un jeu...">
            </form>
        </div>
    </header>

    <main class="profile-main">
        <?php if (!$user): ?>
            <section class="guest-panel">
                <div>
                    <p class="eyebrow">Espace membre</p>
                    <h1>Connecte-toi pour voir ton profil</h1>
                    <p class="muted">Ton profil regroupe tes informations de compte et tes achats Game Store.</p>
                </div>
                <div class="actions">
                    <a href="login.html" class="btn-main btn-main--primary">Se connecter</a>
                    <a href="form.html" class="btn-main btn-main--secondary">Creer un compte</a>
                </div>
            </section>
        <?php else: ?>
            <section class="profile-hero">
                <div class="avatar" aria-hidden="true">
                    <?php if ($picture !== ''): ?>
                        <img src="<?php echo $picture; ?>" alt="">
                    <?php else: ?>
                        <span><?php echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
                <div class="profile-heading">
                    <p class="eyebrow">Profil joueur</p>
                    <h1><?php echo $displayName; ?></h1>
                    <p class="muted"><?php echo $email; ?></p>
                </div>
                <a href="logout.php" class="btn-main btn-main--secondary">Se deconnecter</a>
            </section>

            <?php if ($dbNotice !== ''): ?>
                <p class="notice"><?php echo htmlspecialchars($dbNotice, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <section class="profile-grid">
                <article class="info-card">
                    <span class="label">Connexion</span>
                    <strong><?php echo htmlspecialchars($authLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
                </article>
                <article class="info-card">
                    <span class="label">Membre depuis</span>
                    <strong><?php echo htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?></strong>
                </article>
                <article class="info-card">
                    <span class="label">Paiement</span>
                    <strong><?php echo $cardLast4 ? '**** ' . htmlspecialchars($cardLast4, ENT_QUOTES, 'UTF-8') : 'Non renseigne'; ?></strong>
                </article>
            </section>

            <section class="library-panel">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Bibliotheque</p>
                        <h2>Derniers achats</h2>
                    </div>
                    <a href="games.html" class="btn-main btn-main--primary">Voir les jeux</a>
                </div>

                <?php if (!$purchases): ?>
                    <div class="empty-state">
                        <h3>Aucun achat pour le moment</h3>
                        <p class="muted">Explore le catalogue et ajoute ton premier jeu a ta bibliotheque.</p>
                    </div>
                <?php else: ?>
                    <div class="purchase-list">
                        <?php foreach ($purchases as $purchase): ?>
                            <article class="purchase-item">
                                <strong><?php echo htmlspecialchars((string) $purchase['titre'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <span><?php echo htmlspecialchars(date('d.m.Y', strtotime((string) $purchase['date_achat'])), ENT_QUOTES, 'UTF-8'); ?></span>
                                <span><?php echo htmlspecialchars((string) $purchase['prix_achat'], ENT_QUOTES, 'UTF-8'); ?>
                                    CHF</span>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer id="site-footer"></footer>
    <script src="js/layout.js?v=20260527-2"></script>
    <script src="js/games.js"></script>
    <script src="js/search.js"></script>
</body>

</html>