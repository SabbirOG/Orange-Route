<?php
// Dashboard navigation component for consistent navigation across all dashboard pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;
?>

<nav>
    <ul>
        <li><a href="../landing.php">Home</a></li>
        <?php if ($user_id && $user_role): ?>
            <li><a href="../profile.php">Profile</a></li>
            <li><a href="../../backend/auth.php?action=logout">Logout</a></li>
        <?php else: ?>
            <li><a href="../login.php">Login</a></li>
            <li><a href="../signup.php">Sign Up</a></li>
        <?php endif; ?>
    </ul>
</nav>
