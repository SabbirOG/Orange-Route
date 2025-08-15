<?php
session_start();
include '../backend/db.php';
include '../backend/auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$shuttle_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($shuttle_id <= 0) {
    header("Location: dashboards/user_dashboard.php");
    exit();
}

// Get shuttle details
$stmt = $conn->prepare("
    SELECT 
        s.*,
        u.username as driver_name,
        u.email as driver_email
    FROM shuttles s
    JOIN users u ON s.driver_id = u.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $shuttle_id);
$stmt->execute();
$result = $stmt->get_result();
$shuttle = $result->fetch_assoc();
$stmt->close();

if (!$shuttle) {
    header("Location: dashboards/user_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($shuttle['name']); ?> - OrangeRoute</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .shuttle-header {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            color: var(--white);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 12px;
        }
        
        .shuttle-info {
            background: var(--white);
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid rgba(255, 107, 53, 0.1);
        }
        
        .shuttle-info h3 {
            margin: 0 0 1.5rem 0;
            color: var(--primary-orange);
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .shuttle-info h3::before {
            content: "📋";
            font-size: 1.2rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        @media (min-width: 1200px) {
            .info-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 1rem;
            }
        }
        
        .info-item {
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.05) 0%, rgba(255, 107, 53, 0.02) 100%);
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 107, 53, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.15);
            border-color: rgba(255, 107, 53, 0.2);
        }
        
        .info-item::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
        }
        
        .info-label {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: var(--text-dark);
            font-weight: 600;
            line-height: 1.4;
        }
        
        .info-item:nth-child(1) .info-label::before { content: "👨‍💼"; }
        .info-item:nth-child(2) .info-label::before { content: "🛣️"; }
        .info-item:nth-child(3) .info-label::before { content: "⚡"; }
        .info-item:nth-child(4) .info-label::before { content: "🕐"; }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .status-indicator.active {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .status-indicator.traffic {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .status-indicator.inactive {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .status-indicator span {
            font-size: 0.8rem;
        }
        
        /* Fix navbar consistency */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-section h1 {
            color: var(--white);
            margin: 0;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 1.5rem;
        }
        
        nav a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }
        
        nav a:hover {
            opacity: 0.8;
        }
        
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .status-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        
        .status-active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        
        .status-traffic {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%);
            color: #ffc107;
            border: 2px solid rgba(255, 193, 7, 0.4);
        }
        
        .status-inactive {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(220, 53, 69, 0.1) 100%);
            color: #dc3545;
            border: 2px solid rgba(220, 53, 69, 0.4);
        }
        
        .status-badge span {
            font-size: 1.2rem;
            font-weight: 900;
        }
        
        .map-container {
            height: 60vh;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin: 1rem 0;
        }
        
        .location-status {
            background: var(--white);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }
        
        .refresh-btn {
            background: transparent;
            color: var(--primary-orange);
            border: 2px solid var(--primary-orange);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        
        .refresh-btn:hover {
            background: var(--primary-orange);
            color: white;
            transform: rotate(180deg);
        }
        
        .refresh-btn:active {
            transform: rotate(180deg) scale(0.95);
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .last-updated {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-align: center;
            margin-top: 1rem;
        }
        
        .no-location {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .no-location-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .map-container {
                height: 50vh;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .location-status > div:first-child {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .refresh-btn {
                align-self: flex-end;
            }
            
            .shuttle-header > div {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 1rem !important;
            }
            
            .shuttle-header > div > div:last-child {
                align-self: flex-end;
            }
            
            .info-grid {
                grid-template-columns: 1fr !important;
                gap: 0.75rem !important;
            }
            
            .shuttle-info {
                padding: 1.5rem !important;
            }
            
            .info-item {
                padding: 1rem !important;
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
        <!-- Shuttle Header -->
        <div class="shuttle-header">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="font-size: 2rem;">🚌</div>
                    <div>
                        <h1 style="margin: 0 0 0.25rem 0; font-size: 1.5rem; font-weight: 700;"><?php echo htmlspecialchars($shuttle['name']); ?></h1>
                        <p style="margin: 0; font-size: 1rem; opacity: 0.9;"><?php echo htmlspecialchars($shuttle['route_name']); ?></p>
                    </div>
                </div>
                <div>
                    <?php if($shuttle['status'] === 'active'): ?>
                        <span class="status-badge <?php echo $shuttle['traffic_status'] ? 'status-traffic' : 'status-active'; ?>">
                            <span>●</span>
                            <?php echo $shuttle['traffic_status'] ? 'Traffic Delay' : 'Active'; ?>
                        </span>
                    <?php else: ?>
                        <span class="status-badge status-inactive">
                            <span>●</span>
                            Inactive
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Shuttle Information -->
        <div class="shuttle-info">
            <h3>Shuttle Details</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Driver</span>
                    <span class="info-value"><?php echo htmlspecialchars($shuttle['driver_name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Route</span>
                    <span class="info-value"><?php echo htmlspecialchars($shuttle['route_name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value status-value">
                        <?php if($shuttle['status'] === 'active'): ?>
                            <span class="status-indicator <?php echo $shuttle['traffic_status'] ? 'traffic' : 'active'; ?>">
                                <span>●</span>
                                <?php echo $shuttle['traffic_status'] ? 'Traffic Delay' : 'Active'; ?>
                            </span>
                        <?php else: ?>
                            <span class="status-indicator inactive">
                                <span>●</span>
                                Inactive
                            </span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last Updated</span>
                    <span class="info-value">
                        <?php 
                        if($shuttle['location_updated_at']) {
                            echo date('M j, Y g:i A', strtotime($shuttle['location_updated_at']));
                        } else {
                            echo 'Never';
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Location Status -->
        <div class="location-status">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0;">📍 Live Location</h3>
                <button id="refresh-btn" class="refresh-btn" title="Refresh Location">🔄</button>
            </div>
            <div id="location-info">
                <p>Loading location data...</p>
            </div>
            <div id="last-updated" class="last-updated"></div>
        </div>
        
        <!-- Map Container -->
        <div id="mapid" class="map-container"></div>
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
        const shuttleId = <?php echo $shuttle_id; ?>;
        let map = null;
        let marker = null;
        let refreshInterval = null;
        
        // Initialize map
        function initMap() {
            try {
                // Default center (UIU location)
                const defaultCenter = [23.7806, 90.4192];
                
                map = L.map('mapid').setView(defaultCenter, 12);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                
                console.log('Map initialized successfully');
            } catch (error) {
                console.error('Error initializing map:', error);
                document.getElementById('mapid').innerHTML = 
                    '<div class="no-location"><div class="no-location-icon">🗺️</div><h4>Map Loading Error</h4><p>Unable to load the map. Please refresh the page or try again later.</p></div>';
            }
        }
        
        // Load shuttle location
        function loadShuttleLocation() {
            const locationInfo = document.getElementById('location-info');
            locationInfo.innerHTML = '<p>Loading location data...</p>';
            
            fetch(`../backend/get_shuttle_location.php?shuttle_id=${shuttleId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateLocationInfo(data.shuttle);
                        updateMapMarker(data.shuttle);
                        document.getElementById('last-updated').textContent = 
                            'Last updated: ' + new Date().toLocaleTimeString();
                    } else {
                        console.error('Failed to load shuttle location:', data.message);
                        locationInfo.innerHTML = '<p style="color: #dc3545;">Error loading location: ' + data.message + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading shuttle location:', error);
                    locationInfo.innerHTML = '<p style="color: #dc3545;">Error loading location. Please check your connection and try again.</p>';
                });
        }
        
        // Update location info
        function updateLocationInfo(shuttle) {
            const locationInfo = document.getElementById('location-info');
            
            if (!shuttle.has_location) {
                locationInfo.innerHTML = `
                    <div class="no-location">
                        <div class="no-location-icon">📍</div>
                        <h4>No Location Data</h4>
                        <p>This shuttle hasn't shared its location yet.</p>
                        <p><small>Location will appear when the driver starts tracking.</small></p>
                    </div>
                `;
                return;
            }
            
            const lastUpdate = new Date(shuttle.location_updated_at).toLocaleString();
            const statusText = shuttle.traffic_status ? 'Traffic Delay' : 'Normal';
            const statusColor = shuttle.traffic_status ? '#ffc107' : '#28a745';
            
            locationInfo.innerHTML = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <strong>Latitude:</strong> ${shuttle.latitude.toFixed(6)}
                    </div>
                    <div>
                        <strong>Longitude:</strong> ${shuttle.longitude.toFixed(6)}
                    </div>
                    <div>
                        <strong>Status:</strong> 
                        <span style="color: ${statusColor}; font-weight: 600;">${statusText}</span>
                    </div>
                    <div>
                        <strong>Last Update:</strong> ${lastUpdate}
                    </div>
                </div>
                <div style="margin-top: 1rem; text-align: center;">
                    <a href="https://www.google.com/maps?q=${shuttle.latitude},${shuttle.longitude}" 
                       target="_blank" 
                       style="color: #007bff; text-decoration: none; font-weight: 600;">
                        📍 View on Google Maps
                    </a>
                </div>
            `;
        }
        
        // Update map marker
        function updateMapMarker(shuttle) {
            if (!map) return;
            
            // Clear existing marker
            if (marker) {
                map.removeLayer(marker);
            }
            
            if (!shuttle.has_location) {
                return;
            }
            
            const isTraffic = shuttle.traffic_status;
            const iconColor = isTraffic ? '#ffc107' : '#28a745';
            
            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="
                    background-color: ${iconColor};
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    border: 4px solid white;
                    box-shadow: 0 3px 8px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16px;
                ">🚌</div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            
            marker = L.marker([shuttle.latitude, shuttle.longitude], {
                icon: customIcon
            }).addTo(map);
            
            marker.bindPopup(`
                <div style="text-align: center;">
                    <h4 style="margin: 0 0 0.5rem 0;">${shuttle.name}</h4>
                    <p style="margin: 0 0 0.25rem 0;"><strong>Route:</strong> ${shuttle.route_name}</p>
                    <p style="margin: 0 0 0.25rem 0;"><strong>Driver:</strong> ${shuttle.driver_name}</p>
                    <p style="margin: 0 0 0.25rem 0;"><strong>Status:</strong> ${isTraffic ? '🚦 Traffic Delay' : '✅ Normal'}</p>
                    <p style="margin: 0; font-size: 0.9rem; color: #666;">Last Update: ${new Date(shuttle.location_updated_at).toLocaleString()}</p>
                </div>
            `).openPopup();
            
            // Center map on marker
            map.setView([shuttle.latitude, shuttle.longitude], 15);
        }
        
        // Refresh button
        document.getElementById('refresh-btn').addEventListener('click', function() {
            const btn = this;
            btn.style.pointerEvents = 'none';
            btn.textContent = '⏳';
            btn.style.animation = 'spin 1s linear infinite';
            
            loadShuttleLocation();
            
            setTimeout(() => {
                btn.style.pointerEvents = 'auto';
                btn.textContent = '🔄';
                btn.style.animation = 'none';
            }, 1000);
        });
        
        // Auto-refresh every 30 seconds
        function startAutoRefresh() {
            refreshInterval = setInterval(loadShuttleLocation, 30000);
        }
        
        // Initialize everything
        initMap();
        loadShuttleLocation();
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
