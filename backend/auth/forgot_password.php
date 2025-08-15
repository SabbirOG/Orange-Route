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
    
    // Debug logging
    error_log("Forgot Password - Email: $email, Generated Code: $resetCode");
    
    // Store reset code in database (expires in 60 seconds for security)
    $expiresAt = date('Y-m-d H:i:s', strtotime('+60 seconds'));
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $resetCode, $expiresAt);
    
    if ($stmt->execute()) {
        // Store the reset code in session for display
        $_SESSION['reset_code'] = $resetCode;
        $_SESSION['reset_email'] = $email;
        
        // Redirect to reset password page with the code
        header("Location: ../../frontend/reset_password.php?code=" . $resetCode . "&email=" . urlencode($email));
        exit();
    } else {
        header("Location: ../../frontend/forgot_password.php?error=failed");
        exit();
    }
    
    $stmt->close();
}
?>
