<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - FirstStep</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="login.php" class="back-button">← Back to Login</a>
            <div class="logo-text">
                <h1>FirstStep</h1>
                <p>Password Recovery</p>
            </div>
        </div>
    </header>

    <main>
        <div class="container" style="max-width: 500px;">
            <div class="header-section">
                <h2>🔒 Forgot Password</h2>
                <p>Enter your email and we'll send you a password reset link</p>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <form action="process_forgot_password.php" method="POST">
                <div class="form-section">
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" placeholder="your@email.com" required autofocus>
                        <span class="helper-text">We'll send a password reset link to this email</span>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                        Send Reset Link
                    </button>
                </div>
            </form>

            <div class="signup-link" style="margin-top: 2rem;">
                <p>Remember your password? <a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </main>
</body>
</html>
