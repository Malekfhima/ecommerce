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
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $stock = intval($_POST['stock']);
    
    // Vérification et création du dossier images/
    $imageDir = '../images/';
    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0775, true);
    }

    // Gestion de l'upload d'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $imagePath = $imageDir . $imageName;

        // Vérifier l'extension du fichier
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "<p>Format d'image non valide. Seuls JPG, JPEG, PNG et GIF sont autorisés.</p>";
        } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            // Insérer le produit dans la base de données
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, stock, category) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $imageName, $stock, $category]);

            echo "<p style='color: green;'>Produit ajouté avec succès !</p>";
        } else {
            echo "<p>Erreur lors de l'upload de l'image.</p>";
        }
    } else {
        echo "<p>Aucune image sélectionnée ou erreur d'upload.</p>";
    }
}
?>

<?php include 'header1.php'; ?>

<div class="admin-container">
    <h2>Ajouter un Produit</h2>
    <form method="POST" action="add_product.php" enctype="multipart/form-data">
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

        <label for="image">Image du produit:</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <button type="submit">Ajouter</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
