<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FirstStep</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php if (file_exists('google_config.php')) { require_once 'google_config.php'; } ?>
    <style>
        .google-signin-container {
            margin: 1.5rem 0;
            text-align: center;
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: var(--gray);
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }
        .divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }
        .forgot-password-link {
            text-align: right;
            margin-top: 0.5rem;
        }
        .forgot-password-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }
        .forgot-password-link a:hover {
            text-decoration: underline;
        }
        #g_id_onload {
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="landing.php" class="back-button">← Back</a>
            <div class="logo-text">
                <h1>FirstStep</h1>
                <p>Internship Connection Platform</p>
            </div>
        </div>
    </header>

    <main>
        <div class="container login-container">
            <div class="header-section">
                <h2>Sign In to FirstStep</h2>
                <p>Access your account</p>
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

            <?php if (defined('GOOGLE_CLIENT_ID')): ?>
            <!-- Google Sign-In Button -->
            <div class="google-signin-container">
                <div id="g_id_onload"
                     data-client_id="<?php echo GOOGLE_CLIENT_ID; ?>"
                     data-login_uri="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/google_callback.php'; ?>"
                     data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                     data-type="standard"
                     data-size="large"
                     data-theme="outline"
                     data-text="sign_in_with"
                     data-shape="rectangular"
                     data-logo_alignment="left"
                     data-width="350">
                </div>
            </div>

            <div class="divider">
                <span>OR</span>
            </div>
            <?php endif; ?>

            <form action="process_login.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="••••••••" required style="padding-right: 45px;">
                        <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 5px; color: #666; display: flex; align-items: center; justify-content: center;" title="Show password">
                            <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="forgot-password-link">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Sign In</button>
                </div>
            </form>

            <div class="signup-link">
                <p>Don't have an account? <a href="register.php">Register here!</a></p>
            </div>
        </div>
    </main>
    
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                // Change to eye-off icon (with slash)
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                this.title = 'Hide password';
            } else {
                passwordField.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                this.title = 'Show password';
            }
        });
    </script>
</body>
</html>