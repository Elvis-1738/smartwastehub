<?php
require_once __DIR__ . '/../backend/config.php';

// Redirect if not logged in or wrong role
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'collector') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$collector_id = (int) $_SESSION['user_id'];

// Fetch assigned pickups
$assigned = $conn->query("
    SELECT COUNT(*) AS total 
    FROM pickup_requests 
    WHERE collector_id = $collector_id
")->fetch_assoc()['total'] ?? 0;

// Fetch pending pickups
$pending = $conn->query("
    SELECT COUNT(*) AS total 
    FROM pickup_requests 
    WHERE collector_id = $collector_id AND status = 'pending'
")->fetch_assoc()['total'] ?? 0;

// In-progress pickups
$progress = $conn->query("
    SELECT COUNT(*) AS total 
    FROM pickup_requests 
    WHERE collector_id = $collector_id AND status = 'in_progress'
")->fetch_assoc()['total'] ?? 0;

// Total weight completed (THIS WAS THE ERROR 🔥 FIXED)
$completed_kg = $conn->query("
    SELECT SUM(weight_kg) AS total 
    FROM pickup_requests 
    WHERE collector_id = $collector_id AND status = 'completed'
")->fetch_assoc()['total'] ?? 0;

// Recent 3 pickups
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

<h2 class="fw-bold mb-4">Collector Dashboard</h2>
<p>Welcome back, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>. Manage your assigned pickups and complete collections.</p>

<div class="row mt-4">

    <!-- Assigned -->
    <div class="col-md-3">
        <div class="card shadow-sm p-3 text-center">
            <h5>Assigned</h5>
            <p class="fs-2 fw-bold"><?= $assigned ?></p>
        </div>
    </div>

    <!-- Pending -->
    <div class="col-md-3">
        <div class="card shadow-sm p-3 text-center">
            <h5>Pending</h5>
            <p class="fs-2 fw-bold text-warning"><?= $pending ?></p>
        </div>
    </div>

    <!-- In Progress -->
    <div class="col-md-3">
        <div class="card shadow-sm p-3 text-center">
            <h5>In Progress</h5>
            <p class="fs-2 fw-bold text-primary"><?= $progress ?></p>
        </div>
    </div>

    <!-- Completed Weight -->
    <div class="col-md-3">
        <div class="card shadow-sm p-3 text-center">
            <h5>Completed (kg)</h5>
            <p class="fs-2 fw-bold text-success"><?= number_format($completed_kg, 2) ?></p>
        </div>
    </div>
</div>

<!-- Recent pickups -->
<div class="card shadow-sm p-4 mt-5">
    <h4 class="fw-bold mb-3">Recent Pickups Assigned to You</h4>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Household</th>
                <th>Category</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $recent->fetch_assoc()): ?>
            <tr>
                <td><?= $row['scheduled_date'] ?></td>
                <td><?= $row['time_slot'] ?></td>
                <td><?= htmlspecialchars($row['household_name']) ?></td>
                <td><?= $row['category_id'] ?></td>
                <td class="fw-bold text-success"><?= ucfirst($row['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-4">
            <h5 class="fw-bold mb-3">Quick Actions</h5>

            <!-- Corrected links 👇 based on your actual folder structure -->
            <a href="/smartwastehub/frontend/collector_pickups.php"
               class="btn btn-success w-100 mb-3">
                <i class="bi bi-list-ul me-1"></i> View My Pickups
            </a>

            <a href="/smartwastehub/frontend/collector_complete_pickup.php"
               class="btn btn-primary w-100">
                <i class="bi bi-clipboard-check me-1"></i> Complete a Pickup
            </a>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm p-4">
            <h5 class="fw-bold mb-3">Tips</h5>
            <ul>
                <li>Enter accurate weight to ensure households earn correct credits.</li>
                <li>Mark pickups as completed immediately after collection.</li>
                <li>Check pickup history to confirm successful updates.</li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
