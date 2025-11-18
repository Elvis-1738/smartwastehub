<?php
require_once __DIR__ . '/../config.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'household';
    $phone    = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $errors[] = 'Name, email and password are required.';
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password_hash, role, phone, location)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('ssssss', $name, $email, $password_hash, $role, $phone, $location);

        try {
            if ($stmt->execute()) {
                // create reward wallet for user
                $user_id = $stmt->insert_id;
                $walletStmt = $conn->prepare(
                    "INSERT INTO reward_wallets (user_id, balance) VALUES (?, 0)"
                );
                $walletStmt->bind_param('i', $user_id);
                $walletStmt->execute();

                $success = 'Registration successful. You can now log in.';
            } else {
                $errors[] = 'Error saving user.';
            }
        } catch (mysqli_sql_exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                $errors[] = 'Email already exists.';
            } else {
                $errors[] = 'Database error.';
            }
        }

        $stmt->close();
    }
}
?>

<?php include __DIR__ . '/../../frontend/includes/header.php'; ?>

<h2>Register</h2>

<?php foreach ($errors as $err): ?>
    <p style="color:red;"><?php echo htmlspecialchars($err); ?></p>
<?php endforeach; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<form method="post">
    <label>Name:<br><input type="text" name="name" required></label><br>
    <label>Email:<br><input type="email" name="email" required></label><br>
    <label>Password:<br><input type="password" name="password" required></label><br>
    <label>Role:<br>
        <select name="role">
            <option value="household">Household / SME</option>
            <option value="collector">Collector</option>
        </select>
    </label><br>
    <label>Phone:<br><input type="text" name="phone"></label><br>
    <label>Location:<br><input type="text" name="location"></label><br>
    <button type="submit">Register</button>
</form>

<?php include __DIR__ . '/../../frontend/includes/footer.php'; ?>
