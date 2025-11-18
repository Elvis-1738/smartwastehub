<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

// fetch waste categories
$categories = $conn->query("SELECT id, name FROM waste_categories ORDER BY name ASC");

$success = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $category_id = $_POST['category_id'] ?? '';
    $scheduled_date = $_POST['scheduled_date'] ?? '';
    $time_slot = $_POST['time_slot'] ?? '';

    if (!$category_id || !$scheduled_date || !$time_slot) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO pickup_requests (user_id, category_id, scheduled_date, time_slot) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $_SESSION['user_id'], $category_id, $scheduled_date, $time_slot);

        if ($stmt->execute()) {
            $success = "Pickup request submitted successfully!";
        } else {
            $errors[] = "Error saving request.";
        }
        $stmt->close();
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow">
            <h3 class="text-center text-success fw-bold mb-3">Request Waste Pickup</h3>

            <?php foreach ($errors as $e): ?>
                <div class="alert alert-danger"><?= $e ?></div>
            <?php endforeach; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">

                <label class="form-label fw-semibold">Waste Category</label>
                <select name="category_id" class="form-control mb-3" required>
                    <option value="">Select category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label class="form-label fw-semibold">Pickup Date</label>
                <input type="date" name="scheduled_date" class="form-control mb-3" required>

                <label class="form-label fw-semibold">Preferred Time Slot</label>
                <select name="time_slot" class="form-control mb-3" required>
                    <option value="">Select time slot</option>
                    <option value="Morning (8AM - 11AM)">Morning (8AM - 11AM)</option>
                    <option value="Afternoon (12PM - 3PM)">Afternoon (12PM - 3PM)</option>
                    <option value="Evening (4PM - 7PM)">Evening (4PM - 7PM)</option>
                </select>

                <button class="btn btn-success w-100 mt-2">Submit Request</button>
            </form>

        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
