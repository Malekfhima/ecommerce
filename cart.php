<?php
session_start();

// Rediriger l'utilisateur s'il n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

// Récupérer les articles du panier
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT carts.*, products.name, products.price, products.image FROM carts JOIN products ON carts.product_id = products.id WHERE carts.user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

// Calculer le total du panier
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<?php include 'includes/header.php'; ?>

<div class="cart-container">
    <h2>Votre Panier</h2>
    <div class="cart-items">
        <?php if (count($cartItems) > 0): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="assets/images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                    <div class="cart-item-details">
                        <h3><?= $item['name'] ?></h3>
                        <p>Prix unitaire: <?= $item['price'] ?> TDN</p>
                        <div class="quantity">
                            <button onclick="updateQuantity(<?= $item['id'] ?>, 'decrease')">-</button>
                            <input type="text" value="<?= $item['quantity'] ?>" readonly>
                            <button onclick="updateQuantity(<?= $item['id'] ?>, 'increase')">+</button>
                        </div>
                    </div>
                    <button class="remove-btn" onclick="removeFromCart(<?= $item['id'] ?>)">Supprimer</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Votre panier est vide.</p>
        <?php endif; ?>
    </div>
    <div class="cart-summary">
        <h3>Total: <?= $total ?> €</h3>
        <button class="checkout-btn">Passer la commande</button>
    </div>
</div>

<?php include 'includes/footer.php'; ?>