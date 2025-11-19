<?php
require_once __DIR__ . '/../backend/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    header("Location: /smartwastehub/backend/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wallet balance
$w = $conn->query("SELECT balance FROM reward_wallets WHERE user_id=$user_id")->fetch_assoc();
$balance = $w['balance'] ?? 0;

// Count completed pickups
$pickups = $conn->query("
    SELECT COUNT(*) AS total 
    FROM pickup_requests 
    WHERE user_id=$user_id
")->fetch_assoc();
$total_pickups = $pickups['total'];

// Count total redemptions
$redeems = $conn->query("
    SELECT COUNT(*) AS total 
    FROM reward_transactions 
    WHERE user_id=$user_id AND type='redeem'
")->fetch_assoc();
$total_redeems = $redeems['total'];

include __DIR__ . '/includes/header.php';
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* Gradient Header */
    .hero-header {
        background: linear-gradient(135deg, #0ea5e9, #16a34a);
        border-radius: 15px;
        padding: 35px;
        color: white;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    /* Dashboard Metric Cards */
    .stat-card {
        border-radius: 18px;
        padding: 25px;
        color: white;
        box-shadow: 0 6px 14px rgba(0,0,0,0.15);
        transition: transform .15s ease-in-out;
    }

    .stat-card:hover {
        transform: translateY(-4px);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.9;
    }

    /* Colors */
    .stat-green { background: #22c55e; }
    .stat-blue { background: #3b82f6; }
    .stat-purple { background: #a855f7; }

    /* Action Buttons */
    .action-btn {
        border-radius: 14px;
        padding: 16px;
        font-size: 1.05rem;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .action-btn i {
        font-size: 1.4rem;
    }
</style>

<div class="container py-4">

    <!-- HERO GREETING CARD -->
    <div class="hero-header mb-4">
        <h2 class="fw-bold">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h2>
        <p class="mb-0">Here is your Smart Waste Hub household dashboard summary.</p>
    </div>

    <!-- STATISTICS GRID -->
    <div class="row g-4">

        <!-- Credits -->
        <div class="col-md-4">
            <div class="stat-card stat-green">
                <i class="bi bi-wallet2 stat-icon"></i>
                <h6 class="mt-3 mb-0">Total Credits</h6>
                <h2 class="fw-bold"><?= number_format($balance) ?></h2>
            </div>
        </div>

        <!-- Pickups -->
        <div class="col-md-4">
            <div class="stat-card stat-blue">
                <i class="bi bi-truck stat-icon"></i>
                <h6 class="mt-3 mb-0">Total Pickups</h6>
                <h2 class="fw-bold"><?= $total_pickups ?></h2>
            </div>
        </div>

        <!-- Redemptions -->
        <div class="col-md-4">
            <div class="stat-card stat-purple">
                <i class="bi bi-gift stat-icon"></i>
                <h6 class="mt-3 mb-0">Total Redemptions</h6>
                <h2 class="fw-bold"><?= $total_redeems ?></h2>
            </div>
        </div>

    </div>

    <!-- ACTION BUTTONS -->
    <div class="row g-4 mt-4">

        <div class="col-md-6">
            <a href="household_request_pickup.php" class="btn btn-success w-100 action-btn">
                <i class="bi bi-plus-circle"></i> Request Waste Pickup
            </a>
        </div>

        <div class="col-md-6">
            <a href="household_redeem.php" class="btn btn-primary w-100 action-btn">
                <i class="bi bi-gift-fill"></i> Redeem Credits
            </a>
        </div>

        <div class="col-md-6">
            <a href="household_pickup_history.php" class="btn btn-outline-dark w-100 action-btn">
                <i class="bi bi-clock-history"></i> Pickup History
            </a>
        </div>

        <div class="col-md-6">
            <a href="credit_statement.php" class="btn btn-outline-success w-100 action-btn">
                <i class="bi bi-file-earmark-text"></i> Credit Statement
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
