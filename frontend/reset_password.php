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
    <title>Reset Password - OrangeRoute</title>
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
                <h2 class="text-center text-orange">Reset Password</h2>
                
                <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                    <div class="success-message" style="text-align: center; padding: 2rem; background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%); border-radius: 12px; margin-bottom: 2rem; border: 2px solid #28a745; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                        <h3 style="color: #28a745; margin-bottom: 1rem; font-size: 1.5rem; font-weight: 700;">Password Reset Successful!</h3>
                        <p style="color: #155724; margin-bottom: 1.5rem; font-size: 1.1rem;">Your password has been successfully updated. You can now log in with your new password.</p>
                        <a href="login.php" class="btn-primary" style="display: inline-block; padding: 0.75rem 2rem; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 107, 53, 0.2);">
                            Continue to Login
                        </a>
                    </div>
                <?php elseif (isset($_GET['code']) && isset($_GET['email'])): ?>
                    <div class="alert alert-success" style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #28a745;">
                        <strong>Reset Code Generated!</strong><br>
                        Your reset code is: <strong style="font-size: 1.2rem; color: #FF6B35;"><?php echo htmlspecialchars($_GET['code']); ?></strong><br>
                        <small>This code will expire in 1 hour.</small>
                    </div>
                <?php endif; ?>
                
                <p class="text-center mb-2">Enter your email, reset code, and new password.</p>
                
                <form action="../backend/auth/reset_password.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" required placeholder="Enter your email address" 
                               value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="code">Reset Code:</label>
                        <input type="text" name="code" id="code" required placeholder="Enter reset code" 
                               value="<?php echo isset($_GET['code']) ? htmlspecialchars($_GET['code']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" name="new_password" id="new_password" required placeholder="Enter new password">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">Reset Password</button>
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
