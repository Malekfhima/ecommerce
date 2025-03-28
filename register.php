<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role']; // Récupérer le rôle depuis le formulaire

    // Insérer l'utilisateur dans la base de données
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);

    header('Location: login.php');
}
?>

<?php include 'includes/header.php'; ?>

<div class="register-container">
    <div class="register-form">
        <h2>Inscription</h2>
        <form method="POST" action="register.php">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>

            <label for="role">Rôle:</label>
            <select id="role" name="role" required>
                <option value="customer">Client</option>
                <option value="admin">Administrateur</option>
            </select>

            <button type="submit">S'inscrire</button>
        </form>
        <div class="login-link">
            <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>