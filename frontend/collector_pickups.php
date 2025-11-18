<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'collector') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$collector_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT pr.*, wc.name AS category_name, u.name AS user_name 
    FROM pickup_requests pr
    JOIN waste_categories wc ON pr.category_id = wc.id
    JOIN users u ON pr.user_id = u.id
    WHERE pr.collector_id = ? AND pr.status IN ('assigned','pending')
    ORDER BY pr.created_at DESC
");
$stmt->bind_param("i", $collector_id);
$stmt->execute();
$pickups = $stmt->get_result();

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Assigned Pickups</h2>

<table class="table table-bordered table-striped mt-3">
    <thead class="table-success">
        <tr>
            <th>Category</th>
            <th>Household</th>
            <th>Date</th>
            <th>Time Slot</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $pickups->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['category_name']) ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['scheduled_date']) ?></td>
            <td><?= htmlspecialchars($row['time_slot']) ?></td>
            <td><span class="badge bg-info"><?= ucfirst($row['status']) ?></span></td>
            <td>
                <a class="btn btn-sm btn-success" 
                   href="/smartwastehub/frontend/collector_complete_pickup.php?id=<?= $row['id'] ?>">
                    Mark Completed
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
