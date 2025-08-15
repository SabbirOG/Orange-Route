<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - OrangeRoute</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            <?php include 'includes/navigation.php'; ?>
        </div>
    </header>
    
    <main>
        <div class="container">
            <div class="card" style="max-width: 400px; margin: 2rem auto;">
                <h2 class="text-center text-orange">Forgot Password</h2>
                
                <?php if (isset($_GET['error']) && $_GET['error'] === 'failed'): ?>
                    <div class="alert alert-danger" style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #dc3545;">
                        <strong>Error!</strong> Failed to send reset code. Please try again.
                    </div>
                <?php endif; ?>
                
                <p class="text-center mb-2">Enter your email to receive a reset code.</p>
                
                <form action="../backend/auth/forgot_password.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" required placeholder="Enter your email address">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">Send Reset Code</button>
                </form>
                
                <div class="text-center mt-2">
                    <a href="login.php" class="btn-secondary">Back to Login</a>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 OrangeRoute - Developed by Sabbir Ahmed. Helping the student community with better transportation tracking.</p>
    </footer>
    <script src="../assets/js/main.js"></script>
</body>
</html>
