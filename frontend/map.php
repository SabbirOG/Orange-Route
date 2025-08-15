<?php
session_start();
include '../backend/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Get user info
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Shuttle Tracking - OrangeRoute</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .map-container {
            height: 70vh;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin: 1rem 0;
        }
        
        .shuttle-info {
            background: var(--white);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }
        
        .shuttle-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .shuttle-item:last-child {
            border-bottom: none;
        }
        
        .shuttle-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-active { background-color: #28a745; }
        .status-traffic { background-color: #ffc107; }
        .status-inactive { background-color: #dc3545; }
        
        .refresh-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            background: var(--primary-orange);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }
        
        .last-updated {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <main class="container">
        <div class="hero-section">
            <h1 class="hero-title">🚌 Live Shuttle Tracking</h1>
            <p class="hero-subtitle">Track all active shuttles in real-time</p>
        </div>
        
        <div class="shuttle-info">
            <h3>Active Shuttles</h3>
            <div id="shuttle-list">
                <p>Loading shuttle locations...</p>
            </div>
            <div id="last-updated" class="last-updated"></div>
        </div>
        
        <div id="map" class="map-container"></div>
        
        <button id="refresh-btn" class="refresh-btn" title="Refresh Locations">🔄</button>
    </main>
    
    <footer>
        <p>&copy; 2024 OrangeRoute - Developed by Sabbir Ahmed. Helping the student community with better transportation tracking.</p>
    </footer>
    
    <script src="../assets/js/main.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let map = null;
        let markers = [];
        let refreshInterval = null;
        
        // Initialize map
        function initMap() {
            // Default center (UIU location - you can change this)
            const defaultCenter = [23.7806, 90.4192]; // Dhaka coordinates
            
            map = L.map('map').setView(defaultCenter, 12);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        }
        
        // Load shuttle locations
        function loadShuttleLocations() {
            fetch('../backend/get_shuttle_locations.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateShuttleList(data.shuttles);
                        updateMapMarkers(data.shuttles);
                        document.getElementById('last-updated').textContent = 
                            'Last updated: ' + new Date().toLocaleTimeString();
                    } else {
                        console.error('Failed to load shuttle locations:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading shuttle locations:', error);
                });
        }
        
        // Update shuttle list
        function updateShuttleList(shuttles) {
            const shuttleList = document.getElementById('shuttle-list');
            
            if (shuttles.length === 0) {
                shuttleList.innerHTML = '<p>No active shuttles found.</p>';
                return;
            }
            
            let html = '';
            shuttles.forEach(shuttle => {
                const statusClass = shuttle.traffic_status ? 'status-traffic' : 'status-active';
                const statusText = shuttle.traffic_status ? 'Traffic' : 'Active';
                const lastUpdate = new Date(shuttle.location_updated_at).toLocaleTimeString();
                
                html += `
                    <div class="shuttle-item">
                        <div>
                            <strong>${shuttle.name}</strong> - ${shuttle.route_name}<br>
                            <small>Driver: ${shuttle.driver_name}</small>
                        </div>
                        <div class="shuttle-status">
                            <span class="status-dot ${statusClass}"></span>
                            <span>${statusText}</span>
                            <small>(${lastUpdate})</small>
                        </div>
                    </div>
                `;
            });
            
            shuttleList.innerHTML = html;
        }
        
        // Update map markers
        function updateMapMarkers(shuttles) {
            // Clear existing markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
            
            if (shuttles.length === 0) {
                return;
            }
            
            // Add markers for each shuttle
            shuttles.forEach(shuttle => {
                const isTraffic = shuttle.traffic_status;
                const iconColor = isTraffic ? '#ffc107' : '#28a745';
                
                const customIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background-color: ${iconColor};
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 12px;
                    ">🚌</div>`,
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });
                
                const marker = L.marker([shuttle.latitude, shuttle.longitude], {
                    icon: customIcon
                }).addTo(map);
                
                marker.bindPopup(`
                    <div>
                        <h4>${shuttle.name}</h4>
                        <p><strong>Route:</strong> ${shuttle.route_name}</p>
                        <p><strong>Driver:</strong> ${shuttle.driver_name}</p>
                        <p><strong>Status:</strong> ${isTraffic ? '🚦 Traffic' : '✅ Normal'}</p>
                        <p><strong>Last Update:</strong> ${new Date(shuttle.location_updated_at).toLocaleString()}</p>
                    </div>
                `);
                
                markers.push(marker);
            });
            
            // Fit map to show all markers
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
        
        // Refresh button
        document.getElementById('refresh-btn').addEventListener('click', function() {
            this.style.transform = 'rotate(360deg)';
            loadShuttleLocations();
            setTimeout(() => {
                this.style.transform = 'rotate(0deg)';
            }, 500);
        });
        
        // Auto-refresh every 30 seconds
        function startAutoRefresh() {
            refreshInterval = setInterval(loadShuttleLocations, 30000);
        }
        
        // Initialize everything
        initMap();
        loadShuttleLocations();
        startAutoRefresh();
        
        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    });
    </script>
</body>
</html>
