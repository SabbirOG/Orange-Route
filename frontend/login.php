<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OrangeRoute</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>🚌 OrangeRoute</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="forgot_password.php">Forgot Password</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            <div class="card" style="max-width: 400px; margin: 2rem auto;">
                <h2 class="text-center text-orange">Login to OrangeRoute</h2>
                <p class="text-center mb-2">Welcome back! Please sign in to your account.</p>
                
                <form action="../backend/auth/login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" required placeholder="Enter your email address">
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" required placeholder="Enter your password">
                    </div>

                    <button type="submit" class="btn-primary btn-cta" style="width: 100%;">Login</button>
                </form>

                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error">Access denied or invalid credentials. Please try again.</div>';
                }
                ?>

                <div class="text-center mt-2">
                    <p>Don't have an account? <a href="signup.php" class="text-orange">Sign up here</a></p>
                    <p><a href="forgot_password.php" class="text-orange">Forgot your password?</a></p>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 OrangeRoute - Developed by Sabbir Ahmed. Helping the student community with better transportation tracking.</p>
    </footer>
</body>
</html>
