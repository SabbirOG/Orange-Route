<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $code  = trim($_POST['code']);

    // Find user
    $stmt = $conn->prepare("SELECT id, verified FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        die("Email not found.");
    }
    $stmt->bind_result($user_id, $verified);
    $stmt->fetch();
    $stmt->close();

    if ($verified) {
        die("Account already verified!");
    }

    // Check verification code
    $stmt = $conn->prepare("SELECT id, expires_at FROM email_verifications WHERE user_id=? AND code=?");
    $stmt->bind_param("is", $user_id, $code);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        die("Invalid verification code.");
    }
    $stmt->bind_result($verification_id, $expires_at);
    $stmt->fetch();
    $stmt->close();

    // Check expiry
    if (strtotime($expires_at) < time()) {
        die("Verification code expired.");
    }

    // Mark user as verified
    $stmt = $conn->prepare("UPDATE users SET verified=1 WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete used verification code
    $stmt = $conn->prepare("DELETE FROM email_verifications WHERE id=?");
    $stmt->bind_param("i", $verification_id);
    $stmt->execute();
    $stmt->close();

    echo "Your account has been successfully verified! You can now login.";
}
?>
