<?php
session_start();
include '../db.php';
include '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // Check if user exists
    $user = getUserByEmail($email);
    if (!$user) {
        die("Email not found.");
    }
    
    // Generate reset code
    $resetCode = generateVerificationCode();
    
    // Store reset code in database
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $resetCode, $expiresAt);
    
    if ($stmt->execute()) {
        // In a real application, send email here
        error_log("Password reset code for $email: $resetCode");
        echo "Reset code sent to your email. <a href='../../frontend/reset_password.php'>Reset Password</a>";
    } else {
        echo "Failed to send reset code. Please try again.";
    }
    
    $stmt->close();
}
?>
