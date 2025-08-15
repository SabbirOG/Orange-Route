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
        
        .shuttle-item {
            transition: all 0.3s ease;
        }
        
        .shuttle-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        @media (max-width: 768px) {
            .map-container {
                height: 50vh;
            }
            
            .refresh-btn {
                bottom: 1rem;
                right: 1rem;
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            
            .shuttle-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .shuttle-status {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-section">
                <div class="circle">
                    <img src="../assets/images/orangeroute-logo-modified.png" alt="OrangeRoute Logo" class="logo">
                </div>
                <h1>OrangeRoute</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboards/user_dashboard.php">Dashboard</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="../backend/auth.php?action=logout">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
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
        
        <div id="mapid" class="map-container"></div>
        
        <button id="refresh-btn" class="refresh-btn" title="Refresh Locations">🔄</button>
    </main>
    
    <footer>
        <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.1rem;">📱</span>
                <a href="#" style="color: var(--white); text-decoration: none; font-weight: 500;">Follow Us</a>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.1rem;">📧</span>
                <a href="mailto:contact@orangeroute.com" style="color: var(--white); text-decoration: none; font-weight: 500;">Contact Us</a>
            </div>
        </div>
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
            try {
                // Default center (UIU location - you can change this)
                const defaultCenter = [23.7806, 90.4192]; // Dhaka coordinates
                
                map = L.map('mapid').setView(defaultCenter, 12);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                
                console.log('Map initialized successfully');
            } catch (error) {
                console.error('Error initializing map:', error);
                document.getElementById('mapid').innerHTML = 
                    '<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa; color: #6c757d; text-align: center; padding: 2rem;"><div><h4>Map Loading Error</h4><p>Unable to load the map. Please refresh the page or try again later.</p></div></div>';
            }
        }
        
        // Load shuttle locations
        function loadShuttleLocations() {
            const shuttleList = document.getElementById('shuttle-list');
            shuttleList.innerHTML = '<p>Loading shuttle locations...</p>';
            
            fetch('../backend/get_shuttle_locations.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateShuttleList(data.shuttles);
                        updateMapMarkers(data.shuttles);
                        document.getElementById('last-updated').textContent = 
                            'Last updated: ' + new Date().toLocaleTimeString();
                    } else {
                        console.error('Failed to load shuttle locations:', data.message);
                        shuttleList.innerHTML = '<p style="color: #dc3545;">Error loading shuttle locations: ' + data.message + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading shuttle locations:', error);
                    shuttleList.innerHTML = '<p style="color: #dc3545;">Error loading shuttle locations. Please check your connection and try again.</p>';
                });
        }
        
        // Update shuttle list
        function updateShuttleList(shuttles) {
            const shuttleList = document.getElementById('shuttle-list');
            
            if (shuttles.length === 0) {
                shuttleList.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: #6c757d;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🚌</div>
                        <h4>No Active Shuttles</h4>
                        <p>There are currently no shuttles with live location tracking.</p>
                        <p><small>Check back later or contact your driver for updates.</small></p>
                    </div>
                `;
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
            const btn = this;
            btn.style.transform = 'rotate(360deg)';
            btn.style.pointerEvents = 'none';
            btn.innerHTML = '⏳';
            
            loadShuttleLocations();
            
            setTimeout(() => {
                btn.style.transform = 'rotate(0deg)';
                btn.style.pointerEvents = 'auto';
                btn.innerHTML = '🔄';
            }, 1000);
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
