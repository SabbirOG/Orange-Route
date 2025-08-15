<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Input validation
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }
    
    if (strlen($username) < 3 || strlen($username) > 50) {
        die("Username must be between 3 and 50 characters.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        die("Password must be at least " . PASSWORD_MIN_LENGTH . " characters long.");
    }

    // Check if username or email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) die("Username or email already exists.");
    $stmt->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $role = (strpos($email, 'driver.uiu.bd') !== false) ? 'driver' : 'user';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, verified) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
    $stmt->execute();
    $stmt->close();

    // Redirect to a styled success page
    header("Location: ../../frontend/signup_success.php?username=" . urlencode($username));
    exit();
}
?>
