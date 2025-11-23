<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartWasteHub</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Main Style -->
    <link rel="stylesheet" href="/smartwastehub/css/style.css">

    <script src="/smartwastehub/frontend/js/notifications.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", requestNotificationPermission);
    </script>

</head>

<body class="glass-body">

<nav class="navbar glass-nav px-4 py-3">
    <span class="navbar-brand fw-bold text-white fs-4">
        <i class="bi bi-recycle me-2"></i> Smart Waste Hub
    </span>

    <div>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/smartwastehub/backend/auth/logout.php" 
               class="btn btn-logout">
               <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        <?php endif; ?>
    </div>
</nav>

<div class="container py-5">
