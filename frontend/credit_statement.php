<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Fetch transactions oldest → newest
$stmt = $conn->prepare("
    SELECT id, pickup_id, credits, type, created_at
    FROM reward_transactions
    WHERE user_id = ?
    ORDER BY created_at ASC, id ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
$running = 0;

while ($r = $result->fetch_assoc()) {
    $amount = (float)$r['credits'];

    if ($r['type'] === 'earn') {
        $running += $amount;
        $signed = "+" . number_format($amount, 2);
        $desc = "Earned from Pickup (ID: {$r['pickup_id']})";
    } else {
        $running -= $amount;
        $signed = "-" . number_format($amount, 2);
        $desc = "Reward Redemption";
    }

    $rows[] = [
        'date' => $r['created_at'],
        'type' => ucfirst($r['type']),
        'description' => $desc,
        'amount' => $signed,
        'balance' => number_format($running, 2)
    ];
}

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Credit Statement</h2>
<p>This is your detailed recycling credit statement, including running balance over time.</p>

<div class="card p-3 shadow-sm mt-3">
    <div class="row mb-3">
        <div class="col">
            <a href="/smartwastehub/frontend/credit_statement_export.php" class="btn btn-success">
                Download CSV
            </a>
        </div>
        <div class="col text-end">
            <strong>Closing Balance:</strong>
            <span class="text-success fw-bold">
                <?= $rows ? end($rows)['balance'] : "0.00" ?> credits
            </span>
        </div>
    </div>

    <?php if (empty($rows)): ?>
        <div class="alert alert-info">No credit activity yet.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= date("Y-m-d H:i", strtotime($r['date'])) ?></td>
                    <td><?= htmlspecialchars($r['type']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td class="text-end"><?= $r['amount'] ?></td>
                    <td class="text-end"><?= $r['balance'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
