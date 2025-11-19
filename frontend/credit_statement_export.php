<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    exit("Access denied");
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

// Send CSV headers
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=credit_statement_user_$user_id.csv");

$output = fopen("php://output", "w");

// CSV headers
fputcsv($output, ["Date", "Type", "Description", "Amount", "Balance"]);

$running = 0;

while ($r = $result->fetch_assoc()) {
    $amount = (float)$r['credits'];

    if ($r['type'] === 'earn') {
        $running += $amount;
        $signed = "+" . $amount;
        $desc = "Earned from Pickup (ID: {$r['pickup_id']})";
    } else {
        $running -= $amount;
        $signed = "-" . $amount;
        $desc = "Reward Redemption";
    }

    fputcsv($output, [
        $r['created_at'],
        ucfirst($r['type']),
        $desc,
        $signed,
        number_format($running, 2)
    ]);
}

fclose($output);
exit;
