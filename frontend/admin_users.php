<!--
  users.php
  ----------
  This admin page lists all registered users within the Smart Waste Hub system.

  Key functions:
  - Restricts access to authenticated admin users only.
  - Retrieves essential user details from the database, including:
        • Name
        • Email
        • Role (admin, collector, household)
        • Phone number
        • Location
  - Displays user data in a structured, sortable table for easier management.
  - Helps administrators monitor all system users at a glance.

  Part of the admin dashboard for managing platform users.
-->

<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$data = $conn->query("SELECT id, name, email, role, phone, location FROM users ORDER BY role, name");

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">System Users</h2>

<table class="table table-bordered table-striped mt-3">
<thead class="table-success">
<tr>
  <th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Location</th>
</tr>
</thead>
<tbody>
<?php while($u = $data->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($u['name']) ?></td>
  <td><?= htmlspecialchars($u['email']) ?></td>
  <td><?= $u['role'] ?></td>
  <td><?= $u['phone'] ?></td>
  <td><?= $u['location'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
