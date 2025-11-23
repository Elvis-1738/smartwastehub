<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'collector') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$pickup_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("
    SELECT pr.*, wc.name AS category_name, wc.reward_per_kg, u.name AS user_name, u.id AS household_id
    FROM pickup_requests pr
    JOIN waste_categories wc ON pr.category_id = wc.id
    JOIN users u ON pr.user_id = u.id
    WHERE pr.id = ? AND pr.collector_id = ?
");
$stmt->bind_param("ii", $pickup_id, $_SESSION['user_id']);
$stmt->execute();
$pickup = $stmt->get_result()->fetch_assoc();

$success = "";
$errors = [];

if (!$pickup) {
    $errors[] = "Invalid pickup request.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $pickup) {
    $weight = floatval($_POST['weight_kg']);

    if ($weight <= 0) {
        $errors[] = "Enter a valid weight (kg).";
    } else {
        // Calculate credits
        $credits = $weight * $pickup['reward_per_kg'];
        $user_id = $pickup['household_id'];

        // 1️⃣ Update pickup status + weight
        $update = $conn->prepare("UPDATE pickup_requests SET status='completed', weight_kg=? WHERE id=?");
        $update->bind_param("di", $weight, $pickup_id);
        $update->execute();

        // 2️⃣ Ensure wallet exists for the user
        $createWallet = $conn->prepare("
            INSERT INTO reward_wallets (user_id, balance)
            SELECT ?, 0
            WHERE NOT EXISTS (SELECT 1 FROM reward_wallets WHERE user_id=?)
        ");
        $createWallet->bind_param("ii", $user_id, $user_id);
        $createWallet->execute();

        // 3️⃣ Update wallet balance
        $wallet = $conn->prepare("UPDATE reward_wallets SET balance = balance + ? WHERE user_id=?");
        $wallet->bind_param("di", $credits, $user_id);
        $wallet->execute();

        // 4️⃣ Log reward transaction
        $log = $conn->prepare("
            INSERT INTO reward_transactions (user_id, pickup_id, credits, type)
            VALUES (?, ?, ?, 'earn')
        ");
        $log->bind_param("iid", $user_id, $pickup_id, $credits);
        $log->execute();

        $success = "Pickup completed! Household earned {$credits} recycling credits.";
    }
}

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Complete Pickup</h2>

<?php if ($pickup): ?>
<p><strong>Household:</strong> <?= htmlspecialchars($pickup['user_name']) ?></p>
<p><strong>Category:</strong> <?= htmlspecialchars($pickup['category_name']) ?></p>
<p><strong>Reward Per Kg:</strong> <?= htmlspecialchars($pickup['reward_per_kg']) ?> credits</p>
<p><strong>Date:</strong> <?= htmlspecialchars($pickup['scheduled_date']) ?></p>
<p><strong>Time Slot:</strong> <?= htmlspecialchars($pickup['time_slot']) ?></p>
<?php endif; ?>

<?php foreach($errors as $e): ?>
    <div class="alert alert-danger"><?= $e ?></div>
<?php endforeach; ?>

<?php if($success): ?>

    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (!$success && $pickup): ?>
<form method="POST" class="mt-3">
    <label class="form-label fw-semibold">Weight Collected (kg)</label>
    <input type="number" step="0.01" name="weight_kg" class="form-control mb-3" required>

    <button class="btn btn-success">Submit Completion</button>
</form>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
