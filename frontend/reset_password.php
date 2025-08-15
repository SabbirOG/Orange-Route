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
            <div class="card" style="max-width: 400px; margin: 2rem auto;">
                <h2 class="text-center text-orange">Reset Password</h2>
                <p class="text-center mb-2">Enter your email, reset code, and new password.</p>
                
                <form action="../backend/auth/reset_password.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" required placeholder="Enter your email address">
                    </div>
                    <div class="form-group">
                        <label for="code">Reset Code:</label>
                        <input type="text" name="code" id="code" required placeholder="Enter reset code">
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
</body>
</html>
