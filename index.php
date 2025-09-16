<?php
session_start();
require_once __DIR__ . '/includes/db.php';
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main>
    <h1>Witamy w sklepie</h1>

    <section class="najnowsze-produkty">
        <div class="section-header">
            <h2>Najnowsze produkty</h2>
            <a href="/katalog" class="see-all">Zobacz wszystkie</a>
        </div>

        <div class="product-grid">
        <?php
        $sql = "
        SELECT p.id, p.title, p.price, pi.image_url 
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.sort_order = 1
        ORDER BY p.created_at DESC
        LIMIT 6
        ";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $relativeImg = !empty($row['image_url']) ? 'assets/img/products/' . basename($row['image_url']) : null;
                    if ($relativeImg) {
                        $absolutePath = __DIR__ . '/' . $relativeImg;
                        if (file_exists($absolutePath)) {
                            $img = $relativeImg;
                        } else {
                            $img = 'assets/img/placeholder.svg';
                        }
                    } else {
                        $img = 'assets/img/placeholder.svg';
                    }
                echo '<article class="product-card">';
                echo '<a href="product.php?id=' . $row['id'] . '"><img src="' . $img . '" alt="' . htmlspecialchars($row['title']) . '" class="product-image"></a>';
                echo '<div class="product-body">';
                echo '<h3 class="product-title"><a href="product.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h3>';
                echo '<p class="product-desc">Krótki opis produktu...</p>';
                echo '<div class="product-meta">';
                echo '<div class="price">' . number_format($row['price'], 2) . ' PLN</div>';
                echo '<a class="cta-button" href="cart.php?action=add&id=' . $row['id'] . '">Dodaj do koszyka</a>';
                echo '</div>';
                echo '</div>';
                echo '</article>';
            }
        } else {
            echo "<p>Brak produktów w bazie.</p>";
        }

        ?>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
