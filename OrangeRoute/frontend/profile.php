<?php
session_start();
require_once '../backend/db.php';
require_once '../backend/auth.php';

$user_id = $_SESSION['user_id'] ?? null;
$user = null;

if ($user_id) {
    $user = getUserById($user_id); // Function to fetch user details from the database
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
<body>
    <header>
        <div class="header-container">
            <h1>🚌 OrangeRoute</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            <h1 class="text-center text-orange">User Profile</h1>
            
            <?php if ($user): ?>
                <div class="profile-info">
                    <img src="../uploads/profile_pictures/<?php echo $user['profile_picture'] ?: 'default_avatar.png'; ?>" alt="Profile Picture" class="profile-picture">
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></p>
                    <p><strong>Status:</strong> <?php echo $user['verified'] ? 'Verified' : 'Not Verified'; ?></p>
                    <a href="reset_password.php" class="btn-secondary">Reset Password</a>
                </div>
                
                <div class="card">
                    <h3 class="text-orange">Update Profile Picture</h3>
                    <form action="../backend/upload.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <div class="form-group">
                            <label for="profile_picture">Choose New Profile Picture:</label>
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                        </div>
                        <button type="submit" class="btn-primary">Upload Picture</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="card text-center">
                    <h2>User not found</h2>
                    <p>Please <a href="login.php" class="text-orange">login</a> again to access your profile.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 OrangeRoute - Developed by Sabbir Ahmed. Helping the student community with better transportation tracking.</p>
    </footer>
</body>
</html>