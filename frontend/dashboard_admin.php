<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /smartwastehub/backend/auth/login.php');
    exit;
}

// Overall metrics
$totalUsers         = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='household'")->fetch_assoc()['c'] ?? 0;
$totalCollectors    = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='collector'")->fetch_assoc()['c'] ?? 0;
$totalPickups       = $conn->query("SELECT COUNT(*) AS c FROM pickup_requests")->fetch_assoc()['c'] ?? 0;
$totalCreditsIssued = $conn->query("SELECT COALESCE(SUM(credits),0) AS s FROM reward_transactions WHERE type='earn'")->fetch_assoc()['s'] ?? 0;
$pendingPickups     = $conn->query("SELECT COUNT(*) AS c FROM pickup_requests WHERE status='pending'")->fetch_assoc()['c'] ?? 0;

// Top households by credits
$topUsers = $conn->query("
    SELECT u.id, u.name, COALESCE(SUM(rt.credits),0) AS total_credits
    FROM users u
    LEFT JOIN reward_transactions rt ON u.id = rt.user_id
    WHERE u.role = 'household'
    GROUP BY u.id
    ORDER BY total_credits DESC
    LIMIT 5
");

include __DIR__ . '/includes/header.php';
?>

<style>
    /* Gradient hero header to match other dashboards */
    .hero-header {
        background: linear-gradient(135deg, #0ea5e9, #16a34a);
        border-radius: 15px;
        padding: 35px;
        color: #fff;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    /* Stat cards */
    .stat-card {
        border-radius: 18px;
        padding: 25px;
        color: #fff;
        box-shadow: 0 6px 14px rgba(0,0,0,0.15);
        transition: transform .15s ease-in-out;
    }

    .stat-card:hover {
        transform: translateY(-4px);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.9;
    }

    .stat-green  { background: #22c55e; }
    .stat-blue   { background: #3b82f6; }
    .stat-purple { background: #6366f1; }
    .stat-orange { background: #f97316; }
    .stat-yellow { background: #eab308; }

    /* Action buttons (same vibe as other dashboards) */
    .action-btn {
        border-radius: 14px;
        padding: 16px;
        font-size: 1.05rem;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .action-btn i {
        font-size: 1.3rem;
    }
</style>

<div class="container py-4">

    <!-- HERO HEADER -->
    <div class="hero-header mb-4">
        <h2 class="fw-bold mb-1">Admin Dashboard</h2>
        <p class="mb-0">
            Welcome, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>. 
            Monitor SmartWasteHub activity and manage users, pickups and rewards.
        </p>
    </div>

    <!-- STATISTICS GRID -->
    <div class="row g-4 mb-2">
        <div class="col-md-3">
            <div class="stat-card stat-green">
                <i class="bi bi-house-door stat-icon"></i>
                <h6 class="mt-3 mb-0">Households</h6>
                <h2 class="fw-bold mb-0"><?= number_format($totalUsers) ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-blue">
                <i class="bi bi-truck stat-icon"></i>
                <h6 class="mt-3 mb-0">Collectors</h6>
                <h2 class="fw-bold mb-0"><?= number_format($totalCollectors) ?></h2>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card stat-purple">
                <i class="bi bi-list-task stat-icon"></i>
                <h6 class="mt-3 mb-0">Total Pickups</h6>
                <h2 class="fw-bold mb-0"><?= number_format($totalPickups) ?></h2>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card stat-orange">
                <i class="bi bi-coin stat-icon"></i>
                <h6 class="mt-3 mb-0">Credits Issued</h6>
                <h2 class="fw-bold mb-0"><?= number_format($totalCreditsIssued, 2) ?></h2>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card stat-yellow">
                <i class="bi bi-hourglass-split stat-icon"></i>
                <h6 class="mt-3 mb-0">Pending Pickups</h6>
                <h2 class="fw-bold mb-0"><?= number_format($pendingPickups) ?></h2>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT ROW -->
    <div class="row mt-4">
        <!-- Top households -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm p-4">
                <h5 class="fw-bold mb-3">Top Households by Credits</h5>

                <?php if ($topUsers && $topUsers->num_rows): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th>#</th>
                                    <th>Household</th>
                                    <th class="text-end">Total Credits</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 1;
                                while ($u = $topUsers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $rank++ ?></td>
                                        <td><?= htmlspecialchars($u['name']) ?></td>
                                        <td class="text-end">
                                            <span class="badge bg-success">
                                                <?= number_format($u['total_credits'], 2) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No users or transactions yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Admin actions + tips -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm p-4 mb-3">
                <h5 class="fw-bold mb-3">Admin Actions</h5>

                <a href="/smartwastehub/frontend/admin_requests.php"
                   class="btn btn-success w-100 action-btn">
                    <i class="bi bi-inboxes"></i> View Pickup Requests
                </a>

                <a href="/smartwastehub/frontend/admin_users.php"
                   class="btn btn-primary w-100 action-btn">
                    <i class="bi bi-people-fill"></i> Manage Users
                </a>

                <a href="/smartwastehub/frontend/admin_rewards.php"
                   class="btn btn-warning w-100 action-btn">
                    <i class="bi bi-gift"></i> Manage Rewards
                </a>

                <a href="/smartwastehub/frontend/admin_page.php"
                   class="btn btn-outline-dark w-100 action-btn">
                    <i class="bi bi-gear"></i> Platform Settings
                </a>
            </div>

            <div class="card shadow-sm p-4">
                <h6 class="fw-bold mb-2">Quick Tips</h6>
                <ul class="mb-0">
                    <li>Assign or reassign collectors from the requests page.</li>
                    <li>Use rewards to encourage consistent sorting and recycling.</li>
                    <li>Check pending pickups daily to avoid backlogs.</li>
                    <li>Export data (pickups & credits) for your project report.</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
