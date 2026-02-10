<?php
require_once 'config.php';

$token = isset($_GET['token']) ? sanitize_input($_GET['token']) : '';

if (empty($token)) {
    $_SESSION['error'] = 'Invalid password reset link';
    redirect('login.php');
}

// Verify token
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Invalid or expired password reset link. Please request a new one.';
    redirect('forgot_password.php');
}

$reset = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FirstStep</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="login.php" class="back-button">← Back to Login</a>
            <div class="logo-text">
                <h1>FirstStep</h1>
                <p>Set New Password</p>
            </div>
        </div>
    </header>

    <main>
        <div class="container" style="max-width: 500px;">
            <div class="header-section">
                <h2>🔐 Reset Your Password</h2>
                <p>Enter your new password below</p>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <form action="process_reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-section">
                    <div class="form-group">
                        <label>New Password *</label>
                        <input type="password" name="password" minlength="8" placeholder="••••••••" required autofocus>
                        <span class="helper-text">Min 8 characters: uppercase, lowercase, number, special character</span>
                    </div>

                    <div class="form-group">
                        <label>Confirm New Password *</label>
                        <input type="password" name="confirm_password" minlength="8" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                        Reset Password
                    </button>
                </div>
            </form>

            <div style="margin-top: 2rem; padding: 1rem; background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 6px;">
                <p style="margin: 0; color: #92400E; font-size: 0.9rem;">
                    <strong>⏰ Note:</strong> This reset link expires in 24 hours from when it was sent.
                </p>
            </div>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>
