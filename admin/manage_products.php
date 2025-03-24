<?php
session_start();

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/db.php';

// Récupérer tous les produits
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<?php include 'header1.php'; ?>

<div class="admin-container">
    <h2>Gérer les Produits</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= $product['name'] ?></td>
                    <td><?= $product['price'] ?> €</td>
                    <td><?= $product['stock'] ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id'] ?>">Modifier</a>
                        <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>