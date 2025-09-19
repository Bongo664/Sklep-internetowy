<?php
session_start();
require_once __DIR__ . '/includes/db.php';
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main>
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Odkryj najlepsze produkty</h1>
                <p class="hero-subtitle">Szeroki wybór wysokiej jakości produktów w najlepszych cenach. Znajdź wszystko
                    czego potrzebujesz w jednym miejscu.</p>
                <a href="/katalog" class="hero-button">Przeglądaj katalog</a>
            </div>
            <div class="hero-visual">
                <div class="hero-decoration"></div>
            </div>
        </div>
    </section>

    <section class="categories-preview">
        <div class="container">
            <h2>Popularne kategorie</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon">📱</div>
                    <h3>Elektronika</h3>
                    <p>Najnowsze gadżety i urządzenia</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">💻</div>
                    <h3>Laptopy i komputery</h3>
                    <p>Sprzęt do pracy i rozrywki</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">🏠</div>
                    <h3>Dom i ogród</h3>
                    <p>Wszystko dla Twojego domu</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">🎮</div>
                    <h3>Rozrywka</h3>
                    <p>Gry, książki i multimedia</p>
                </div>
            </div>
        </div>
    </section>

    <section class="najnowsze-produkty">
        <div class="container">
            <div class="section-header">
                <h2>Najnowsze produkty</h2>
                <a href="/katalog" class="see-all-button">
                    Zobacz wszystkie
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="product-carousel">
                <button class="carousel-arrow carousel-arrow-left" id="prevProducts" onclick="slideProducts(-1)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                </button>

                <div class="product-carousel-container">
                    <div class="product-grid" id="productCarousel">
                        <?php
                        $sql = "
                    SELECT p.id, p.title, p.price, p.short_description, pi.image_url, i.quantity_in_stock
                    FROM products p
                    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.sort_order = 1
                    LEFT JOIN inventory i ON p.id = i.product_id
                    ORDER BY p.created_at DESC
                    LIMIT 10
                    ";

                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $relativeImg = !empty($row['image_url']) ? 'assets/img/products/' . basename($row['image_url']) : null;
                                $img = ($relativeImg && file_exists(__DIR__ . '/' . $relativeImg)) ? $relativeImg : 'assets/img/placeholder.svg';

                                $quantity = isset($row['quantity_in_stock']) ? (int) $row['quantity_in_stock'] : 0;
                                $imgStyle = ($quantity == 0) ? 'style="filter: grayscale(80%); opacity: 0.5;"' : '';

                                echo '<article class="product-card">';
                                echo '<a href="product.php?id=' . $row['id'] . '" class="product-image-link">';
                                echo '<div class="product-image-container">';
                                echo '<img src="' . $img . '" alt="' . htmlspecialchars($row['title']) . '" class="product-image" ' . $imgStyle . '>';
                                if ($quantity == 0) {
                                    echo '<div class="out-of-stock-badge">Brak w magazynie</div>';
                                } elseif ($quantity <= 3) {
                                    echo '<div class="low-stock-badge">Ostatnie sztuki</div>';
                                }
                                echo '</div>';
                                echo '</a>';
                                echo '<div class="product-body">';
                                echo '<h3 class="product-title">' . htmlspecialchars($row['title']) . '</h3>';
                                echo '<p class="product-desc">' . htmlspecialchars($row['short_description']) . '</p>';
                                echo '<div class="product-meta">';
                                echo '<div class="price">' . number_format($row['price'], 2) . ' PLN</div>';
                                if ($quantity > 0) {
                                    echo '<a class="cta-button" href="cart.php?action=add&id=' . $row['id'] . '">';
                                    echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
                                    echo '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>';
                                    echo '<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>';
                                    echo '</svg>';
                                    echo 'Dodaj do koszyka';
                                    echo '</a>';
                                } else {
                                    echo '<button class="cta-button disabled" disabled>Brak w magazynie</button>';
                                }
                                echo '</div>';
                                echo '</div>';
                                echo '</article>';
                            }
                        } else {
                            echo '<div class="no-products">';
                            echo '<div class="no-products-icon">📦</div>';
                            echo '<p>Brak produktów w bazie danych</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <button class="carousel-arrow carousel-arrow-right" id="nextProducts" onclick="slideProducts(1)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6" />
                    </svg>
                </button>
            </div>

            <div class="carousel-dots" id="carouselDots"></div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3>Darmowa dostawa</h3>
                    <p>Przy zamówieniach powyżej 200 PLN</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Bezpieczne płatności</h3>
                    <p>SSL i szyfrowanie danych</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">↩️</div>
                    <h3>30 dni na zwrot</h3>
                    <p>Bez dodatkowych pytań</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Wsparcie 24/7</h3>
                    <p>Jesteśmy zawsze do dyspozycji</p>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    let currentSlide = 0;
    let totalProducts = 0;
    let productsPerSlide = 3;
    let totalSlides = 0;

    document.addEventListener('DOMContentLoaded', function () {
        initializeCarousel();
    });

    function initializeCarousel() {
        const productCards = document.querySelectorAll('#productCarousel .product-card');
        totalProducts = productCards.length;
        totalSlides = Math.ceil(totalProducts / productsPerSlide);

        if (totalProducts <= productsPerSlide) {
            document.querySelector('.carousel-arrow-left').style.display = 'none';
            document.querySelector('.carousel-arrow-right').style.display = 'none';
            document.getElementById('carouselDots').style.display = 'none';
            return;
        }

        createDots();
        updateCarouselPosition();
        updateArrows();
    }

    function createDots() {
        const dotsContainer = document.getElementById('carouselDots');
        dotsContainer.innerHTML = '';

        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot';
            dot.onclick = () => goToSlide(i);
            dotsContainer.appendChild(dot);
        }

        updateDots();
    }

    function slideProducts(direction) {
        currentSlide += direction;

        if (currentSlide >= totalSlides) {
            currentSlide = 0;
        } else if (currentSlide < 0) {
            currentSlide = totalSlides - 1;
        }

        updateCarouselPosition();
        updateArrows();
        updateDots();
    }

    function goToSlide(slideIndex) {
        currentSlide = slideIndex;
        updateCarouselPosition();
        updateArrows();
        updateDots();
    }

    function updateCarouselPosition() {
        const carousel = document.getElementById('productCarousel');
        const translateX = -(currentSlide * 100);
        carousel.style.transform = `translateX(${translateX}%)`;
    }

    function updateArrows() {
        const leftArrow = document.querySelector('.carousel-arrow-left');
        const rightArrow = document.querySelector('.carousel-arrow-right');

        leftArrow.style.opacity = '1';
        rightArrow.style.opacity = '1';
    }

    function updateDots() {
        const dots = document.querySelectorAll('.carousel-dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }


    window.addEventListener('resize', function () {
        if (window.innerWidth <= 768) {
            productsPerSlide = 1;
        } else if (window.innerWidth <= 1024) {
            productsPerSlide = 2;
        } else {
            productsPerSlide = 3;
        }

        totalSlides = Math.ceil(totalProducts / productsPerSlide);

        if (currentSlide >= totalSlides) {
            currentSlide = 0;
        }

        createDots();
        updateCarouselPosition();
    });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>