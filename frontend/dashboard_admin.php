<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /smartwastehub/backend/auth/login.php');
    exit;
}

// Get overall metrics
$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='household'")->fetch_assoc()['c'] ?? 0;
$totalCollectors = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='collector'")->fetch_assoc()['c'] ?? 0;
$totalPickups = $conn->query("SELECT COUNT(*) AS c FROM pickup_requests")->fetch_assoc()['c'] ?? 0;
$totalCreditsIssued = $conn->query("SELECT COALESCE(SUM(credits),0) AS s FROM reward_transactions WHERE type='earn'")->fetch_assoc()['s'] ?? 0;
$pendingPickups = $conn->query("SELECT COUNT(*) AS c FROM pickup_requests WHERE status='pending'")->fetch_assoc()['c'] ?? 0;

// Top households by credits (demo)
$topUsers = $conn->query("
    SELECT u.id, u.name, COALESCE(SUM(rt.credits),0) AS total_credits
    FROM users u
    LEFT JOIN reward_transactions rt ON u.id = rt.user_id
    WHERE u.role='household'
    GROUP BY u.id
    ORDER BY total_credits DESC
    LIMIT 5
");

include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="fw-bold">Admin Dashboard</h2>
        <p class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>. Manage users, pickups and rewards from here.</p>
    </div>
</div>

<div class="row mt-4 g-4">
    <div class="col-md-2">
        <div class="glass-card text-center">
            <h6 class="mb-2 text-muted">Households</h6>
            <div class="h3"><?php echo number_format($totalUsers) ?></div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="glass-card text-center">
            <h6 class="mb-2 text-muted">Collectors</h6>
            <div class="h3"><?php echo number_format($totalCollectors) ?></div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="glass-card text-center">
            <h6 class="mb-2 text-muted">Pickups</h6>
            <div class="h3"><?php echo number_format($totalPickups) ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass-card text-center">
            <h6 class="mb-2 text-muted">Credits Issued</h6>
            <div class="h3 text-success"><?php echo number_format($totalCreditsIssued,2) ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass-card text-center">
            <h6 class="mb-2 text-muted">Pending Pickups</h6>
            <div class="h3 text-warning"><?php echo number_format($pendingPickups) ?></div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="glass-card">
            <h5 class="mb-3">Top Households by Credits</h5>
            <?php if ($topUsers && $topUsers->num_rows): ?>
                <ul class="list-group">
                    <?php while($u = $topUsers->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($u['name']) ?>
                            <span class="badge bg-success"><?php echo number_format($u['total_credits'],2) ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No users or transactions yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card">
            <h5 class="mb-3">Admin Actions</h5>
            <a href="/smartwastehub/frontend/admin_requests.php" class="glass-btn action-btn mb-2">View Pickup Requests</a>
            <a href="/smartwastehub/frontend/admin_users.php" class="glass-btn action-btn mb-2">Manage Users</a>
            <a href="/smartwastehub/frontend/admin_products.php" class="glass-btn action-btn mb-2">Manage Rewards</a>
            <a href="/smartwastehub/frontend/admin_page.php" class="glass-btn action-btn">Platform Settings</a>
        </div>

        <div class="glass-card mt-3">
            <h6>Quick Tips</h6>
            <ul class="mb-0">
                <li>Assign collectors from the requests page.</li>
                <li>Use the rewards panel to manage redeemable items.</li>
                <li>Export reports for grading and evaluation.</li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
