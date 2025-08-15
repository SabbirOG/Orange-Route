<?php
session_start();
include '../db.php';
include '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $newPassword = $_POST['new_password'];
    
    // Find user
    $user = getUserByEmail($email);
    if (!$user) {
        die("Email not found.");
    }
    
    // Check reset code
    $stmt = $conn->prepare("SELECT id, expires_at, used FROM password_resets WHERE user_id=? AND code=?");
    $stmt->bind_param("is", $user['id'], $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset = $result->fetch_assoc();
    $stmt->close();
    
    if (!$reset) {
        die("Invalid reset code.");
    }
    
    if ($reset['used']) {
        die("Reset code already used.");
    }
    
    if (strtotime($reset['expires_at']) < time()) {
        die("Reset code expired.");
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $hashedPassword, $user['id']);
    
    if ($stmt->execute()) {
        // Mark reset code as used
        $stmt2 = $conn->prepare("UPDATE password_resets SET used=1 WHERE id=?");
        $stmt2->bind_param("i", $reset['id']);
        $stmt2->execute();
        $stmt2->close();
        
        header("Location: ../../frontend/reset_password.php?success=1");
        exit();
    } else {
        echo "Failed to reset password. Please try again.";
    }
    
    $stmt->close();
}
?>
