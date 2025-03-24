<?php
session_start();

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/db.php';

// Récupérer toutes les commandes
$stmt = $pdo->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id");
$orders = $stmt->fetchAll();
?>

<?php include 'header1.php'; ?>

<div class="admin-container">
    <h2>Gérer les Commandes</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['username'] ?></td>
                    <td><?= $order['total_price'] ?> €</td>
                    <td><?= $order['status'] ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td>
                        <a href="view_order.php?id=<?= $order['id'] ?>">Voir</a>
                        <a href="update_order.php?id=<?= $order['id'] ?>">Modifier</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>