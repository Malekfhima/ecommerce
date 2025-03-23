<?php
session_start();

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/db.php';

// Traitement du formulaire d'ajout de produit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    // Insérer le produit dans la base de données
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $category, $stock, $image]);

    echo "<p>Produit ajouté avec succès !</p>";
}
?>

<?php include '../includes/header.php'; ?>

<div class="admin-container">
    <h2>Ajouter un Produit</h2>
    <form method="POST" action="add_product.php">
        <label for="name">Nom du produit:</label>
        <input type="text" id="name" name="name" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="price">Prix:</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="category">Catégorie:</label>
        <input type="text" id="category" name="category" required>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required>

        <label for="image">URL de l'image:</label>
        <input type="text" id="image" name="image" required>

        <button type="submit">Ajouter</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>