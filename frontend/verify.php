<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account - OrangeRoute</title>
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
                <h2 class="text-center text-orange">Verify Your Account</h2>
                <p class="text-center mb-2">Enter your email and verification code to activate your account.</p>
                
                <form method="POST" action="../backend/auth/verify.php">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" required placeholder="Enter your email address">
                    </div>
                    <div class="form-group">
                        <label for="code">Verification Code:</label>
                        <input type="text" name="code" id="code" required placeholder="Enter verification code">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">Verify Account</button>
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