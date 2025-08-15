<?php
// auth.php - Authentication helper functions
require_once 'config.php';

function getUserById($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function getUserByEmail($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../frontend/landing.php");
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        die("Access denied. Required role: " . $role);
    }
}

function logout() {
    // Log logout attempt for debugging
    error_log("Logout attempt for user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie with multiple attempts
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        // Try multiple cookie destruction methods
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        setcookie(session_name(), '', time() - 42000, $params["path"]);
        setcookie(session_name(), '', time() - 42000);
    }
    
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    // Destroy the session
    session_destroy();
    
    // Log successful logout
    error_log("Logout successful, redirecting to landing page");
    
    // Redirect to landing page
    header("Location: ../frontend/landing.php");
    exit();
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    logout();
}

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function sendVerificationEmail($email, $code) {
    // In a real application, you would send an actual email here
    // For now, we'll just log it or return true
    error_log("Verification code for $email: $code");
    return true;
}

function createVerificationCode($userId, $code) {
    global $conn;
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $stmt = $conn->prepare("INSERT INTO email_verifications (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $code, $expiresAt);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

function verifyCode($userId, $code) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, expires_at FROM email_verifications WHERE user_id = ? AND code = ? AND expires_at > NOW()");
    $stmt->bind_param("is", $userId, $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $verification = $result->fetch_assoc();
    $stmt->close();
    
    return $verification;
}

function deleteVerificationCode($verificationId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM email_verifications WHERE id = ?");
    $stmt->bind_param("i", $verificationId);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}
?>
