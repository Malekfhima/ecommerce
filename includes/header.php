<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportShop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="products.php">Produits</a>
            <a href="cart.php">Panier</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profil</a>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
            <?php endif; ?>
        </nav>
    </header>