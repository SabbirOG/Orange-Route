<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to landing page if user is not authenticated
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: landing.php");
    exit();
}

// Redirect authenticated users to their appropriate dashboard
if (isset($_SESSION['role'])) {
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
            <?php include 'includes/navigation.php'; ?>
        </div>
    </header>
    <main>
        <div class="container">
            <div class="card text-center">
                <h2 class="text-orange">Your University Shuttle Tracking Solution</h2>
                <p class="mt-2">Track your shuttles in real-time, chat with drivers, and manage your profile easily. Developed by Sabbir Ahmed to help the student community.</p>
                
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>🚌 Real-time Tracking</h3>
                        <p>Track shuttle locations and status updates in real-time</p>
                    </div>
                    <div class="dashboard-card">
                        <h3>💬 Driver Communication</h3>
                        <p>Chat directly with drivers for updates and information</p>
                    </div>
                    <div class="dashboard-card">
                        <h3>👤 Profile Management</h3>
                        <p>Manage your profile and preferences easily</p>
                    </div>
                </div>
                
                <div class="mt-2" style="text-align: center;">
                    <a href="login.php" style="display: inline-block; padding: 12px 24px; margin: 8px; background: #FF6B35; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; border: 2px solid #FF6B35; transition: all 0.3s ease;" onmouseover="this.style.background='white'; this.style.color='#FF6B35'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)'" onmouseout="this.style.background='#FF6B35'; this.style.color='white'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">Get Started</a>
                    <a href="signup.php" style="display: inline-block; padding: 12px 24px; margin: 8px; background: #FF6B35; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; border: 2px solid #FF6B35; transition: all 0.3s ease;" onmouseover="this.style.background='white'; this.style.color='#FF6B35'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)'" onmouseout="this.style.background='#FF6B35'; this.style.color='white'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">Create Account</a>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.1rem;">📱</span>
                <a href="#" style="color: var(--white); text-decoration: none; font-weight: 500;">Follow Us</a>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.1rem;">📧</span>
                <a href="mailto:contact@orangeroute.com" style="color: var(--white); text-decoration: none; font-weight: 500;">Contact Us</a>
            </div>
        </div>
    </footer>
    <script src="../assets/js/main.js"></script>
</body>
</html>