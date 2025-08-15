<?php
session_start();
include '../../backend/db.php';
include '../../backend/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Get user info
$user = getUserById($_SESSION['user_id']);

// Get active shuttles
$stmt = $conn->prepare("SELECT s.*, u.username as driver_name FROM shuttles s JOIN users u ON s.driver_id = u.id WHERE s.status = 'active'");
$stmt->execute();
$result = $stmt->get_result();
$activeShuttles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - OrangeRoute</title>
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
                <h2 class="text-orange">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p>Track your university shuttles and stay connected with drivers.</p>
            </div>
            
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>🚌 Active Shuttles</h3>
                    <p><?php echo count($activeShuttles); ?> shuttles currently running</p>
                </div>
                <div class="dashboard-card">
                    <h3>💬 Chat</h3>
                    <p>Communicate with drivers and other users</p>
                </div>
                <div class="dashboard-card">
                    <h3>📍 Live Tracking</h3>
                    <p>Real-time shuttle location updates</p>
                </div>
            </div>
            
            <?php if (count($activeShuttles) > 0): ?>
                <div class="card">
                    <h3 class="text-orange">Currently Active Shuttles</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Shuttle Name</th>
                                <th>Route</th>
                                <th>Driver</th>
                                <th>Status</th>
                                <th>Traffic</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($activeShuttles as $shuttle): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shuttle['name']); ?></td>
                                    <td><?php echo htmlspecialchars($shuttle['route_name']); ?></td>
                                    <td><?php echo htmlspecialchars($shuttle['driver_name']); ?></td>
                                    <td><span class="status-active">Active</span></td>
                                    <td><?php echo $shuttle['traffic_status'] ? '🚦 Traffic' : '✅ Normal'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="card text-center">
                    <h3>No Active Shuttles</h3>
                    <p>There are currently no shuttles running. Check back later!</p>
                </div>
            <?php endif; ?>
            
            <!-- Live Map Section -->
            <div class="card">
                <h3>🗺️ Live Shuttle Tracking</h3>
                <p>Track all active shuttles in real-time on the map</p>
                <div style="text-align: center; margin: 1rem 0;">
                    <a href="../map.php" class="btn-primary">View Live Map</a>
                </div>
            </div>
            
            <div class="card">
                <h3 class="text-orange">General Chat</h3>
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
</body>
</html>
