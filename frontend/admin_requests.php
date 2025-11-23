<!-- frontend\admin_requests.php -->
 
<!--
  admin_requests.php
  --------------------
  This page displays a complete list of all waste pickup requests for the admin.

  Key functions:
  - Ensures only authenticated admin users can access the page.
  - Retrieves pickup request data from the database, including:
        • Household (user) name
        • Waste category
        • Scheduled date and time slot
        • Request status
        • Assigned collector (if any)
        • Recorded waste weight (if available)
  - Joins multiple tables: pickup_requests, waste_categories, and users.
  - Presents the results in a clean, formatted table for admin review.
-->

<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$data = $conn->query("
    SELECT pr.*, wc.name AS category_name, u.name AS user_name,
           c.name AS collector_name
    FROM pickup_requests pr
    JOIN waste_categories wc ON pr.category_id = wc.id
    JOIN users u ON pr.user_id = u.id
    LEFT JOIN users c ON pr.collector_id = c.id
    ORDER BY pr.created_at DESC
");

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">All Pickup Requests</h2>

<table class="table table-bordered table-striped mt-3">
<thead class="table-success">
<tr>
  <th>Household</th><th>Category</th><th>Date</th>
  <th>Slot</th><th>Status</th><th>Collector</th><th>Weight</th>
</tr>
</thead>
<tbody>
<?php while($r = $data->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($r['user_name']) ?></td>
  <td><?= htmlspecialchars($r['category_name']) ?></td>
  <td><?= $r['scheduled_date'] ?></td>
  <td><?= $r['time_slot'] ?></td>
  <td><?= ucfirst($r['status']) ?></td>
  <td><?= $r['collector_name'] ?: '-' ?></td>
  <td><?= $r['weight_kg'] ?: '-' ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
