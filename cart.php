<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT carts.*, products.name, products.price FROM carts JOIN products ON carts.product_id = products.id WHERE carts.user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<h2>Votre Panier</h2>
<div class="cart-items">
    <?php foreach ($cartItems as $item): ?>
        <div class="cart-item">
            <h3><?= $item['name'] ?></h3>
            <p>Prix: <?= $item['price'] ?> €</p>
            <p>Quantité: <?= $item['quantity'] ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>