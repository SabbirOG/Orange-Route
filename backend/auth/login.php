<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) die("Email not found.");
    $stmt->bind_result($user_id, $username, $hashed_password, $role);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($password, $hashed_password)) die("Incorrect password.");

    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;

    if ($role === 'driver') {
        header("Location: ../../frontend/dashboards/driver_dashboard.php");
    } elseif ($role === 'admin') {
        header("Location: ../../frontend/dashboards/admin_dashboard.php");
    } else {
        header("Location: ../../frontend/dashboards/user_dashboard.php");
    }
    exit();
}
?>
