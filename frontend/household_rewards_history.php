<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Fetch all reward transactions for this user, ordered oldest -> newest (for running balance)
$stmt = $conn->prepare("
    SELECT id, pickup_id, credits, type, created_at
    FROM reward_transactions
    WHERE user_id = ?
    ORDER BY created_at ASC, id ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}
$stmt->close();

// Calculate running balances
$running = 0;
$tx_with_balance = [];
foreach ($transactions as $t) {
    // interpret type: earn -> +credits, redeem/spend -> -credits
    $credits = (float)$t['credits'];
    if ($t['type'] === 'earn') {
        $running += $credits;
        $signed = '+' . number_format($credits, 2);
    } else { // redeem or spend
        $running -= $credits;
        $signed = '-' . number_format($credits, 2);
    }

    $tx_with_balance[] = [
        'id' => $t['id'],
        'pickup_id' => $t['pickup_id'],
        'type' => $t['type'],
        'credits' => $credits,
        'signed' => $signed,
        'created_at' => $t['created_at'],
        'balance' => $running,
    ];
}

// We'll display newest first, so reverse the array
$tx_with_balance = array_reverse($tx_with_balance);

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Rewards & Credits History</h2>

<p>Here is your complete credits history (earned from pickups and redeemed rewards).</p>

<div class="card mt-3 p-3 shadow-sm">
    <div class="row mb-3">
        <div class="col">
            <a href="/smartwastehub/frontend/household_redeem.php" class="btn btn-success">Redeem Credits</a>
            <a href="/smartwastehub/frontend/household_request_pickup.php" class="btn btn-outline-success ms-2">Request Pickup</a>
        </div>
        <div class="col text-end">
            <strong>Current Balance:</strong>
            <span class="text-success fw-bold"><?= number_format($tx_with_balance ? $tx_with_balance[0]['balance'] : ($walletRow['balance'] ?? 0), 2) ?> credits</span>
        </div>
    </div>

    <?php if (count($tx_with_balance) === 0): ?>
        <div class="alert alert-info">No transactions yet. Earn credits by completing pickups or redeem rewards once you have credits.</div>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-success">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Details</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Balance</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tx_with_balance as $tx): ?>
                <tr>
                    <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($tx['created_at']))) ?></td>
                    <td><?= htmlspecialchars(ucfirst($tx['type'])) ?></td>
                    <td>
                        <?php
                        if ($tx['type'] === 'earn' && !empty($tx['pickup_id'])) {
                            echo "Pickup (ID: " . intval($tx['pickup_id']) . ")";
                        } elseif ($tx['type'] === 'redeem' || $tx['type'] === 'spend') {
                            echo "Redeemed reward";
                        } else {
                            echo "Transaction";
                        }
                        ?>
                    </td>
                    <td class="text-end"><?= $tx['signed'] ?> </td>
                    <td class="text-end"><?= number_format($tx['balance'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
