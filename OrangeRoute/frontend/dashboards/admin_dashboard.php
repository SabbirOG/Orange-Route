<?php
session_start();
include '../../backend/db.php';
include '../../backend/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

// Fetch all users
$result = $conn->query("SELECT * FROM users");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OrangeRoute</title>
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
                <h2 class="text-orange">Admin Dashboard</h2>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! Manage users and shuttle assignments.</p>
            </div>
            
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>👥 Total Users</h3>
                    <p><?php echo count($users); ?> registered users</p>
                </div>
                <div class="dashboard-card">
                    <h3>🚌 Drivers</h3>
                    <p><?php echo count(array_filter($users, function($u) { return $u['role'] === 'driver'; })); ?> drivers</p>
                </div>
                <div class="dashboard-card">
                    <h3>👤 Students</h3>
                    <p><?php echo count(array_filter($users, function($u) { return $u['role'] === 'user'; })); ?> students</p>
                </div>
            </div>

            <div class="card">
                <h3 class="text-orange">User Management</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="<?php echo $user['role'] === 'admin' ? 'status-active' : ($user['role'] === 'driver' ? 'status-inactive' : ''); ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="<?php echo $user['verified'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $user['verified'] ? 'Verified' : 'Pending'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($user['role'] === 'driver'): ?>
                                        <form action="../../backend/assign_shuttle.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="driver_id" value="<?php echo $user['id']; ?>">
                                            <select name="route_name" required style="margin-right: 0.5rem;">
                                                <?php echo getRouteOptions(); ?>
                                            </select>
                                            <button type="submit" class="btn-primary">Assign Route</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: var(--dark-gray);">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card">
                <h3 class="text-orange">System Overview</h3>
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h4>Active Shuttles</h4>
                        <p>Check shuttle status and routes</p>
                    </div>
                    <div class="dashboard-card">
                        <h4>User Activity</h4>
                        <p>Monitor user engagement and usage</p>
                    </div>
                    <div class="dashboard-card">
                        <h4>System Health</h4>
                        <p>Database and server status</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 OrangeRoute - Developed by Sabbir Ahmed. Helping the student community with better transportation tracking.</p>
    </footer>
</body>
</html>
