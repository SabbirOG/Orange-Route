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
            <h1>🚌 OrangeRoute</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../profile.php">Profile</a></li>
                    <li><a href="../../backend/auth.php?action=logout">Logout</a></li>
                </ul>
            </nav>
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
                                            <input type="hidden" name="shuttle_id" value="<?php echo $shuttle['id']; ?>">
                                            <?php if($shuttle['status'] === 'inactive'): ?>
                                                <button type="submit" name="action" value="start" class="btn-primary">Start Route</button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="down" class="btn-secondary">Stop Route</button>
                                            <?php endif; ?>
                                            <button type="submit" name="action" value="traffic" class="btn-secondary">Report Traffic</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
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
</body>
</html>
