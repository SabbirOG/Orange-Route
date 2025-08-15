<?php
// test_schema.php - Test database schema
include 'db.php';

echo "<h2>Database Schema Test</h2>";

// Test shuttles table structure
$result = $conn->query("DESCRIBE shuttles");
if ($result) {
    echo "<h3>Shuttles Table Structure:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Error: " . $conn->error . "</p>";
}

// Test if location fields exist
$result = $conn->query("SELECT latitude, longitude, location_updated_at FROM shuttles LIMIT 1");
if ($result) {
    echo "<p style='color: green;'>✓ Location fields exist in shuttles table</p>";
} else {
    echo "<p style='color: red;'>✗ Location fields missing: " . $conn->error . "</p>";
}

// Test users table
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p>Users in database: " . $row['count'] . "</p>";
}

$conn->close();
?>
