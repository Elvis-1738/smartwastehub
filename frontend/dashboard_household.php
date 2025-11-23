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

    /* Action Buttons Core */
    .action-btn {
        border-radius: 14px;
        padding: 16px;
        font-size: 1.05rem;
        font-weight: 600;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        transition: 0.2s ease-in-out;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }

    /* Custom solid buttons */
    .btn-solid-green {
        background: #16a34a;
        color: white !important;
    }
    .btn-solid-green:hover {
        background: #15803d;
    }

    .btn-solid-blue {
        background: #0d6efd;
        color: white !important;
    }
    .btn-solid-blue:hover {
        background: #0a58ca;
    }

    .btn-solid-dark {
        background: #1f2937;
        color: white !important;
    }
    .btn-solid-dark:hover {
        background: #111827;
    }

    .btn-solid-lightgreen {
        background: #22c55e;
        color: white !important;
    }
    .btn-solid-lightgreen:hover {
        background: #16a34a;
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
            <a href="household_request_pickup.php"
               class="action-btn btn-solid-green w-100">
                <i class="bi bi-plus-circle"></i> Request Waste Pickup
            </a>
        </div>

        <div class="col-md-6">
            <a href="household_redeem.php"
               class="action-btn btn-solid-blue w-100">
                <i class="bi bi-gift-fill"></i> Redeem Credits
            </a>
        </div>

        <!-- FIXED BUTTONS (now 100% visible) -->
        <div class="col-md-6">
            <a href="household_pickup_history.php"
               class="action-btn btn-solid-dark w-100">
                <i class="bi bi-clock-history"></i> Pickup History
            </a>
        </div>

        <div class="col-md-6">
            <a href="credit_statement.php"
               class="action-btn btn-solid-lightgreen w-100">
                <i class="bi bi-file-earmark-text"></i> Credit Statement
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
