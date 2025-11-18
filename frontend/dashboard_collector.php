<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'collector') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Collector Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
<p>Here you can view and complete assigned waste pickups.</p>

<p>
    <a class="btn btn-success" href="/smartwastehub/frontend/collector_pickups.php">
        View Assigned Pickups
    </a>
</p>

<?php include __DIR__ . '/includes/footer.php'; ?>
