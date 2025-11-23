<?php
require_once __DIR__ . '/../config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password_hash, role FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Admin can log in using plain password
    if ($user && (
        ($user["role"] === "admin" && $password === $user["password_hash"]) ||
        password_verify($password, $user["password_hash"])
    )) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];
        $_SESSION["user_role"] = $user["role"];

        if ($user["role"] === "collector") {
            header("Location: /smartwastehub/frontend/dashboard_collector.php");
        } elseif ($user["role"] === "admin") {
            header("Location: /smartwastehub/frontend/dashboard_admin.php");
        } else {
            header("Location: /smartwastehub/frontend/dashboard_household.php");
        }
        exit;
    } else {
        $errors[] = "Invalid email or password.";
    }
}
?>

<?php include __DIR__ . '/../../frontend/includes/header.php'; ?>

<style>
/* LOGIN CARD */
.login-box {
    background: #ffffff;
    border-radius: 18px;
    padding: 35px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

/* ROLE BADGES */
.role-badges span {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    margin-right: 6px;
    font-weight: 600;
}

.badge-household { background: #22c55e; color: white; }
.badge-collector { background: #0ea5e9; color: white; }
.badge-admin { background: #a855f7; color: white; }
</style>

<div class="row justify-content-center mt-4">
    <div class="col-md-5">
        <div class="login-box">

            <h3 class="text-center mb-2 text-success fw-bold">Login</h3>

            <p class="text-center text-muted" style="font-size: 0.9rem;">
                This system is used by:
            </p>

            <!-- Role badges -->
            <div class="role-badges text-center mb-3">
                <span class="badge-household">Households</span>
                <span class="badge-collector">Collectors</span>
                <span class="badge-admin">Administrators</span>
            </div>

            <?php foreach($errors as $e): ?>
                <div class="alert alert-danger"><?php echo $e; ?></div>
            <?php endforeach; ?>

            <form method="POST">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control mb-3" required>

                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control mb-3" required>

                <button class="btn btn-success w-100">Login</button>
            </form>

            <p class="mt-3 text-center">
                No account?  
                <a href="/smartwastehub/backend/auth/register.php" class="text-success fw-bold">
                    Register
                </a>
            </p>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../../frontend/includes/footer.php'; ?>
