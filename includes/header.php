<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <header>
        <div class="header-container">
            <div class="logo">
                <a href="index.php">Sklep</a>
            </div>

            <nav>
                <ul>
                    <li><a href="index.php">Strona główna</a></li>
                    <li><a href="katalog.php">Katalog</a></li>
                    <?php if (isset($_SESSION['user_id']) && ($_SESSION['is_admin'] ?? false)): ?>
                        <li><a href="add_product.php">Dodaj produkt</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="search-bar">
                <form action="search.php" method="GET">
                    <input type="text" name="q" placeholder="Szukaj produktów...">
                    <button type="submit">Szukaj</button>
                </form>
            </div>

            <div class="user-cart">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <span class="user-greeting">Witaj,
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_email']); ?>!</span>
                        <?php if ($_SESSION['is_admin'] ?? false): ?>
                            <span class="admin-badge">ADMIN</span>
                        <?php endif; ?>
                        <a href="logout.php">Wyloguj</a>
                    </div>
                <?php else: ?>
                    <a href="login.php">Zaloguj</a>
                    <a href="register.php">Rejestracja</a>
                <?php endif; ?>

                <a href="cart.php" class="cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1" />
                        <circle cx="20" cy="21" r="1" />
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                    </svg>
                    <span class="cart-badge">0</span>
                </a>
            </div>
        </div>
    </header>