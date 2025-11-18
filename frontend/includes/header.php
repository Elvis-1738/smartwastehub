<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SmartWasteHub</title>
    <link rel="stylesheet" href="/smartwastehub/css/style.css">
</head>
<body>
<header>
    <h1>SmartWasteHub</h1>
    <nav>
        <a href="/smartwastehub/frontend/index.php">Home</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/smartwastehub/backend/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="/smartwastehub/backend/auth/login.php">Login</a>
            <a href="/smartwastehub/backend/auth/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
