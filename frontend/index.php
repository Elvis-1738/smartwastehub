<?php include __DIR__ . '/includes/header.php'; ?>

<style>
.hero-section {
    margin-top: 80px;
    text-align: center;
    padding: 60px;
    border-radius: 25px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    color: #fff;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.hero-title {
    font-size: 48px;
    font-weight: 700;
}

.hero-subtext {
    font-size: 20px;
    margin-top: 10px;
    opacity: 0.9;
}

.hero-buttons {
    margin-top: 40px;
}

.hero-buttons a {
    padding: 12px 30px;
    border-radius: 30px;
    font-size: 18px;
    margin: 10px;
    transition: 0.2s ease-in-out;
}

.btn-login {
    background: #0d6efd;
    color: #fff;
    border: none;
}

.btn-login:hover {
    background: #0954bd;
    transform: translateY(-2px);
}

.btn-register {
    background: #28a745;
    color: #fff;
    border: none;
}

.btn-register:hover {
    background: #1e7f35;
    transform: translateY(-2px);
}
</style>

<div class="hero-section">
    <h1 class="hero-title">Welcome to <span style="color:#90EE90;">SmartWasteHub</span></h1>
    <p class="hero-subtext">
        Schedule waste pickups, earn recycling rewards, and help keep your community clean.
    </p>

    <div class="hero-buttons">
        <a href="/smartwastehub/backend/auth/login.php" class="btn btn-login">
            Login
        </a>
        <a href="/smartwastehub/backend/auth/register.php" class="btn btn-register">
            Create Account
        </a>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
