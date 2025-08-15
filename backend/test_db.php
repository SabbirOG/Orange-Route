<?php
include 'db.php';

echo "<h2>Database Connection Test</h2>";
echo "✅ Database connected successfully!<br><br>";

// Test database tables
$tables = ['users', 'shuttles', 'general_chat', 'email_verifications', 'password_resets'];
echo "<h3>Checking Database Tables:</h3>";

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✅ Table '$table' exists<br>";
    } else {
        echo "❌ Table '$table' missing<br>";
    }
}

echo "<br><h3>Database Information:</h3>";
echo "Database: " . $conn->get_server_info() . "<br>";
echo "Client: " . $conn->client_info . "<br>";

$conn->close();
?>
