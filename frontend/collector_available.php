<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'collector') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

// Fetch all pending pickup requests
$requests = $conn->query("
    SELECT pr.*, u.name AS user_name, wc.name AS category_name
    FROM pickup_requests pr
    JOIN users u ON pr.user_id = u.id
    JOIN waste_categories wc ON pr.category_id = wc.id
    WHERE pr.status = 'pending'
    ORDER BY pr.created_at ASC
");

// Handle claim action
if (isset($_POST['pickup_id'])) {
    $pickup_id = intval($_POST['pickup_id']);
    $collector_id = $_SESSION['user_id'];

    $conn->query("UPDATE pickup_requests 
                  SET status='assigned', collector_id=$collector_id
                  WHERE id=$pickup_id AND status='pending'");

    header("Location: /smartwastehub/frontend/collector_available.php?claimed=1");
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Available Pickup Requests</h2>

<?php if (isset($_GET['claimed'])): ?>
    <div class="alert alert-success">Pickup successfully claimed! Go to 'My Pickups' to complete it.</div>
<?php endif; ?>

<table class="table table-bordered mt-3">
    <thead class="table-success">
        <tr>
            <th>Household</th>
            <th>Waste Category</th>
            <th>Pickup Date</th>
            <th>Time Slot</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($r = $requests->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($r['user_name']) ?></td>
            <td><?= htmlspecialchars($r['category_name']) ?></td>
            <td><?= $r['scheduled_date'] ?></td>
            <td><?= $r['time_slot'] ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="pickup_id" value="<?= $r['id'] ?>">
                    <button class="btn btn-sm btn-success">Claim</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
