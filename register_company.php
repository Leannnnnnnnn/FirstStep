<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration - FirstStep</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="landing.php" class="back-button">← Back</a>
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
        <div class="container registration-form">
            <div class="header-section">
                <h2>Company Registration</h2>
                <p>Create your company account to post internship opportunities</p>
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

            // Check if form data exists (meaning validation failed)
            $hasFormData = isset($_SESSION['form_data']);

            // Preserve form data
            $companyEmail = isset($_SESSION['form_data']['companyEmail']) ? htmlspecialchars($_SESSION['form_data']['companyEmail']) : '';
            $contactNumber = isset($_SESSION['form_data']['contactNumber']) ? htmlspecialchars($_SESSION['form_data']['contactNumber']) : '';
            $companyName = isset($_SESSION['form_data']['companyName']) ? htmlspecialchars($_SESSION['form_data']['companyName']) : '';
            $contactPerson = isset($_SESSION['form_data']['contactPerson']) ? htmlspecialchars($_SESSION['form_data']['contactPerson']) : '';
            $industryType = isset($_SESSION['form_data']['industryType']) ? htmlspecialchars($_SESSION['form_data']['industryType']) : '';
            $companyAddress = isset($_SESSION['form_data']['companyAddress']) ? htmlspecialchars($_SESSION['form_data']['companyAddress']) : '';
            $companyDescription = isset($_SESSION['form_data']['companyDescription']) ? htmlspecialchars($_SESSION['form_data']['companyDescription']) : '';

            // Clear form data after retrieving
            unset($_SESSION['form_data']);
            ?>

            <form action="process_company_registration.php" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <h3>Account Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Company Email *</label>
                            <input type="email" name="companyEmail" placeholder="company@email.com" value="<?php echo $companyEmail; ?>" required>
                            <span class="helper-text">Use your official company email address</span>
                        </div>
                        <div class="form-group">
                            <label>Contact Number *</label>
                            <input type="tel" name="contactNumber" placeholder="+63 912 345 6789" value="<?php echo $contactNumber; ?>" required>
                            <span class="helper-text">Include country code (e.g., +63 for Philippines)</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Password *</label>
                            <div style="position: relative;">
                                <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?&quot;:{}|<>]).{8,}$" title="Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character" style="padding-right: 45px;">
                                <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 5px; color: #666; display: flex; align-items: center; justify-content: center;" title="Show password">
                                    <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                            <span class="helper-text">Must be at least 8 characters and include: uppercase, lowercase, number, and special character (!@#$%^&*etc.)</span>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <div style="position: relative;">
                                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required style="padding-right: 45px;">
                                <button type="button" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 5px; color: #666; display: flex; align-items: center; justify-content: center;" title="Show password">
                                    <svg id="eyeIconConfirm" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                            <span class="helper-text">Re-enter your password to confirm</span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Company Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Company Name *</label>
                            <input type="text" name="companyName" placeholder="Acme Corporation" value="<?php echo $companyName; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Person *</label>
                            <input type="text" name="contactPerson" placeholder="Maria Santos" value="<?php echo $contactPerson; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Industry Type *</label>
                            <select name="industryType" required>
                                <option value="">Select industry</option>
                                <option value="Technology" <?php if($industryType == 'Technology') echo 'selected'; ?>>Technology</option>
                                <option value="Finance" <?php if($industryType == 'Finance') echo 'selected'; ?>>Finance</option>
                                <option value="Healthcare" <?php if($industryType == 'Healthcare') echo 'selected'; ?>>Healthcare</option>
                                <option value="Education" <?php if($industryType == 'Education') echo 'selected'; ?>>Education</option>
                                <option value="Marketing" <?php if($industryType == 'Marketing') echo 'selected'; ?>>Marketing</option>
                                <option value="Manufacturing" <?php if($industryType == 'Manufacturing') echo 'selected'; ?>>Manufacturing</option>
                                <option value="Retail" <?php if($industryType == 'Retail') echo 'selected'; ?>>Retail</option>
                                <option value="Hospitality" <?php if($industryType == 'Hospitality') echo 'selected'; ?>>Hospitality</option>
                                <option value="Other" <?php if($industryType == 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Company Address *</label>
                        <input type="text" name="companyAddress" placeholder="123 Business St., Makati City, Metro Manila" value="<?php echo $companyAddress; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Company Description *</label>
                        <textarea name="companyDescription" placeholder="Tell us about your company, mission, and what you do..." required><?php echo $companyDescription; ?></textarea>
                        <span class="helper-text">Minimum 50 characters - Describe your company's mission and services</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Company Branding</h3>
                    <?php if ($hasFormData): ?>
                        <div class="alert alert-warning" style="margin-bottom: 1rem;">
                            ⚠️ Please re-upload your company logo (file inputs cannot be pre-filled for security reasons)
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Upload Company Logo *</label>
                        <input type="file" name="companyLogo" accept=".png,.jpg,.jpeg,.svg" required class="file-input">
                        <span class="helper-text">Supported formats: PNG, JPG, SVG | Maximum file size: 2MB</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Terms and Conditions</h3>
                    <div class="terms-box">
                        <p>By creating an account, you agree to our <a href="terms.php" target="_blank" class="terms-link">Terms and Conditions</a>. Please read them carefully before proceeding.</p>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="acceptTerms" id="acceptTerms" required>
                            <span>I have read and I agree to the <a href="terms.php" target="_blank" class="terms-link">Terms and Conditions</a> of FirstStep. *</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary company-btn">Create Company Account</button>
                </div>
            </form>

            <div class="signin-link">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </main>
</body>
</html>

<script>
    // Password visibility toggle for main password field
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

    // Password visibility toggle for confirm password field
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPasswordField = document.getElementById('confirmPassword');
        const eyeIconConfirm = document.getElementById('eyeIconConfirm');
        
        if (confirmPasswordField.type === 'password') {
            confirmPasswordField.type = 'text';
            // Change to eye-off icon (with slash)
            eyeIconConfirm.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            this.title = 'Hide password';
        } else {
            confirmPasswordField.type = 'password';
            // Change back to eye icon
            eyeIconConfirm.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            this.title = 'Show password';
        }
    });
</script>