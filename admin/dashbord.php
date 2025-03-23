<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
include '../includes/db.php';
?>

<?php include '../includes/header.php'; ?>

<h2>Tableau de Bord Administrateur</h2>
<ul>
    <li><a href="manage_products.php">Gérer les produits</a></li>
    <li><a href="manage_orders.php">Gérer les commandes</a></li>
</ul>

<?php include '../includes/footer.php'; ?>