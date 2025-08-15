<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

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

    echo "Signup successful. <a href='../../frontend/login.php'>Login here</a>.";
}
?>
