<?php
session_start();
require_once __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

if (!isset($_GET['id'])) {
    echo "<main><p>Nie wybrano produktu.</p></main>";
    include __DIR__ . '/includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];

$sql = "SELECT id, title, description, price FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "<main><p>Produkt nie istnieje.</p></main>";
    include __DIR__ . '/includes/footer.php';
    exit;
}

$sql_images = "SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order ASC";
$stmt_img = $conn->prepare($sql_images);
$stmt_img->bind_param("i", $id);
$stmt_img->execute();
$images = $stmt_img->get_result();

$sql_stock = "SELECT quantity_in_stock FROM inventory WHERE product_id = ?";
$stmt_stock = $conn->prepare($sql_stock);
$stmt_stock->bind_param("i", $id);
$stmt_stock->execute();
$stock = $stmt_stock->get_result()->fetch_assoc();
$quantity = $stock ? (int)$stock['quantity_in_stock'] : 0;

$sql_specs = "SELECT spec_name, spec_value FROM product_specs WHERE product_id = ? ORDER BY id ASC";
$stmt_specs = $conn->prepare($sql_specs);
$stmt_specs->bind_param("i", $id);
$stmt_specs->execute();
$specs = $stmt_specs->get_result();

$sql_recommended = "SELECT p.id, p.title, p.price, pi.image_url 
FROM products p 
LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.sort_order = 1
WHERE p.id != ? 
ORDER BY RAND() 
LIMIT 4";
$stmt_recommended = $conn->prepare($sql_recommended);
$stmt_recommended->bind_param("i", $id);
$stmt_recommended->execute();
$recommended = $stmt_recommended->get_result();
?>

<main class="product-page">
    <div class="product-gallery">
        <div class="main-image">
            <?php
            if ($images->num_rows > 0) {
                $images->data_seek(0); 
                $firstImg = $images->fetch_assoc();
                $mainImg = 'assets/img/products/' . basename($firstImg['image_url']);
            } else {
                $mainImg = 'assets/img/placeholder.png';
            }
            ?>
            <img src="<?php echo $mainImg; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" id="mainProductImage">
        </div>
        <div class="thumbnails">
            <?php
            $images->data_seek(0); 
            while ($row = $images->fetch_assoc()) {
                $thumb = 'assets/img/products/' . basename($row['image_url']);
                echo '<img src="' . $thumb . '" alt="' . htmlspecialchars($product['title']) . '" class="thumbnail">';
            }
            ?>
        </div>
    </div>

    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['title']); ?></h1>
        <p class="product-price"><?php echo number_format($product['price'], 2); ?> PLN</p>
        <p class="product-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <p class="product-stock">
        <?php 
        if ($quantity > 5) {
            echo '<span style="color:green;">Dostępny</span>';
        } elseif ($quantity > 0) {
            echo '<span style="color:orange;">Ograniczona ilość</span>';
        } else {
            echo '<span style="color:red;">Brak w magazynie</span>';
        }
        ?>
        </p>
        <?php if ($quantity > 0): ?>
        <a href="cart.php?action=add&id=<?php echo $product['id']; ?>" class="cta-button">Dodaj do koszyka</a>
        <?php else: ?>
        <button class="cta-button disabled" disabled>Brak w magazynie</button>
        <?php endif; ?>

        <?php if ($specs && $specs->num_rows > 0): ?>
        <div class="product-specs">
            <h2>Specyfikacja produktu</h2>
            <table>
                <tbody>
                    <?php while ($spec = $specs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($spec['spec_name']); ?></td>
                        <td><?php echo htmlspecialchars($spec['spec_value']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php if ($recommended && $recommended->num_rows > 0): ?>
<section class="recommended-section">
    <div class="container">
        <h2>Polecane produkty</h2>
        <div class="recommended-grid">
            <?php while ($item = $recommended->fetch_assoc()): ?>
            <div class="recommended-item">
                <a href="product.php?id=<?php echo $item['id']; ?>" class="recommended-link">
                    <div class="recommended-image">
                        <?php 
                        $itemImg = $item['image_url'] ? 'assets/img/products/' . basename($item['image_url']) : 'assets/img/placeholder.png';
                        ?>
                        <img src="<?php echo $itemImg; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="product-image">
                    </div>
                </a>
                <div class="recommended-info">
                    <h3 class="recommended-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="recommended-price"><?php echo number_format($item['price'], 2); ?> PLN</p>
                    <a href="product.php?id=<?php echo $item['id']; ?>" class="recommended-button">Zobacz produkt</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
    <?php include __DIR__ . '/assets/app.js'; ?>
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>