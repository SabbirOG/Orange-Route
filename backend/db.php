<?php
// Include configuration
require_once 'config.php';

// Database connection class for OrangeRoute
// Updated: September 24, 2025 - Enhanced error handling

// Create database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    logError("Database connection failed: " . $conn->connect_error, __FILE__, __LINE__);
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset(DB_CHARSET);
?>
