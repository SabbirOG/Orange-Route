<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: dashboards/admin_dashboard.php");
            exit();
        case 'driver':
            header("Location: dashboards/driver_dashboard.php");
            exit();
        case 'user':
            header("Location: dashboards/user_dashboard.php");
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrangeRoute - University Shuttle Tracking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-section">
                <div class="circle">
                    <img src="../assets/images/orangeroute-logo-modified.png" alt="OrangeRoute Logo" class="logo">
                </div>
                <h1>OrangeRoute</h1>
            </div>
            <nav>
                <a href="login.php" class="btn-secondary">Login</a>
                <a href="signup.php" class="btn-secondary">Sign Up</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="container">
            <!-- Hero Section -->
            <div class="hero-section">
                <h1 class="hero-title">Welcome to OrangeRoute</h1>
                <p class="hero-subtitle">Your University Shuttle Tracking Solution</p>
                <p class="hero-description">Track your shuttles in real-time, chat with drivers, and manage your profile easily. Developed by Sabbir Ahmed to help the student community.</p>
            </div>

            <!-- Features Section -->
            <div class="features-section">
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-icon">🚌</div>
                        <h3>Real-time Tracking</h3>
                        <p>Track shuttle locations and status updates in real-time with live GPS tracking</p>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">💬</div>
                        <h3>Driver Communication</h3>
                        <p>Chat directly with drivers for updates and information about your journey</p>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-icon">👤</div>
                        <h3>Profile Management</h3>
                        <p>Manage your profile, preferences, and account settings easily</p>
                    </div>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <div class="hero-section">
                    <h2 class="hero-title text-orange">Ready to Get Started?</h2>
                    <p class="hero-subtitle">Join thousands of students who trust OrangeRoute for their daily commute</p>
                    <div class="mt-2 text-center">
                        <a href="login.php" class="landing-btn">Get Started</a>
                        <a href="signup.php" class="landing-btn">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 OrangeRoute – Developed by Sabbir Ahmed.</p>
    </footer>
    <script src="../assets/js/main.js"></script>
</body>
</html>
