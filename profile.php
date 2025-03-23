<?php
session_start();

// Rediriger l'utilisateur s'il n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

// Récupérer les informations de l'utilisateur
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Mettre à jour les informations de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];

    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
    $stmt->execute([$username, $email, $password, $userId]);

    // Rafraîchir les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    echo "<p>Profil mis à jour avec succès !</p>";
}
?>

<?php include 'includes/header.php'; ?>

<div class="profile-container">
    <h2>Profil de <?= $user['username'] ?></h2>
    <form method="POST" action="profile.php">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" value="<?= $user['username'] ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required>

        <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer):</label>
        <input type="password" id="password" name="password">

        <button type="submit">Mettre à jour</button>
    </form>

    <h3>Historique des Commandes</h3>
    <div class="orders-history">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();

        if (count($orders) > 0):
            foreach ($orders as $order):
        ?>
                <div class="order">
                    <p>Commande #<?= $order['id'] ?></p>
                    <p>Total: <?= $order['total_price'] ?> €</p>
                    <p>Statut: <?= $order['status'] ?></p>
                    <p>Date: <?= $order['created_at'] ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune commande passée pour le moment.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>