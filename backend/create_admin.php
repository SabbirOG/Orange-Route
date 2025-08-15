<?php
// create_admin.php - Create admin user for testing
// WARNING: Remove this file after creating admin user

// Security check - only allow access from localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die("Access denied. This script can only be run from localhost.");
}

include 'db.php';

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE role = 'admin'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Admin user already exists!";
    exit();
}

// Create admin user
$username = "admin";
$email = "admin@orangeroute.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $conn->prepare("INSERT INTO users (username, email, password, role, verified) VALUES (?, ?, ?, ?, 1)");
$stmt->bind_param("ssss", $username, $email, $password, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Username: admin<br>";
    echo "Email: admin@orangeroute.com<br>";
    echo "Password: admin123<br>";
    echo "<br><strong>IMPORTANT: Delete this file after creating admin user!</strong>";
} else {
    echo "Error creating admin user: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
