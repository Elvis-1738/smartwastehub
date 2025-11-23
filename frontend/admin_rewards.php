<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$data = $conn->query("
    SELECT rt.*, u.name
    FROM reward_transactions rt
    JOIN users u ON rt.user_id = u.id
    ORDER BY rt.created_at DESC
");

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Reward Transactions</h2>

<table class="table table-bordered table-striped mt-3">
<thead class="table-success">
<tr>
  <th>User</th><th>Pickup ID</th><th>Credits</th><th>Type</th><th>Date</th>
</tr>
</thead>
<tbody>
<?php while($t = $data->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($t['name']) ?></td>
  <td><?= $t['pickup_id'] ?></td>
  <td><?= $t['credits'] ?></td>
  <td><?= ucfirst($t['type']) ?></td>
  <td><?= $t['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
