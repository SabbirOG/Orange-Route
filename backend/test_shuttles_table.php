<?php
// Test shuttles table structure
error_reporting(0);
ini_set('display_errors', 0);

include 'db.php';

header('Content-Type: application/json');

try {
    // Check if location columns exist
    $result = $conn->query("DESCRIBE shuttles");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    // Check if location columns exist
    $hasLatitude = in_array('latitude', $columns);
    $hasLongitude = in_array('longitude', $columns);
    $hasLocationUpdated = in_array('location_updated_at', $columns);
    
    // Get sample shuttle data
    $shuttleResult = $conn->query("SELECT id, driver_id, name, route_name, status, latitude, longitude, location_updated_at FROM shuttles LIMIT 1");
    $sampleShuttle = $shuttleResult ? $shuttleResult->fetch_assoc() : null;
    
    echo json_encode([
        'success' => true,
        'table_structure' => [
            'columns' => $columns,
            'has_latitude' => $hasLatitude,
            'has_longitude' => $hasLongitude,
            'has_location_updated_at' => $hasLocationUpdated
        ],
        'sample_shuttle' => $sampleShuttle,
        'total_shuttles' => $conn->query("SELECT COUNT(*) as count FROM shuttles")->fetch_assoc()['count']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
