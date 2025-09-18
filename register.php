<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Proszę wypełnić wszystkie wymagane pola.';
    } elseif ($password !== $confirm_password) {
        $error = 'Hasła nie są identyczne.';
    } elseif (strlen($password) < 6) {
        $error = 'Hasło musi mieć co najmniej 6 znaków.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Proszę podać prawidłowy adres email.';
    } else {
        $sql = "SELECT id FROM customers WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Użytkownik z tym adresem email już istnieje.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO customers (email, password_hash, full_name, phone, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $email, $password_hash, $full_name, $phone);

            if ($stmt->execute()) {
                $success = 'Konto zostało utworzone! Możesz się teraz zalogować.';
            } else {
                $error = 'Wystąpił błąd podczas tworzenia konta. Spróbuj ponownie.';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Rejestracja</h1>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <p class="auth-link">
                    <a href="login.php">Przejdź do logowania</a>
                </p>
            <?php else: ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="full_name">Imię i nazwisko: *</label>
                        <input type="text" id="full_name" name="full_name" required
                            value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email: *</label>
                        <input type="email" id="email" name="email" required
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon:</label>
                        <input type="tel" id="phone" name="phone"
                            value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Hasło: *</label>
                        <input type="password" id="password" name="password" required minlength="6">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Potwierdź hasło: *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>

                    <button type="submit" class="auth-button">Zarejestruj się</button>
                </form>

            <?php endif; ?>

            <p class="auth-link">
                Masz już konto? <a href="login.php">Zaloguj się</a>
            </p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>