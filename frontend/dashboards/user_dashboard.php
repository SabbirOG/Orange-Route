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
    <style>
        .shuttle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15) !important;
        }
        
        .shuttle-card:hover .status-badge {
            transform: scale(1.05);
        }
        
        .shuttle-card:hover .btn-primary {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4) !important;
        }
        
        .shuttle-card:hover > div:last-child {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .shuttle-grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            
            .shuttle-card {
                padding: 1.5rem !important;
            }
            
            .card[style*="background: linear-gradient"] {
                padding: 1rem !important;
            }
            
            .card[style*="background: linear-gradient"] > div {
                flex-direction: column !important;
                gap: 0.5rem !important;
            }
            
            .card[style*="background: linear-gradient"] h2 {
                font-size: 1.25rem !important;
            }
            
            .card[style*="background: linear-gradient"] p {
                font-size: 0.9rem !important;
            }
        }
    </style>
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
            
            <!-- Active Shuttles Overview -->
            <div class="card" style="background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%); color: var(--white); text-align: center; padding: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
                    <div style="font-size: 2rem;">🚌</div>
                    <div>
                        <h2 style="margin: 0 0 0.25rem 0; font-size: 1.5rem;">Active Shuttles</h2>
                        <p style="margin: 0; font-size: 1rem; opacity: 0.9;">
                            <?php echo count($activeShuttles); ?> shuttle<?php echo count($activeShuttles) !== 1 ? 's' : ''; ?> running
                        </p>
                    </div>
                </div>
            </div>
            
            <?php if (count($activeShuttles) > 0): ?>
                <div class="shuttle-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                    <?php foreach($activeShuttles as $shuttle): ?>
                        <div class="shuttle-card" style="background: var(--white); border: 1px solid var(--light-gray); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <!-- Card Header -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                                <div>
                                    <h4 style="margin: 0 0 0.5rem 0; color: var(--primary-orange); font-size: 1.3rem; font-weight: 700;">🚌 <?php echo htmlspecialchars($shuttle['name']); ?></h4>
                                    <p style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;"><?php echo htmlspecialchars($shuttle['route_name']); ?></p>
                                </div>
                                <div style="text-align: right;">
                                    <span class="status-badge <?php echo $shuttle['traffic_status'] ? 'status-traffic' : 'status-active'; ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: <?php echo $shuttle['traffic_status'] ? 'rgba(255, 193, 7, 0.15)' : 'rgba(40, 167, 69, 0.15)'; ?>; color: <?php echo $shuttle['traffic_status'] ? '#ffc107' : '#28a745'; ?>; border: 2px solid <?php echo $shuttle['traffic_status'] ? 'rgba(255, 193, 7, 0.3)' : 'rgba(40, 167, 69, 0.3)'; ?>;">
                                        <span style="font-size: 0.8rem;">●</span>
                                        <?php echo $shuttle['traffic_status'] ? 'Traffic Delay' : 'Active'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Card Body -->
                            <div style="margin-bottom: 1.5rem;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                    <div style="text-align: center; padding: 0.75rem; background: rgba(255, 107, 53, 0.05); border-radius: 8px;">
                                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">👨‍💼</div>
                                        <p style="margin: 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Driver</p>
                                        <p style="margin: 0; font-size: 1rem; color: var(--text-dark); font-weight: 600;"><?php echo htmlspecialchars($shuttle['driver_name']); ?></p>
                                    </div>
                                    <div style="text-align: center; padding: 0.75rem; background: rgba(255, 107, 53, 0.05); border-radius: 8px;">
                                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🕐</div>
                                        <p style="margin: 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">Last Update</p>
                                        <p style="margin: 0; font-size: 1rem; color: var(--text-dark); font-weight: 600;">
                                            <?php 
                                            if($shuttle['location_updated_at']) {
                                                echo date('M j, g:i A', strtotime($shuttle['location_updated_at']));
                                            } else {
                                                echo 'Never';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Card Footer -->
                            <div style="text-align: center;">
                                <a href="../shuttle_profile.php?id=<?php echo $shuttle['id']; ?>" 
                                   class="btn-primary" 
                                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2rem; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3); background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);">
                                    <span>📍</span>
                                    View Live Map
                                </a>
                            </div>
                            
                            <!-- Hover Effect -->
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(255, 107, 53, 0.05) 0%, rgba(255, 107, 53, 0.02) 100%); opacity: 0; transition: opacity 0.3s ease; pointer-events: none;"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card text-center">
                    <div style="padding: 3rem 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">🚌</div>
                        <h3>No Active Shuttles</h3>
                        <p>There are currently no shuttles running. Check back later!</p>
                    </div>
                </div>
            <?php endif; ?>
            
            
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
</body>
</html>
