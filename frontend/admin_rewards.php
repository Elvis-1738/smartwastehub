<!--
  reward_transactions.php
  -----------------------
  Admin dashboard page for viewing all reward transactions in the system.

  Responsibilities:
  - Restricts access to admin users only.
  - Fetches all reward transactions from the database, including:
        • User who received or spent credits
        • Associated pickup request ID
        • Number of credits awarded or deducted
        • Transaction type (earn or spend)
        • Timestamp of the transaction
  - Joins the reward_transactions table with users to display user names.
  - Presents the data in a structured table for easy review and auditing.

  Part of the Smart Waste Hub admin management module.
-->

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
