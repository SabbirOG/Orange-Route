<?php
session_start();
include '../../backend/db.php';
include '../../backend/config.php';

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
            <div class="card location-card">
                <h3>📍 Update Location</h3>
                <p>Share your current location with students so they can track your shuttle in real-time.</p>
                
                <div id="location-status" class="location-status">
                    <p>Click "Get My Location" to share your current position</p>
                </div>
                
                <div id="manual-location" class="manual-location" style="display: none; margin: 1rem 0; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                    <h4>📍 Manual Location Entry</h4>
                    <p>If GPS is not working, you can enter your coordinates manually:</p>
                    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                        <div>
                            <label>Latitude:</label>
                            <input type="number" id="manual-lat" step="0.000001" placeholder="23.7806" style="width: 120px; padding: 0.5rem; margin: 0 0.5rem;">
                        </div>
                        <div>
                            <label>Longitude:</label>
                            <input type="number" id="manual-lng" step="0.000001" placeholder="90.4192" style="width: 120px; padding: 0.5rem; margin: 0 0.5rem;">
                        </div>
                        <button id="use-manual-location" class="btn-secondary location-btn">
                            <span class="btn-icon">📍</span>
                            Use These Coordinates
                        </button>
                    </div>
                </div>
                
                <div class="location-controls">
                    <button id="get-location-btn" class="btn-primary location-btn">
                        <span class="btn-icon">📍</span>
                        Get My Location
                    </button>
                    <button id="refresh-location-btn" class="btn-secondary location-btn" style="display: none;">
                        <span class="btn-icon">🔄</span>
                        Refresh Location
                    </button>
                    <button id="stop-tracking-btn" class="btn-secondary location-btn" style="display: none;">
                        <span class="btn-icon">⏹️</span>
                        Stop Tracking
                    </button>
                    <button id="center-location-btn" class="btn-secondary location-btn" style="display: none;">
                        <span class="btn-icon">🎯</span>
                        Center on Me
                    </button>
                    <button id="update-location-btn" class="btn-secondary location-btn" disabled>
                        <span class="btn-icon">📤</span>
                        Update Location
                    </button>
                    <div id="last-updated" class="last-updated"></div>
                </div>
                
                <div id="location-map" class="location-map"></div>
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
        <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
            <div>
                <a href="https://github.com/SabbirOG" target="_blank" style="color: var(--white); text-decoration: none; font-weight: 500;">Follow Us</a>
            </div>
            <div>
                <a href="https://www.linkedin.com/in/sabbirog/" target="_blank" style="color: var(--white); text-decoration: none; font-weight: 500;">Contact Us</a>
            </div>
        </div>
    </footer>
    
    <script src="../../assets/js/chat.js"></script>
    <script src="../../assets/js/main.js"></script>
    
    <!-- Location Update Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const getLocationBtn = document.getElementById('get-location-btn');
        const refreshLocationBtn = document.getElementById('refresh-location-btn');
        const stopTrackingBtn = document.getElementById('stop-tracking-btn');
        const centerLocationBtn = document.getElementById('center-location-btn');
        const updateLocationBtn = document.getElementById('update-location-btn');
        const useManualLocationBtn = document.getElementById('use-manual-location');
        const manualLocationDiv = document.getElementById('manual-location');
        const locationStatus = document.getElementById('location-status');
        const lastUpdated = document.getElementById('last-updated');
        const locationMap = document.getElementById('location-map');
        
        let currentLocation = null;
        let map = null;
        let marker = null;
        let watchId = null; // For continuous location tracking
        let autoUpdateInterval = null; // For automatic server updates
        
        // Function to get accurate location with multiple attempts
        function getAccurateLocation(attempt = 1) {
            console.log(`getAccurateLocation called, attempt ${attempt}`);
            const maxAttempts = 3;
            
            // Geolocation options for maximum GPS accuracy
            const options = {
                enableHighAccuracy: true,  // Use GPS if available
                timeout: 25000,            // Wait even longer for GPS
                maximumAge: 0,             // Don't use cached location
                altitude: false,           // Don't need altitude
                altitudeAccuracy: false,   // Don't need altitude accuracy
                speed: false,              // Don't need speed
                heading: false             // Don't need heading
            };
            
            console.log('Calling navigator.geolocation.getCurrentPosition...');
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    console.log('Location success callback called', position);
                    const accuracy = position.coords.accuracy;
                    
                    // Check if accuracy is good enough (less than 50 meters)
                    if (accuracy > 50 && attempt < maxAttempts) {
                        console.log(`Accuracy ${Math.round(accuracy)}m not good enough, retrying...`);
                        locationStatus.innerHTML = `<p>Location accuracy: ${Math.round(accuracy)}m (attempt ${attempt}/${maxAttempts}). Trying again for better accuracy...</p>`;
                        setTimeout(() => getAccurateLocation(attempt + 1), 2000);
                        return;
                    }
                    
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: accuracy
                    };
                    
                    // Show detailed location info with debugging
                    locationStatus.innerHTML = `
                        <div class="success">
                            <p>✅ Location obtained successfully!</p>
                            <p><strong>Latitude:</strong> ${currentLocation.latitude.toFixed(6)}</p>
                            <p><strong>Longitude:</strong> ${currentLocation.longitude.toFixed(6)}</p>
                            <p><strong>Accuracy:</strong> ${Math.round(currentLocation.accuracy)} meters</p>
                            <p><strong>Google Maps Link:</strong> <a href="https://www.google.com/maps?q=${currentLocation.latitude},${currentLocation.longitude}" target="_blank" style="color: #007bff;">View on Google Maps</a></p>
                            <p><strong>Attempts:</strong> ${attempt}/${maxAttempts}</p>
                        </div>
                    `;
                    updateLocationBtn.disabled = false;
                    getLocationBtn.disabled = false;
                    refreshLocationBtn.style.display = 'inline-flex';
                    stopTrackingBtn.style.display = 'inline-flex';
                    centerLocationBtn.style.display = 'inline-flex';
                    
                    // Show map with location
                    showLocationOnMap(currentLocation);
                    
                    // Start continuous location tracking (like Google Maps)
                    startLocationTracking();
                },
                function(error) {
                    console.log('Location error callback called', error);
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
                    
                    // Try again if we haven't reached max attempts
                    if (attempt < maxAttempts) {
                        locationStatus.innerHTML = `<p class="error">${errorMessage} Trying again... (attempt ${attempt}/${maxAttempts})</p>`;
                        setTimeout(() => getAccurateLocation(attempt + 1), 3000);
                        return;
                    }
                    
                    locationStatus.innerHTML = '<p class="error">' + errorMessage + '</p>';
                    getLocationBtn.disabled = false;
                    
                    // Show manual location option if GPS fails
                    if (error.code === error.PERMISSION_DENIED || error.code === error.POSITION_UNAVAILABLE) {
                        manualLocationDiv.style.display = 'block';
                    }
                },
                options
            );
        }
        
        // Get user's current location with multiple attempts for accuracy
        getLocationBtn.addEventListener('click', function() {
            console.log('Get Location button clicked');
            
            if (!navigator.geolocation) {
                console.log('Geolocation not supported');
                locationStatus.innerHTML = '<p style="color: red;">Geolocation is not supported by this browser.</p>';
                return;
            }
            
            console.log('Starting location detection...');
            locationStatus.innerHTML = '<p>Getting your location with GPS...</p>';
            getLocationBtn.disabled = true;
            
            // Try to get the most accurate location possible
            getAccurateLocation();
        });
        
        // Refresh location button
        refreshLocationBtn.addEventListener('click', function() {
            getLocationBtn.click(); // Trigger the same location detection
        });
        
        // Stop tracking button
        stopTrackingBtn.addEventListener('click', function() {
            stopLocationTracking();
            locationStatus.innerHTML = `
                <div class="success">
                    <p>⏹️ Location tracking stopped</p>
                    <p><strong>Last Known Location:</strong></p>
                    <p><strong>Latitude:</strong> ${currentLocation ? currentLocation.latitude.toFixed(6) : 'N/A'}</p>
                    <p><strong>Longitude:</strong> ${currentLocation ? currentLocation.longitude.toFixed(6) : 'N/A'}</p>
                </div>
            `;
            
            // Re-enable the Get My Location button
            getLocationBtn.disabled = false;
            getLocationBtn.textContent = 'Get My Location';
            
            // Hide tracking buttons
            stopTrackingBtn.style.display = 'none';
            centerLocationBtn.style.display = 'none';
        });
        
        // Center location button
        centerLocationBtn.addEventListener('click', function() {
            if (currentLocation && map) {
                map.setView([currentLocation.latitude, currentLocation.longitude], 18);
                if (marker) {
                    marker.openPopup();
                }
            }
        });
        
        // Manual location entry
        useManualLocationBtn.addEventListener('click', function() {
            const lat = parseFloat(document.getElementById('manual-lat').value);
            const lng = parseFloat(document.getElementById('manual-lng').value);
            
            if (isNaN(lat) || isNaN(lng)) {
                alert('Please enter valid latitude and longitude values.');
                return;
            }
            
            if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                alert('Invalid coordinates. Latitude must be between -90 and 90, Longitude between -180 and 180.');
                return;
            }
            
            currentLocation = {
                latitude: lat,
                longitude: lng,
                accuracy: 0 // Manual entry, no accuracy info
            };
            
            locationStatus.innerHTML = `
                <div class="success">
                    <p>✅ Manual location set successfully!</p>
                    <p><strong>Latitude:</strong> ${currentLocation.latitude.toFixed(6)}</p>
                    <p><strong>Longitude:</strong> ${currentLocation.longitude.toFixed(6)}</p>
                    <p><strong>Source:</strong> Manual Entry</p>
                </div>
            `;
            
            updateLocationBtn.disabled = false;
            refreshLocationBtn.style.display = 'inline-flex';
            stopTrackingBtn.style.display = 'inline-flex';
            manualLocationDiv.style.display = 'none';
            
            // Show map with location
            showLocationOnMap(currentLocation);
            
            // Start continuous location tracking (like Google Maps)
            startLocationTracking();
        });
        
        // Update location to server
        updateLocationBtn.addEventListener('click', function() {
            console.log('Update Location button clicked');
            console.log('Current location:', currentLocation);
            
            if (!currentLocation) {
                alert('Please get your location first.');
                return;
            }
            
            // Get the first active shuttle ID (assuming driver has one active shuttle)
            const shuttleRows = document.querySelectorAll('tbody tr');
            let shuttleId = null;
            
            console.log('Found shuttle rows:', shuttleRows.length);
            
            for (let row of shuttleRows) {
                const statusCell = row.querySelector('td:nth-child(3)'); // Status column
                console.log('Status cell text:', statusCell ? statusCell.textContent.trim() : 'No status cell');
                if (statusCell && statusCell.textContent.trim().toLowerCase().includes('active')) {
                    const form = row.querySelector('form');
                    if (form) {
                        const shuttleIdInput = form.querySelector('input[name="shuttle_id"]');
                        if (shuttleIdInput) {
                            shuttleId = shuttleIdInput.value;
                            console.log('Found active shuttle ID:', shuttleId);
                            break;
                        }
                    }
                }
            }
            
            // Fallback: if no active shuttle found, try to get any shuttle ID
            if (!shuttleId) {
                for (let row of shuttleRows) {
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
                locationStatus.innerHTML = '<p class="error">❌ No shuttle found. Please ensure you have an assigned shuttle.</p>';
                return;
            }
            
            updateLocationBtn.disabled = true;
            updateLocationBtn.textContent = 'Updating...';
            locationStatus.innerHTML = '<p class="info">🔄 Updating location...</p>';
            
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
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Get the response text first to debug
                return response.text().then(text => {
                    console.log('Raw response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    locationStatus.innerHTML = '<p class="success">✅ Location updated successfully!</p>';
                    lastUpdated.textContent = 'Last updated: ' + new Date().toLocaleTimeString();
                    updateLocationBtn.textContent = 'Update Location';
                    
                    // Show success animation
                    updateLocationBtn.style.background = '#28a745';
                    setTimeout(() => {
                        updateLocationBtn.style.background = '';
                    }, 2000);
                } else {
                    locationStatus.innerHTML = '<p class="error">❌ Failed to update location: ' + (data.message || 'Unknown error') + '</p>';
                    updateLocationBtn.textContent = 'Update Location';
                }
                updateLocationBtn.disabled = false;
            })
            .catch(error => {
                console.error('Location update error:', error);
                locationStatus.innerHTML = '<p class="error">❌ Error updating location: ' + error.message + '</p>';
                updateLocationBtn.disabled = false;
                updateLocationBtn.textContent = 'Update Location';
            });
        });
        
        // Start continuous location tracking (like Google Maps)
        function startLocationTracking() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
            }
            
            const trackingOptions = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 5000 // Allow 5 seconds old location
            };
            
            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    // Update current location
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    
                    // Update status with live info
                    locationStatus.innerHTML = `
                        <div class="success">
                            <p>🔄 Live tracking active</p>
                            <p><strong>Latitude:</strong> ${currentLocation.latitude.toFixed(6)}</p>
                            <p><strong>Longitude:</strong> ${currentLocation.longitude.toFixed(6)}</p>
                            <p><strong>Accuracy:</strong> ${Math.round(currentLocation.accuracy)} meters</p>
                            <p><strong>Last Update:</strong> ${new Date().toLocaleTimeString()}</p>
                        </div>
                    `;
                    
                    // Update map with new location
                    if (map) {
                        map.setView([currentLocation.latitude, currentLocation.longitude], 18);
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        marker = L.marker([currentLocation.latitude, currentLocation.longitude])
                            .addTo(map)
                            .bindPopup(`
                                <div style="text-align: center;">
                                    <h4>📍 Your Live Location</h4>
                                    <p><strong>Lat:</strong> ${currentLocation.latitude.toFixed(6)}</p>
                                    <p><strong>Lng:</strong> ${currentLocation.longitude.toFixed(6)}</p>
                                    <p><strong>Time:</strong> ${new Date().toLocaleTimeString()}</p>
                                    <p><strong>Accuracy:</strong> ${Math.round(currentLocation.accuracy)}m</p>
                                </div>
                            `)
                            .openPopup();
                    }
                    
                    // Auto-update location to server every 30 seconds (like Google Maps)
                    autoUpdateLocationToServer();
                },
                function(error) {
                    console.log('Location tracking error:', error);
                    // Don't show error for tracking, just log it
                },
                trackingOptions
            );
        }
        
        // Stop location tracking
        function stopLocationTracking() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            if (autoUpdateInterval) {
                clearInterval(autoUpdateInterval);
                autoUpdateInterval = null;
            }
            
            // Reset button states
            getLocationBtn.disabled = false;
            getLocationBtn.textContent = 'Get My Location';
            // Keep updateLocationBtn enabled if we have a current location
            updateLocationBtn.disabled = !currentLocation;
        }
        
        // Auto-update location to server (like Google Maps)
        function autoUpdateLocationToServer() {
            if (autoUpdateInterval) {
                clearInterval(autoUpdateInterval);
            }
            
            autoUpdateInterval = setInterval(() => {
                if (currentLocation) {
                    // Get active shuttle ID
                    const shuttleId = getActiveShuttleId();
                    if (shuttleId) {
                        updateLocationToServer(shuttleId, currentLocation);
                    }
                }
            }, 30000); // Update every 30 seconds
        }
        
        // Helper function to update location to server
        function updateLocationToServer(shuttleId, location) {
            fetch('../../backend/update_location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    shuttle_id: shuttleId,
                    latitude: location.latitude,
                    longitude: location.longitude
                })
            })
            .then(response => response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.log('Raw response:', text);
                    return { success: false, message: 'Invalid response from server' };
                }
            }))
            .then(data => {
                if (data.success) {
                    console.log('Location auto-updated:', data.message);
                    // Update last updated time
                    lastUpdated.innerHTML = `<p><strong>Last Updated:</strong> ${new Date().toLocaleTimeString()}</p>`;
                } else {
                    console.log('Auto-update failed:', data.message);
                }
            })
            .catch(error => {
                console.log('Auto-update error:', error);
            });
        }
        
        // Show location on map
        function showLocationOnMap(location) {
            locationMap.style.display = 'block';
            locationMap.classList.add('visible');
            
            if (!map) {
                // Start with a higher zoom level for better accuracy
                map = L.map('location-map').setView([location.latitude, location.longitude], 18);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
            } else {
                // Use higher zoom level for better accuracy
                map.setView([location.latitude, location.longitude], 18);
            }
            
            if (marker) {
                map.removeLayer(marker);
            }
            
            marker = L.marker([location.latitude, location.longitude])
                .addTo(map)
                .bindPopup(`
                    <div style="text-align: center;">
                        <h4>📍 Your Location</h4>
                        <p><strong>Lat:</strong> ${location.latitude.toFixed(6)}</p>
                        <p><strong>Lng:</strong> ${location.longitude.toFixed(6)}</p>
                        <p><strong>Time:</strong> ${new Date().toLocaleTimeString()}</p>
                    </div>
                `)
                .openPopup();
        }
    });
    </script>
    
    <!-- Leaflet CSS and JS for map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
