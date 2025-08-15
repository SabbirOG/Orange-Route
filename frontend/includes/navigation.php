<?php
// Navigation component for consistent navigation across all pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;
?>

<nav>
    <ul>
        <li><a href="landing.php">Home</a></li>
        <?php if ($user_id && $user_role): ?>
            <li><a href="dashboards/<?php echo $user_role; ?>_dashboard.php">Dashboard</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="../backend/auth.php?action=logout">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="btn-secondary">Login</a></li>
            <li><a href="signup.php" class="btn-secondary">Sign Up</a></li>
            <li><a href="forgot_password.php">Forgot Password</a></li>
        <?php endif; ?>
    </ul>
</nav>
