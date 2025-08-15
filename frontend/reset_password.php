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
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="error-message" style="text-align: center; padding: 2rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%); border-radius: 12px; margin-bottom: 2rem; border: 2px solid #dc3545; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                        <h3 style="color: #dc3545; margin-bottom: 1rem; font-size: 1.5rem; font-weight: 700;">Password Reset Failed</h3>
                        <?php
                        $error = $_GET['error'];
                        switch($error) {
                            case 'email_not_found':
                                echo '<p style="color: #721c24; margin-bottom: 1.5rem; font-size: 1.1rem;">Email address not found. Please check your email and try again.</p>';
                                break;
                            case 'invalid_code':
                                echo '<p style="color: #721c24; margin-bottom: 1.5rem; font-size: 1.1rem;">Invalid reset code. Please check the code and try again.</p>';
                                break;
                            case 'code_used':
                                echo '<p style="color: #721c24; margin-bottom: 1.5rem; font-size: 1.1rem;">This reset code has already been used. Please request a new one.</p>';
                                break;
                            case 'code_expired':
                                echo '<p style="color: #721c24; margin-bottom: 1.5rem; font-size: 1.1rem;">Reset code has expired. Please request a new one.</p>';
                                break;
                            case 'database_error':
                                echo '<p style="color: #721c24; margin-bottom: 1.5rem; font-size: 1.1rem;">Database error occurred. Please try again later.</p>';
                                break;
                            case 'last_password':
                                echo '<p style="color: #721c24; margin-bottom: 1.5rem; font-size: 1.1rem;">This was your last correct password. Please choose a different password.</p>';
                                break;
                            case 'old_password':
                                echo '<p style="color: #721c24; margin-bottom: 1.5rem; font-size: 1.1rem;">This is an old password. Please choose a different password.</p>';
                                break;
                            default:
                                echo '<p style="color: #721c24; margin-bottom: 1.5rem; font-size: 1.1rem;">An error occurred. Please try again.</p>';
                        }
                        ?>
                        <a href="forgot_password.php" class="btn-primary" style="display: inline-block; padding: 0.75rem 2rem; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 107, 53, 0.2);">
                            Request New Code
                        </a>
                    </div>
                <?php elseif (isset($_GET['code']) && isset($_GET['email'])): ?>
                    <div class="alert alert-success" style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #28a745;">
                        <strong>Reset Code Generated!</strong><br>
                        Your reset code is: <strong style="font-size: 1.2rem; color: #FF6B35;"><?php echo htmlspecialchars($_GET['code']); ?></strong><br>
                        <small><strong>Please copy this code and enter it in the form below.</strong></small><br>
                        <small>This code will expire in <span id="countdown" style="color: #dc3545; font-weight: bold;">60</span> seconds.</small>
                    </div>
                <?php endif; ?>
                
                <?php if (!isset($_GET['success']) || $_GET['success'] != '1'): ?>
                    <p class="text-center mb-2">Enter your email, reset code, and new password.</p>
                    
                    <form action="../backend/auth/reset_password.php" method="POST">
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" name="email" id="email" required placeholder="Enter your email address" 
                                   value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="code">Reset Code:</label>
                            <input type="text" name="code" id="code" required placeholder="Enter the 6-digit reset code">
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
                <?php endif; ?>
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
    
    <!-- Countdown Timer for Reset Code -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownElement = document.getElementById('countdown');
        if (countdownElement) {
            let timeLeft = 60;
            
            const countdown = setInterval(function() {
                timeLeft--;
                countdownElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    countdownElement.textContent = 'EXPIRED';
                    countdownElement.style.color = '#dc3545';
                    countdownElement.style.fontSize = '1.1rem';
                    
                    // Disable the form
                    const form = document.querySelector('form');
                    if (form) {
                        form.style.opacity = '0.5';
                        form.style.pointerEvents = 'none';
                    }
                    
                    // Show expired message
                    const alertDiv = document.querySelector('.alert.alert-success');
                    if (alertDiv) {
                        alertDiv.innerHTML = `
                            <strong style="color: #dc3545;">Reset Code Expired!</strong><br>
                            <span style="color: #dc3545;">The reset code has expired. Please request a new one.</span><br>
                            <a href="forgot_password.php" class="btn-primary" style="display: inline-block; margin-top: 0.5rem; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; font-size: 0.9rem;">
                                Request New Code
                            </a>
                        `;
                        alertDiv.style.background = 'rgba(220, 53, 69, 0.1)';
                        alertDiv.style.borderLeftColor = '#dc3545';
                    }
                } else if (timeLeft <= 5) {
                    countdownElement.style.color = '#dc3545';
                    countdownElement.style.fontSize = '1.1rem';
                }
            }, 1000);
        }
    });
    </script>
</body>
</html>
