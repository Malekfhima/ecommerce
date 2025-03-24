<?php
// Sécurité et vérification des droits d'accès
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Vérifier que l'utilisateur est admin
require_admin();

// Titre de la page
$page_title = "Gestion des produits";

// Inclure l'en-tête
include '../includes/header.php';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout d'un nouveau produit
    if (isset($_POST['add_product'])) {
        $name = sanitize_input($_POST['name']);
        $description = sanitize_input($_POST['description']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $stock = intval($_POST['stock']);
        $active = isset($_POST['active']) ? 1 : 0;

        // Gestion de l'upload d'image
        $image_name = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/products/';
            $image_name = basename($_FILES['image']['name']);
            $image_path = $upload_dir . $image_name;
            
            // Vérifier le type de fichier
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $error = "Type de fichier non autorisé. Seuls JPEG, PNG et GIF sont acceptés.";
            }
        }

        if (!isset($error)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO products (name, description, price, category_id, stock, image, active, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$name, $description, $price, $category_id, $stock, $image_name, $active]);
                
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'Produit ajouté avec succès!'
                ];
                header('Location: products.php');
                exit();
            } catch (PDOException $e) {
                error_log("Erreur d'ajout de produit: " . $e->getMessage());
                $error = "Erreur lors de l'ajout du produit.";
            }
        }
    }
    
    // Mise à jour d'un produit
    if (isset($_POST['update_product'])) {
        $id = intval($_POST['product_id']);
        $name = sanitize_input($_POST['name']);
        $description = sanitize_input($_POST['description']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $stock = intval($_POST['stock']);
        $active = isset($_POST['active']) ? 1 : 0;

        // Gestion de l'image (mise à jour facultative)
        $image_update = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/products/';
            $image_name = basename($_FILES['image']['name']);
            $image_path = $upload_dir . $image_name;
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                    $image_update = ", image = ?";
                } else {
                    $error = "Erreur lors de l'upload de la nouvelle image.";
                }
            } else {
                $error = "Type de fichier non autorisé. Seuls JPEG, PNG et GIF sont acceptés.";
            }
        }

        if (!isset($error)) {
            try {
                if ($image_update) {
                    $sql = "UPDATE products SET 
                            name = ?, description = ?, price = ?, category_id = ?, 
                            stock = ?, active = ? $image_update 
                            WHERE id = ?";
                    $params = [$name, $description, $price, $category_id, $stock, $active, $image_name, $id];
                } else {
                    $sql = "UPDATE products SET 
                            name = ?, description = ?, price = ?, category_id = ?, 
                            stock = ?, active = ? 
                            WHERE id = ?";
                    $params = [$name, $description, $price, $category_id, $stock, $active, $id];
                }
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'Produit mis à jour avec succès!'
                ];
                header('Location: products.php');
                exit();
            } catch (PDOException $e) {
                error_log("Erreur de mise à jour de produit: " . $e->getMessage());
                $error = "Erreur lors de la mise à jour du produit.";
            }
        }
    }
}

// Suppression d'un produit (via GET pour simplifier)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    try {
        // Ne supprimez pas physiquement le produit, marquez-le comme inactif
        $stmt = $pdo->prepare("UPDATE products SET active = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Produit désactivé avec succès!'
        ];
        header('Location: products.php');
        exit();
    } catch (PDOException $e) {
        error_log("Erreur de suppression de produit: " . $e->getMessage());
        $error = "Erreur lors de la désactivation du produit.";
    }
}

// Récupérer la liste des produits
try {
    $search = isset($_GET['search']) ? '%' . sanitize_input($_GET['search']) . '%' : '%';
    $category_filter = isset($_GET['category']) ? intval($_GET['category']) : null;
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE ?";
    
    $params = [$search];
    
    if ($category_filter) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_filter;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Récupérer les catégories pour le filtre et le formulaire
    $categories = $pdo->query("SELECT * FROM categories WHERE active = 1 ORDER BY name")->fetchAll();
    
} catch (PDOException $e) {
    error_log("Erreur de récupération des produits: " . $e->getMessage());
    $error = "Erreur lors du chargement des produits.";
}

// Récupérer le produit à éditer (si édition)
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $edit_product = $stmt->fetch();
        
        if (!$edit_product) {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Produit non trouvé!'
            ];
            header('Location: products.php');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erreur de récupération du produit: " . $e->getMessage());
        $error = "Erreur lors du chargement du produit.";
    }
}
?>

<!-- Inclure la sidebar admin -->
<?php include 'sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestion des produits</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-circle"></i> Ajouter un produit
            </button>
        </div>
    </div>

    <!-- Alertes et notifications -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?> alert-dismissible fade show">
            <?php echo $_SESSION['flash_message']['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="products.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Rechercher un produit..." 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="category">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                    <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des produits -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Aucun produit trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <?php if ($product['image']): ?>
                                            <img src="../images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="img-thumbnail" width="50">
                                        <?php else: ?>
                                            <span class="text-muted">Aucune image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Non catégorisé'); ?></td>
                                    <td><?php echo format_price($product['price']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['stock'] > 0 ? 'success' : 'danger'; ?>">
                                            <?php echo $product['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['active'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $product['active'] ? 'Actif' : 'Inactif'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="products.php?delete=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir désactiver ce produit?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal d'ajout/édition de produit -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" 
                  action="products.php<?php echo $edit_product ? '?edit=' . $edit_product['id'] : ''; ?>">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?php echo $edit_product ? 'Modifier le produit' : 'Ajouter un nouveau produit'; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="update_product" value="1">
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_product" value="1">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom du produit *</label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php 
                                    echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; 
                                ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Prix *</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="price" name="price" required
                                                   value="<?php echo $edit_product ? htmlspecialchars($edit_product['price']) : ''; ?>">
                                            <span class="input-group-text">€</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Stock *</label>
                                        <input type="number" class="form-control" id="stock" name="stock" required
                                               value="<?php echo $edit_product ? htmlspecialchars($edit_product['stock']) : '0'; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Catégorie</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Non catégorisé</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                            <?php echo ($edit_product && $edit_product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="active" name="active" 
                                    <?php echo ($edit_product && $edit_product['active']) || !$edit_product ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="active">Produit actif</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image du produit</label>
                                <?php if ($edit_product && $edit_product['image']): ?>
                                    <div class="mb-2">
                                        <img src="../images/products/<?php echo htmlspecialchars($edit_product['image']); ?>" 
                                             alt="Image actuelle" class="img-thumbnail w-100">
                                        <small class="text-muted">Image actuelle</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_product ? 'Mettre à jour' : 'Ajouter'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Afficher automatiquement le modal si on est en mode édition
<?php if ($edit_product): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var productModal = new bootstrap.Modal(document.getElementById('productModal'));
        productModal.show();
    });
<?php endif; ?>
</script>

<?php
// Inclure le pied de page
include '../includes/footer.php';
?>