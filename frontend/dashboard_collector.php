<?php
require_once __DIR__ . '/../backend/config.php';

// Redirect if not logged in or not a collector
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'collector') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$collector_id = (int) $_SESSION['user_id'];

// Fetch counts
$assigned = $conn->query("SELECT COUNT(*) AS total FROM pickup_requests WHERE collector_id = $collector_id")->fetch_assoc()['total'] ?? 0;
$pending = $conn->query("SELECT COUNT(*) AS total FROM pickup_requests WHERE collector_id = $collector_id AND status='pending'")->fetch_assoc()['total'] ?? 0;
$progress = $conn->query("SELECT COUNT(*) AS total FROM pickup_requests WHERE collector_id = $collector_id AND status='in_progress'")->fetch_assoc()['total'] ?? 0;
$completed_kg = $conn->query("SELECT SUM(weight_kg) AS total FROM pickup_requests WHERE collector_id = $collector_id AND status='completed'")->fetch_assoc()['total'] ?? 0;

// Recent pickups
$recent = $conn->query("
    SELECT pr.*, u.name AS household_name
    FROM pickup_requests pr
    JOIN users u ON pr.user_id = u.id
    WHERE pr.collector_id = $collector_id
    ORDER BY pr.id DESC
    LIMIT 3
");
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ========================================================= -->
<!-- 🎨 CUSTOM DASHBOARD STYLES -->
<!-- ========================================================= -->
<style>
/* Hero Banner */
.hero-header {
    background: linear-gradient(135deg, #0ea5e9, #16a34a);
    border-radius: 15px;
    padding: 35px;
    color: white;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

/* Stat Cards */
.stat-card {
    border-radius: 18px;
    padding: 25px;
    color: white;
    box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    transition: transform .15s ease-in-out;
    text-align: center;
}
.stat-card:hover { transform: translateY(-4px); }

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.9;
}

/* Colors */
.stat-green  { background: #22c55e; }
.stat-blue   { background: #3b82f6; }
.stat-orange { background: #f97316; }
.stat-purple { background: #a855f7; }

/* Action Buttons */
.dashboard-btn {
    padding: 22px;
    border-radius: 12px;
    color: #fff !important;
    font-size: 20px;
    font-weight: 600;
    display: block;
    text-align: center;
    transition: 0.2s;
}

/* Button Colors */
.btn-green { background: #188351; }
.btn-green:hover { background: #0f5e3d; }

.btn-blue { background: #0d6efd; }
.btn-blue:hover { background: #0a58ca; }

.btn-black { background: #212529; }
.btn-black:hover { background: #000; }

/* Table */
.table thead {
    background: #d1f5d3;
    font-weight: bold;
}
</style>

<!-- ========================================================= -->
<!-- 🟩 HERO SECTION -->
<!-- ========================================================= -->

<div class="container py-4">
    <div class="hero-header mb-4">
        <h2 class="fw-bold">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h2>
        <p class="mb-0">Here is your Smart Waste Hub collector dashboard summary.</p>
    </div>

    <!-- ========================================================= -->
    <!-- 🟦 STAT CARDS -->
    <!-- ========================================================= -->

    <div class="row g-4">

        <div class="col-md-3">
            <div class="stat-card stat-blue">
                <i class="bi bi-list-task stat-icon"></i>
                <h6 class="mt-3 mb-0">Assigned Pickups</h6>
                <h2 class="fw-bold"><?= $assigned ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-orange">
                <i class="bi bi-hourglass-split stat-icon"></i>
                <h6 class="mt-3 mb-0">Pending</h6>
                <h2 class="fw-bold"><?= $pending ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-purple">
                <i class="bi bi-arrow-repeat stat-icon"></i>
                <h6 class="mt-3 mb-0">In Progress</h6>
                <h2 class="fw-bold"><?= $progress ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-green">
                <i class="bi bi-check2-circle stat-icon"></i>
                <h6 class="mt-3 mb-0">Completed (kg)</h6>
                <h2 class="fw-bold"><?= number_format($completed_kg, 2) ?></h2>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- 🟧 ACTION BUTTONS -->
    <!-- ========================================================= -->

    <div class="row mt-4 mb-4">

        <div class="col-md-6">
            <a href="/smartwastehub/frontend/collector_pickups.php"
                class="dashboard-btn btn-green">
                <i class="bi bi-list-ul me-2"></i> View My Pickups
            </a>
        </div>

        <div class="col-md-6">
            <a href="/smartwastehub/frontend/collector_available.php"
                class="dashboard-btn btn-blue">
                <i class="bi bi-search me-2"></i> View Available Requests
            </a>
        </div>

    </div>

    <div class="row mt-3 mb-5">
        <div class="col-md-12">
            <a href="/smartwastehub/frontend/collector_complete_pickup.php"
                class="dashboard-btn btn-black">
                <i class="bi bi-clipboard-check me-2"></i> Complete a Pickup
            </a>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- 🟩 RECENT PICKUPS TABLE -->
    <!-- ========================================================= -->

    <div class="card shadow-sm p-4 mt-5">
        <h4 class="fw-bold mb-3">Recent Pickups Assigned to You</h4>

        <?php if ($recent && $recent->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Household</th>
                            <th>Category ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recent->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['scheduled_date']) ?></td>
                                <td><?= htmlspecialchars($row['time_slot']) ?></td>
                                <td><?= htmlspecialchars($row['household_name']) ?></td>
                                <td><?= htmlspecialchars($row['category_id']) ?></td>
                                <td>
                                    <?php
                                        $status = $row['status'];
                                        $badge = "secondary";
                                        if ($status == "pending") $badge = "warning";
                                        if ($status == "assigned") $badge = "info";
                                        if ($status == "completed") $badge = "success";
                                        if ($status == "cancelled") $badge = "danger";
                                    ?>
                                    <span class="badge bg-<?= $badge ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">No pickups assigned yet.</p>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
