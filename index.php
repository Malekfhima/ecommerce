<?php include 'includes/header.php'; ?>

<div class="hero">
    <h1>Bienvenue sur SportShop</h1>
    <p>Votre destination pour les meilleurs produits sportifs.</p>
    <a href="products.php" class="btn">Voir les produits</a>
</div>

<section class="featured-products">
    <h2>Produits en vedette</h2>
    <div class="products-grid">
        <?php
        include 'includes/db.php';
        $stmt = $pdo->query("SELECT * FROM products LIMIT 4");
        while ($product = $stmt->fetch()):
        ?>
            <div class="product-card">
                <img src="assets/images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                <h3><?= $product['name'] ?></h3>
                <p><?= $product['price'] ?> €</p>
                <a href="product_details.php?id=<?= $product['id'] ?>" class="btn">Voir plus</a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>