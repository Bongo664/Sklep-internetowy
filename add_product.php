<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

$categories_sql = "SELECT id, name FROM product_categories ORDER BY name";
$categories_result = $conn->query($categories_sql);

$manufacturers_sql = "SELECT id, name FROM manufacturers ORDER BY name";
$manufacturers_result = $conn->query($manufacturers_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $manufacturer_id = (int)($_POST['manufacturer_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $short_description = trim($_POST['short_description'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $sku = trim($_POST['sku'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);
    
    if (empty($title) || empty($short_description) || empty($description) || $price <= 0) {
        $error = 'Proszę wypełnić wszystkie wymagane pola i podać prawidłową cenę.';
    } elseif ($category_id <= 0 || $manufacturer_id <= 0) {
        $error = 'Proszę wybrać kategorię i producenta.';
    } else {
        if (empty($sku)) {
            $sku = 'USR-' . strtoupper(substr(md5($title . time()), 0, 8));
        }
        
        $sku_check = "SELECT id FROM products WHERE sku = ?";
        $stmt = $conn->prepare($sku_check);
        $stmt->bind_param("s", $sku);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Produkt o podanym SKU już istnieje.';
        } else {
            $sql = "INSERT INTO products (sku, title, manufacturer_id, category_id, short_description, description, price, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiissd", $sku, $title, $manufacturer_id, $category_id, $short_description, $description, $price);
            
            if ($stmt->execute()) {
                $product_id = $conn->insert_id;
                
                $inventory_sql = "INSERT INTO inventory (product_id, quantity_in_stock) VALUES (?, ?)";
                $inventory_stmt = $conn->prepare($inventory_sql);
                $inventory_stmt->bind_param("ii", $product_id, $quantity);
                $inventory_stmt->execute();
                
                if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = 'assets/img/products/';
                    $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
                    $new_filename = $sku . '-1.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($file_extension, $allowed_extensions) && $_FILES['product_image']['size'] < 5000000) {
                        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                            $image_sql = "INSERT INTO product_images (product_id, image_url, alt_text, sort_order) VALUES (?, ?, ?, 1)";
                            $image_stmt = $conn->prepare($image_sql);
                            $image_url = '/img/products/' . $new_filename;
                            $alt_text = $title;
                            $image_stmt->bind_param("iss", $product_id, $image_url, $alt_text);
                            $image_stmt->execute();
                        }
                    }
                }
                
                $success = 'Produkt został pomyślnie dodany! <a href="product.php?id=' . $product_id . '">Zobacz produkt</a>';
            } else {
                $error = 'Wystąpił błąd podczas dodawania produktu. Spróbuj ponownie.';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main>
    <div class="form-container">
        <div class="form-card">
            <h1>Dodaj nowy produkt</h1>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php else: ?>
            
            <form method="POST" enctype="multipart/form-data" class="product-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Nazwa produktu: *</label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="sku">SKU (opcjonalne):</label>
                        <input type="text" id="sku" name="sku" 
                               value="<?php echo htmlspecialchars($_POST['sku'] ?? ''); ?>"
                               placeholder="Zostanie wygenerowane automatycznie">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="manufacturer_id">Producent: *</label>
                        <select id="manufacturer_id" name="manufacturer_id" required>
                            <option value="">Wybierz producenta</option>
                            <?php while ($manufacturer = $manufacturers_result->fetch_assoc()): ?>
                                <option value="<?php echo $manufacturer['id']; ?>"
                                        <?php echo (isset($_POST['manufacturer_id']) && $_POST['manufacturer_id'] == $manufacturer['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($manufacturer['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Kategoria: *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Wybierz kategorię</option>
                            <?php while ($category = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $category['id']; ?>"
                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Cena (PLN): *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required 
                               value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Ilość w magazynie:</label>
                        <input type="number" id="quantity" name="quantity" min="0" 
                               value="<?php echo htmlspecialchars($_POST['quantity'] ?? '0'); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="short_description">Krótki opis: *</label>
                    <textarea id="short_description" name="short_description" rows="2" required 
                              placeholder="Krótki opis produktu (wyświetlany na liście produktów)"><?php echo htmlspecialchars($_POST['short_description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="description">Pełny opis: *</label>
                    <textarea id="description" name="description" rows="6" required 
                              placeholder="Szczegółowy opis produktu"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="product_image">Zdjęcie produktu:</label>
                    <input type="file" id="product_image" name="product_image" 
                           accept="image/jpeg,image/jpg,image/png,image/gif">
                    <small>Maksymalny rozmiar: 5MB. Obsługiwane formaty: JPG, PNG, GIF</small>
                </div>
                
                <button type="submit" class="submit-button">Dodaj produkt</button>
            </form>
            
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>