<?php
session_start();
include '../db.php';
include '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $newPassword = $_POST['new_password'];
    
    // Debug logging
    error_log("Reset Password Attempt - Email: $email, Code: $code");
    
    // Find user
    $user = getUserByEmail($email);
    if (!$user) {
        header("Location: ../../frontend/reset_password.php?error=email_not_found");
        exit();
    }
    
    // Check reset code
    $stmt = $conn->prepare("SELECT id, expires_at, used FROM password_resets WHERE user_id=? AND code=?");
    $stmt->bind_param("is", $user['id'], $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset = $result->fetch_assoc();
    $stmt->close();
    
    if (!$reset) {
        header("Location: ../../frontend/reset_password.php?error=invalid_code");
        exit();
    }
    
    if ($reset['used']) {
        header("Location: ../../frontend/reset_password.php?error=code_used");
        exit();
    }
    
    if (strtotime($reset['expires_at']) < time()) {
        header("Location: ../../frontend/reset_password.php?error=code_expired");
        exit();
    }
    
    // Check password history (if table exists)
    $passwordHistory = [];
    try {
        $stmt = $conn->prepare("SELECT password_hash FROM password_history WHERE user_id=? ORDER BY created_at DESC LIMIT 5");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $passwordHistory[] = $row['password_hash'];
        }
        $stmt->close();
    } catch (Exception $e) {
        // If password_history table doesn't exist, continue without history check
        error_log("Password history check failed: " . $e->getMessage());
    }
    
    // Check if new password matches any old password
    foreach ($passwordHistory as $oldHash) {
        if (password_verify($newPassword, $oldHash)) {
            // Check if it's the most recent password
            if ($passwordHistory[0] === $oldHash) {
                header("Location: ../../frontend/reset_password.php?error=last_password");
            } else {
                header("Location: ../../frontend/reset_password.php?error=old_password");
            }
            exit();
        }
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $hashedPassword, $user['id']);
    
    if ($stmt->execute()) {
        // Add new password to history (if table exists)
        try {
            $stmt3 = $conn->prepare("INSERT INTO password_history (user_id, password_hash) VALUES (?, ?)");
            $stmt3->bind_param("is", $user['id'], $hashedPassword);
            $stmt3->execute();
            $stmt3->close();
        } catch (Exception $e) {
            // If password_history table doesn't exist, continue without adding to history
            error_log("Password history insertion failed: " . $e->getMessage());
        }
        
        // Mark reset code as used
        $stmt2 = $conn->prepare("UPDATE password_resets SET used=1 WHERE id=?");
        $stmt2->bind_param("i", $reset['id']);
        $stmt2->execute();
        $stmt2->close();
        
        header("Location: ../../frontend/reset_password.php?success=1");
        exit();
    } else {
        $stmt->close();
        header("Location: ../../frontend/reset_password.php?error=database_error");
        exit();
    }
}
?>
