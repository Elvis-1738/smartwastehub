<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

// fetch user's requests
$stmt = $conn->prepare("
    SELECT pr.*, wc.name AS category_name, u.name AS collector_name
    FROM pickup_requests pr
    JOIN waste_categories wc ON pr.category_id = wc.id
    LEFT JOIN users u ON pr.collector_id = u.id
    WHERE pr.user_id = ?
    ORDER BY pr.created_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$requests = $stmt->get_result();

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">My Pickup Requests</h2>
<p>Track all requests submitted to SmartWasteHub.</p>

<table class="table table-bordered table-striped mt-3">
    <thead class="table-success">
        <tr>
            <th>Category</th>
            <th>Date</th>
            <th>Time Slot</th>
            <th>Status</th>
            <th>Collector</th>
            <th>Weight (kg)</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $requests->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['category_name']) ?></td>
            <td><?= htmlspecialchars($row['scheduled_date']) ?></td>
            <td><?= htmlspecialchars($row['time_slot']) ?></td>
            <td>
                <?php
                $status = $row['status'];
                $badge = "secondary";
                if ($status == "pending") $badge = "warning";
                if ($status == "assigned") $badge = "info";
                if ($status == "completed") $badge = "success";
                if ($status == "cancelled") $badge = "danger";
                ?>
                <span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span>
            </td>
            <td><?= $row['collector_name'] ? htmlspecialchars($row['collector_name']) : '-' ?></td>
            <td><?= $row['weight_kg'] ? htmlspecialchars($row['weight_kg']) : '-' ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
