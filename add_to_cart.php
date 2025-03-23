<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['productId'];
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$userId, $productId]);

    echo json_encode(['message' => 'Produit ajouté au panier']);
}
?>