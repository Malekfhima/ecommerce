
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
            <a href="products.php"><i class="fas fa-store"></i> Produits</a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Panier</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                <a href="register.php"><i class="fas fa-user-plus"></i> Inscription</a>
            <?php endif; ?>
        </nav>
    </header>