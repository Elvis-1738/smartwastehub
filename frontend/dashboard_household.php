<?php
require_once __DIR__ . '/../backend/config.php';
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header('Location: /smartwastehub/backend/auth/login.php');
    exit;
}
include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Household Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
<p>Here you can request waste pickups and view your rewards.</p>

<!-- 🌱 Request Pickup Button -->
<p>
    <a class="btn btn-success" href="/smartwastehub/frontend/household_request_pickup.php">
        Request Waste Pickup
    </a>
</p>

<!-- 📋 View Pickup History Button (Add this below the first button) -->
<p>
    <a class="btn btn-outline-success" href="/smartwastehub/frontend/household_pickup_history.php">
        View Pickup History
    </a>
</p>

<?php include __DIR__ . '/includes/footer.php'; ?>
