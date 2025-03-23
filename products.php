<?php
include 'includes/db.php';

$category = $_GET['category'] ?? 'all';
$sql = $category === 'all' ? "SELECT * FROM products" : "SELECT * FROM products WHERE category = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($category === 'all' ? [] : [$category]);
$products = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<h2>Nos Produits Sportifs</h2>
<nav class="categories">
    <a href="products.php?category=all">Tous</a>
    <a href="products.php?category=fitness">Fitness</a>
    <a href="products.php?category=running">Running</a>
    <a href="products.php?category=football">Football</a>
    <a href="products.php?category=yoga">Yoga</a>
</nav>

<div class="products-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <img src="assets/images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
            <h3><?= $product['name'] ?></h3>
            <p><?= $product['price'] ?> €</p>
            <a href="product_details.php?id=<?= $product['id'] ?>" class="btn">Voir plus</a>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>