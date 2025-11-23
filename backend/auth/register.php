<?php
require_once __DIR__ . '/../config.php';

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $phone = trim($_POST['phone']);
    $location = trim($_POST['location']);

    if ($name === "" || $email === "" || $password === "") {
        $errors[] = "Name, email and password are required.";
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name,email,password_hash,role,phone,location) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $name,$email,$password_hash,$role,$phone,$location);

        if ($stmt->execute()) {

            // 🌱 Auto-create reward wallet for the new user
            $newUserId = $stmt->insert_id;
            $wallet = $conn->prepare("INSERT INTO reward_wallets (user_id, balance) VALUES (?, 0)");
            $wallet->bind_param("i", $newUserId);
            $wallet->execute();

            $success = "Registration successful! You may login.";

        } else {
            $errors[] = "Email already registered.";
        }
        $stmt->close();
    }
}
?>

<?php include __DIR__ . '/../../frontend/includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow p-4">

            <h3 class="text-center mb-3 text-success fw-bold">Create Account</h3>

            <?php foreach($errors as $e): ?>
                <div class="alert alert-danger"><?php echo $e; ?></div>
            <?php endforeach; ?>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">

                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control mb-3" required>

                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control mb-3" required>

                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control mb-3" required>

                <label class="form-label">User Role</label>
                <select name="role" class="form-control mb-3">
                    <option value="household">Household / SME</option>
                    <option value="collector">Waste Collector</option>
                    <option value="admin">Administrator</option>
                </select>

                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control mb-3">

                <label class="form-label">Location / Zone</label>
                <input type="text" name="location" class="form-control mb-3">

                <button class="btn btn-success w-100">Register</button>
            </form>

            <p class="mt-3 text-center">
                Already have an account?
                <a href="/smartwastehub/backend/auth/login.php" class="text-success fw-bold">Login</a>
            </p>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../../frontend/includes/footer.php'; ?>
