<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - OrangeRoute</title>
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
                    <li><a href="forgot_password.php">Forgot Password</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            <div class="card" style="max-width: 400px; margin: 2rem auto;">
                <h2 class="text-center text-orange">Create Your Account</h2>
                <p class="text-center mb-2">Join OrangeRoute and start tracking your university shuttles!</p>
                
                <form action="../backend/auth/signup.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" required placeholder="Enter your username">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" required placeholder="example@bscse.uiu.ac.bd">
                        <small style="color: var(--dark-gray); font-size: 0.9rem;">Use your university email address</small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" required placeholder="Enter a strong password">
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%;">Create Account</button>
                </form>

                <div class="text-center mt-2">
                    <p>Already have an account? <a href="login.php" class="text-orange">Login here</a></p>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 OrangeRoute - Developed by Sabbir Ahmed. Helping the student community with better transportation tracking.</p>
    </footer>
</body>
</html>
