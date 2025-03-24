<?php
session_start();

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/db.php';

// Vérifier si l'ID du produit est fourni via POST (après soumission)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    // Récupérer les nouvelles données du formulaire
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $stock = intval($_POST['stock']);
    $imageName = $_POST['old_image']; // Conserver l'image existante par défaut

    // Vérification et mise à jour de l'image si une nouvelle est téléchargée
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageDir = '../images/';

        // Vérifier si le dossier existe, sinon le créer
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0775, true);
        }

        $newImageName = time() . '_' . basename($_FILES['image']['name']);
        $imagePath = $imageDir . $newImageName;

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                // Supprimer l'ancienne image si elle existe
                if (!empty($_POST['old_image']) && file_exists($imageDir . $_POST['old_image'])) {
                    unlink($imageDir . $_POST['old_image']);
                }
                $imageName = $newImageName;
            } else {
                echo "<p>Erreur lors de l'upload de l'image.</p>";
            }
        } else {
            echo "<p>Format d'image non valide (JPG, JPEG, PNG, GIF seulement).</p>";
        }
    }

    // Mettre à jour le produit dans la base de données
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, stock = ?, category = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $imageName, $stock, $category, $productId]);

    echo "<p style='color: green;'>Produit mis à jour avec succès !</p>";

} elseif (isset($_GET['product_id'])) {
    // Charger le produit à modifier depuis la base de données
    $productId = intval($_GET['product_id']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        die("Produit introuvable.");
    }
} else {
    die("Requête invalide.");
}
?>

<?php include 'header1.php'; ?>

<div class="admin-container">
    <h2>Modifier le Produit</h2>
    <form method="POST" action="edit_product.php" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= $productId ?>">
        <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">

        <label for="name">Nom du produit:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

        <label for="price">Prix:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= $product['price'] ?>" required>

        <label for="category">Catégorie:</label>
        <input type="text" id="category" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" value="<?= $product['stock'] ?>" required>

        <label for="image">Image actuelle:</label>
        <img src="../images/<?= htmlspecialchars($product['image']) ?>" alt="Image du produit" width="150">

        <label for="image">Nouvelle image (optionnel) :</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Modifier</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
