<?php
session_start();

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/db.php';
?>

<?php include 'header1.php'; ?>

<div class="admin-dashboard">
    <h2>Tableau de Bord Administrateur</h2>
    <ul>
        <li><a href="add_product.php"><i class="fas fa-plus"></i> Ajouter un Produit</a></li>
        <li><a href="manage_products.php"><i class="fas fa-box"></i> Gérer les Produits</a></li>
        <li><a href="manage_orders.php"><i class="fas fa-receipt"></i> Gérer les Commandes</a></li>
    </ul>
</div>

<?php include '../includes/footer.php'; ?>