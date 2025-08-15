<?php
session_start();
require_once '../backend/db.php';
require_once '../backend/auth.php';

// Redirect to landing page if user is not authenticated
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: landing.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserById($user_id); // Function to fetch user details from the database

// If user not found, redirect to landing page
if (!$user) {
    session_destroy();
    header("Location: landing.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>User Profile - OrangeRoute</title>
</head>
<body class="profile-page">
    <header>
        <div class="header-container">
            <div class="logo-section">
                <div class="circle">
                    <img src="../assets/images/orangeroute-logo-modified.png" alt="OrangeRoute Logo" class="logo">
                </div>
                <h1>OrangeRoute</h1>
            </div>
            <?php include 'includes/navigation.php'; ?>
        </div>
    </header>
    
    <main>
        <div class="container">
            <h1 class="text-center text-orange">User Profile</h1>
            
            <?php if (isset($_GET['success']) && $_GET['success'] === 'uploaded'): ?>
                <div class="alert alert-success">
                    <strong>Success!</strong> Profile picture uploaded successfully.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <strong>Error!</strong> 
                    <?php 
                    if ($_GET['error'] === 'access_denied') {
                        echo 'Access denied. You can only update your own profile picture.';
                    } else {
                        echo htmlspecialchars($_GET['error']);
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-info">
                <img src="../uploads/profile_pictures/<?php echo $user['profile_picture'] ?: '../assets/images/default_avatar.svg'; ?>" alt="Profile Picture" class="profile-picture">
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Role:</strong> <span class="role-badge role-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></p>
                <p><strong>Status:</strong> <span class="<?php echo $user['verified'] ? 'status-verified' : 'status-not-verified'; ?>"><?php echo $user['verified'] ? 'Verified' : 'Not Verified'; ?></span></p>
                <a href="reset_password.php" class="btn-secondary">Reset Password</a>
            </div>
            
            <div class="card">
                <h3 class="text-orange">Update Profile Picture</h3>
                <form action="../backend/upload.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="form-group">
                        <label for="profile_picture">Choose New Profile Picture:</label>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                    </div>
                    <button type="submit" class="btn-primary">Upload Picture</button>
                </form>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 OrangeRoute - Developed by Sabbir Ahmed. Helping the student community with better transportation tracking.</p>
    </footer>
    <script src="../assets/js/main.js"></script>
</body>
</html>