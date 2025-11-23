<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

// Safe wallet lookup
$walletQuery = $conn->query("SELECT balance FROM reward_wallets WHERE user_id={$_SESSION['user_id']}");
$walletRow = $walletQuery->fetch_assoc();
$wallet = $walletRow['balance'] ?? 0;

// Fetch redeemable items
$items = $conn->query("SELECT * FROM reward_items ORDER BY cost_credits ASC");

$success = "";
$error = "";

// Handle redemption
if (isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);
    $itemQuery = $conn->query("SELECT * FROM reward_items WHERE id=$item_id");
    $item = $itemQuery->fetch_assoc();

    if (!$item) {
        $error = "Invalid reward item.";
    } else {

        $cost = (int)$item['cost_credits'];
        $user_id = (int)$_SESSION['user_id'];

        if ($wallet < $cost) {
            $error = "You do not have enough credits.";
        } else {
            // 1) Deduct credits from wallet
            $deduct = $conn->prepare("UPDATE reward_wallets SET balance = balance - ? WHERE user_id = ?");
            $deduct->bind_param("di", $cost, $user_id);
            $deduct->execute();

            // 2) Log redemption transaction (pickup_id = 0)
            $log = $conn->prepare("
                INSERT INTO reward_transactions (user_id, pickup_id, credits, type)
                VALUES (?, NULL, ?, 'redeem')
            ");
            $log->bind_param("id", $user_id, $cost);

            if (!$log->execute()) {
                $error = "Transaction insert failed: " . $log->error;
            } else {
                $success = "Successfully redeemed: " . htmlspecialchars($item['name']);
                $wallet -= $cost;
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<h2 class="text-success fw-bold">Redeem Your Recycling Credits</h2>

<p><strong>Your Balance:</strong> <span class="text-success fw-bold"><?= number_format($wallet) ?></span> credits</p>

<?php if($success): ?>
    
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<table class="table table-bordered mt-3">
    <thead class="table-success">
        <tr>
            <th>Reward</th>
            <th>Cost (credits)</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($r = $items->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= $r['cost_credits'] ?></td>
            <td>
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="item_id" value="<?= $r['id'] ?>">
                    <button class="btn btn-sm btn-success"
                        <?= $wallet < $r['cost_credits'] ? 'disabled' : '' ?>>
                        Redeem
                    </button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
