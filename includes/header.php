<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Sklep internetowy</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php">Mój Sklep</a>
        </div>

        <div class="search-bar">
            <form action="products.php" method="get">
                <input type="text" name="q" placeholder="Szukaj produktów...">
                <button type="submit">Szukaj</button>
            </form>
        </div>
        <div class="user-cart">
            <a href="cart.php" class="cart" aria-label="Koszyk">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 6h15l-1.5 9h-12L6 6z" stroke="#111827" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <path d="M9 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM18 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" fill="#111827"/>
                </svg>
                <span id="cart-count" class="cart-badge">0</span>
            </a>
            <a href="login.php" class="login">Logowanie</a>
        </div>
    </div>
</header>