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

// Traitement du formulaire de paiement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $paymentMethod = $_POST['payment_method'];

    // Insérer la commande dans la base de données
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, 'pending', NOW())");
    $stmt->execute([$userId, $total]);
    $orderId = $pdo->lastInsertId();

    // Insérer les articles de la commande
    foreach ($cartItems as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
    }

    // Vider le panier
    $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Rediriger vers une page de confirmation
    header('Location: order_confirmation.php?id=' . $orderId);
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="checkout-container">
    <h2>Passer la Commande</h2>
    <div class="checkout-summary">
        <h3>Récapitulatif de la Commande</h3>
        <div class="cart-items">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="assets/images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                    <div class="cart-item-details">
                        <h4><?= $item['name'] ?></h4>
                        <p>Quantité: <?= $item['quantity'] ?></p>
                        <p>Prix: <?= $item['price'] * $item['quantity'] ?> €</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="total">
            <p>Total: <?= $total ?> €</p>
        </div>
    </div>
    <form method="POST" action="checkout.php">
        <h3>Informations de Livraison</h3>
        <label for="name">Nom complet:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="address">Adresse de livraison:</label>
        <textarea id="address" name="address" required></textarea>

        <h3>Méthode de Paiement</h3>
        <label for="payment_method">Choisissez une méthode de paiement:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="credit_card">Carte de crédit</option>
            <option value="paypal">PayPal</option>
            <option value="bank_transfer">Virement bancaire</option>
        </select>

        <button type="submit">Confirmer la Commande</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>