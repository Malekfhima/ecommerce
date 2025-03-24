<?php
include 'includes/db.php';

$productId = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    die("Produit non trouvé.");
}
?>

<?php include 'includes/header.php'; ?>

<div class="product-details">
    <img src="assets/images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
    <h1><?= $product['name'] ?></h1>
    <p><?= $product['description'] ?></p>
    <p>Prix: <?= $product['price'] ?> €</p>
    <p>Stock: <?= $product['stock'] > 0 ? 'Disponible' : 'Rupture de stock' ?></p>
    <button onclick="addToCart(<?= $product['id'] ?>)">Ajouter au panier <i class="fas fa-cart-plus"></i></button>
</div>

<?php include 'includes/footer.php'; ?>