<?php
require_once __DIR__ . '/../backend/config.php';
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header('Location: /smartwastehub/backend/auth/login.php');
    exit;
}
include __DIR__ . '/includes/header.php';
?>
<h2>Household Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
<p>Here you will request pickups and view your rewards.</p>
<?php include __DIR__ . '/includes/footer.php'; ?>
