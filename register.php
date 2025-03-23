<?php include 'includes/header.php'; ?>

<div class="register-container">
    <div class="register-form">
        <h2>Inscription</h2>
        <form method="POST" action="register.php">
            <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required>

            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password"><i class="fas fa-lock"></i> Mot de passe:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">S'inscrire</button>
        </form>
        <div class="login-link">
            <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>