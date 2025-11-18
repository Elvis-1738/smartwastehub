<?php
require_once __DIR__ . '/../config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'collector') {
                header('Location: /smartwastehub/frontend/dashboard_collector.php');
            } elseif ($user['role'] === 'admin') {
                header('Location: /smartwastehub/frontend/dashboard_admin.php');
            } else {
                header('Location: /smartwastehub/frontend/dashboard_household.php');
            }
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
        $stmt->close();
    }
}
?>

<?php include __DIR__ . '/../../frontend/includes/header.php'; ?>

<h2>Login</h2>

<?php foreach ($errors as $err): ?>
    <p style="color:red;"><?php echo htmlspecialchars($err); ?></p>
<?php endforeach; ?>

<form method="post">
    <label>Email:<br><input type="email" name="email" required></label><br>
    <label>Password:<br><input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>

<?php include __DIR__ . '/../../frontend/includes/footer.php'; ?>
