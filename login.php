<?php include 'includes/header.php'; ?>

<div class="login-container">
    <div class="login-form">
        <h2>Connexion</h2>
        <form method="POST" action="login.php">
            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password"><i class="fas fa-lock"></i> Mot de passe:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <div class="register-link">
            <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>