<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join FirstStep - Internship Connection Platform</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <div class="logo-text" style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="uploads/logos/FirstStep_Logo.png" alt="FirstStep Logo" style="height: 45px; width: auto; object-fit: contain;">
                <div>
                    <h1 style="margin: 0; font-size: 1.5rem;">FirstStep</h1>
                    <p style="margin: 0; font-size: 0.75rem; color: #6b7280;">Internship Connection Platform</p>
                </div>
            </div>
        </div>
</header>

    <main>
        <div class="container join-container">
            <div class="header-section">
                <h2>Join FirstStep</h2>
                <p>Choose your account type to get started</p>
            </div>

            <div class="account-types">
                <div class="account-card">
                    <h3>I'm a Student</h3>
                    <p class="card-description">Looking for internship opportunities to gain experience and develop my skills</p>
                    <ul class="card-features">
                        <li>✓ Browse internship opportunities</li>
                        <li>✓ Apply to multiple companies</li>
                        <li>✓ Build your professional profile</li>
                    </ul>
                    <a href="register_student.php" class="btn-primary">Register as Student →</a>
                </div>

                <div class="account-card">
                    <h3>I'm a Company</h3>
                    <p class="card-description">Looking to hire talented interns and provide valuable work experience</p>
                    <ul class="card-features">
                        <li>✓ Post internship opportunities</li>
                        <li>✓ Review qualified candidates</li>
                        <li>✓ Manage your hiring process</li>
                    </ul>
                    <a href="register_company.php" class="btn-primary company-btn">Register as Company →</a>
                </div>
            </div>

            <div class="signin-link">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </main>
</body>
</html>