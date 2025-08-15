<?php
session_start();
include '../../backend/db.php';

// Check if driver is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    die("Access denied. Please login as driver.");
}

$driver_id = $_SESSION['user_id'];

// Fetch shuttle assigned to this driver
$stmt = $conn->prepare("SELECT * FROM shuttles WHERE driver_id=?");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
$shuttles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - OrangeRoute</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-section">
                <div class="circle">
                    <img src="../../assets/images/orangeroute-logo-modified.png" alt="OrangeRoute Logo" class="logo">
                </div>
                <h1>OrangeRoute</h1>
            </div>
            <?php include 'includes/dashboard_navigation.php'; ?>
        </div>
    </header>
    
    <main>
        <div class="container">
            <div class="card">
                <h2 class="text-orange">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p>Manage your assigned shuttle and keep passengers informed.</p>
            </div>
            
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>🚌 My Shuttles</h3>
                    <p><?php echo count($shuttles); ?> shuttle(s) assigned</p>
                </div>
                <div class="dashboard-card">
                    <h3>📊 Status Updates</h3>
                    <p>Keep passengers informed about delays</p>
                </div>
                <div class="dashboard-card">
                    <h3>💬 Communication</h3>
                    <p>Chat with passengers and other drivers</p>
                </div>
            </div>

            <?php if(count($shuttles) === 0): ?>
                <div class="card text-center">
                    <h3>No Shuttle Assigned</h3>
                    <p>Please wait for admin to assign you a shuttle route.</p>
                </div>
            <?php else: ?>
                <div class="card">
                    <h3 class="text-orange">Your Assigned Shuttle(s)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Shuttle Name</th>
                                <th>Route</th>
                                <th>Status</th>
                                <th>Traffic</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($shuttles as $shuttle): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shuttle['name']); ?></td>
                                    <td><?php echo htmlspecialchars($shuttle['route_name']); ?></td>
                                    <td>
                                        <span class="<?php echo $shuttle['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo ucfirst($shuttle['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $shuttle['traffic_status'] ? '🚦 Traffic' : '✅ Normal'; ?></td>
                                    <td>
                                        <form action="../../backend/shuttle.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="shuttle_id" value="<?php echo $shuttle['id']; ?>">
                                            <?php if($shuttle['status'] === 'inactive'): ?>
                                                <button type="submit" name="action" value="start" class="btn-primary">Start Route</button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="down" class="btn-secondary">Stop Route</button>
                                            <?php endif; ?>
                                            <?php if($shuttle['traffic_status']): ?>
                                                <button type="submit" name="action" value="no_traffic" class="btn-secondary">No Traffic</button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="traffic" class="btn-secondary">Report Traffic</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <!-- Location Update Section -->
            <div class="card">
                <h3>📍 Update Location</h3>
                <p>Share your current location with students so they can track your shuttle in real-time.</p>
                
                <div id="location-status" class="location-status">
                    <p>Click "Get My Location" to share your current position</p>
                </div>
                
                <div class="location-controls">
                    <button id="get-location-btn" class="btn-primary">Get My Location</button>
                    <button id="update-location-btn" class="btn-secondary" disabled>Update Location</button>
                    <span id="last-updated" class="last-updated"></span>
                </div>
                
                <div id="location-map" class="location-map" style="height: 300px; margin-top: 1rem; border-radius: 8px; display: none;"></div>
            </div>
            
            <div class="card">
                <h3 class="text-orange">Driver Communication</h3>
                <div class="chat-container">
                    <div class="chat-messages" id="chatMessages">
                        <!-- Chat messages will be loaded here -->
                    </div>
                    <div class="chat-input-container">
                        <form id="chatForm">
                            <input type="text" class="chat-input" id="messageInput" placeholder="Type your message..." required>
                            <button type="submit" class="btn-primary">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 OrangeRoute - Developed by Sabbir Ahmed. Helping the student community with better transportation tracking.</p>
    </footer>
    
    <script src="../../assets/js/chat.js"></script>
    <script src="../../assets/js/main.js"></script>
    
    <!-- Location Update Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const getLocationBtn = document.getElementById('get-location-btn');
        const updateLocationBtn = document.getElementById('update-location-btn');
        const locationStatus = document.getElementById('location-status');
        const lastUpdated = document.getElementById('last-updated');
        const locationMap = document.getElementById('location-map');
        
        let currentLocation = null;
        let map = null;
        let marker = null;
        
        // Get user's current location
        getLocationBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                locationStatus.innerHTML = '<p style="color: red;">Geolocation is not supported by this browser.</p>';
                return;
            }
            
            locationStatus.innerHTML = '<p>Getting your location...</p>';
            getLocationBtn.disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    
                    locationStatus.innerHTML = '<p style="color: green;">✅ Location obtained successfully!</p>';
                    updateLocationBtn.disabled = false;
                    getLocationBtn.disabled = false;
                    
                    // Show map with location
                    showLocationOnMap(currentLocation);
                },
                function(error) {
                    let errorMessage = 'Unable to get your location. ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += 'Please allow location access.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage += 'Location request timed out.';
                            break;
                    }
                    locationStatus.innerHTML = '<p style="color: red;">' + errorMessage + '</p>';
                    getLocationBtn.disabled = false;
                }
            );
        });
        
        // Update location to server
        updateLocationBtn.addEventListener('click', function() {
            if (!currentLocation) {
                alert('Please get your location first.');
                return;
            }
            
            // Get the first active shuttle ID (assuming driver has one active shuttle)
            const shuttleRows = document.querySelectorAll('tbody tr');
            let shuttleId = null;
            
            for (let row of shuttleRows) {
                const statusCell = row.querySelector('td:nth-child(3)');
                if (statusCell && statusCell.textContent.includes('Active')) {
                    const form = row.querySelector('form');
                    if (form) {
                        const shuttleIdInput = form.querySelector('input[name="shuttle_id"]');
                        if (shuttleIdInput) {
                            shuttleId = shuttleIdInput.value;
                            break;
                        }
                    }
                }
            }
            
            if (!shuttleId) {
                alert('No active shuttle found. Please start a route first.');
                return;
            }
            
            updateLocationBtn.disabled = true;
            updateLocationBtn.textContent = 'Updating...';
            
            fetch('../../backend/update_location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    shuttle_id: shuttleId,
                    latitude: currentLocation.latitude,
                    longitude: currentLocation.longitude
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    locationStatus.innerHTML = '<p style="color: green;">✅ Location updated successfully!</p>';
                    lastUpdated.textContent = 'Last updated: ' + new Date().toLocaleTimeString();
                    updateLocationBtn.textContent = 'Update Location';
                } else {
                    locationStatus.innerHTML = '<p style="color: red;">❌ Failed to update location: ' + data.message + '</p>';
                }
                updateLocationBtn.disabled = false;
            })
            .catch(error => {
                locationStatus.innerHTML = '<p style="color: red;">❌ Error updating location: ' + error.message + '</p>';
                updateLocationBtn.disabled = false;
                updateLocationBtn.textContent = 'Update Location';
            });
        });
        
        // Show location on map
        function showLocationOnMap(location) {
            locationMap.style.display = 'block';
            
            if (!map) {
                map = L.map('location-map').setView([location.latitude, location.longitude], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
            } else {
                map.setView([location.latitude, location.longitude], 15);
            }
            
            if (marker) {
                map.removeLayer(marker);
            }
            
            marker = L.marker([location.latitude, location.longitude])
                .addTo(map)
                .bindPopup('Your current location')
                .openPopup();
        }
    });
    </script>
    
    <!-- Leaflet CSS and JS for map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
