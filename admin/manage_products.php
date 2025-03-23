<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $category, $stock, $image]);
}
?>

<?php include '../includes/header.php'; ?>

<h2>Gérer les Produits</h2>
<form method="POST" action="manage_products.php">
    <label for="name">Nom:</label>
    <input type="text" id="name" name="name" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea>

    <label for="price">Prix:</label>
    <input type="number" id="price" name="price" step="0.01" required>

    <label for="category">Catégorie:</label>
    <input type="text" id="category" name="category" required>

    <label for="stock">Stock:</label>
    <input type="number" id="stock" name="stock" required>

    <label for="image">Image:</label>
    <input type="text" id="image" name="image" required>

    <button type="submit">Ajouter</button>
</form>

<?php include '../includes/footer.php'; ?>