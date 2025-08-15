<?php
session_start();
include '../backend/db.php';

// Get username from URL parameter
$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Successful - OrangeRoute</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <div class="circle">
                    <img src="../assets/images/orangeroute-logo-modified.png" alt="OrangeRoute Logo" class="logo">
                </div>
                <h1>OrangeRoute</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="landing.php">Home</a></li>
                    <li><a href="login.php" class="btn-secondary">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <div class="hero-section">
            <div class="success-message" style="text-align: center; padding: 3rem; background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%); border-radius: 12px; margin: 2rem auto; max-width: 600px; border: 2px solid #28a745; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                <h1 class="hero-title" style="color: #28a745; margin-bottom: 1rem;">Welcome to OrangeRoute, <?php echo $username; ?>!</h1>
                <p class="hero-subtitle" style="color: #155724; margin-bottom: 2rem; font-size: 1.2rem;">Your account has been created successfully. You can now log in and start using OrangeRoute.</p>
                
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="login.php" class="btn-primary" style="display: inline-block; padding: 0.75rem 2rem; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 107, 53, 0.2);">
                        Login Now
                    </a>
                    <a href="landing.php" class="btn-secondary" style="display: inline-block; padding: 0.75rem 2rem; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease;">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
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
</body>
</html>
