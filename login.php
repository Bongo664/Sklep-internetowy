<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Proszę wypełnić wszystkie pola.';
    } else {
        $sql = "SELECT id, email, password_hash, full_name, is_admin FROM customers WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['is_admin'] = (bool) $user['is_admin'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Nieprawidłowe dane logowania.';
            }
        } else {
            $error = 'Nieprawidłowe dane logowania.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Logowanie</h1>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Hasło:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="auth-button">Zaloguj się</button>
            </form>

            <p class="auth-link">
                Nie masz konta? <a href="register.php">Zarejestruj się</a>
            </p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>