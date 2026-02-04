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
            <a href="index.php" class="back-button">← Back</a>
            <div class="logo-text">
                <h1>FirstStep</h1>
                <p>Internship Connection Platform</p>
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
            ?>

            <form action="process_company_registration.php" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <h3>Account Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Company Email *</label>
                            <input type="email" name="companyEmail" placeholder="company@email.com" required>
                            <span class="helper-text">Use your official company email address</span>
                        </div>
                        <div class="form-group">
                            <label>Contact Number *</label>
                            <input type="tel" name="contactNumber" placeholder="+63 912 345 6789" required>
                            <span class="helper-text">Include country code (e.g., +63 for Philippines)</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Password *</label>
                            <input type="password" name="password" placeholder="••••••••" required minlength="8">
                            <span class="helper-text">Minimum 8 characters required</span>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <input type="password" name="confirmPassword" placeholder="••••••••" required>
                            <span class="helper-text">Re-enter your password to confirm</span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Company Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Company Name *</label>
                            <input type="text" name="companyName" placeholder="Acme Corporation" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Person *</label>
                            <input type="text" name="contactPerson" placeholder="Maria Santos" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Industry Type *</label>
                            <select name="industryType" required>
                                <option value="">Select industry</option>
                                <option value="Technology">Technology</option>
                                <option value="Finance">Finance</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Education">Education</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Retail">Retail</option>
                                <option value="Hospitality">Hospitality</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Company Address *</label>
                        <input type="text" name="companyAddress" placeholder="123 Business St., Makati City, Metro Manila" required>
                    </div>
                    <div class="form-group">
                        <label>Company Description *</label>
                        <textarea name="companyDescription" placeholder="Tell us about your company, mission, and what you do..." required></textarea>
                        <span class="helper-text">Minimum 50 characters - Describe your company's mission and services</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Company Branding</h3>
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
                    <br>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="acceptTerms" id="acceptTerms" required>
                            <span>I have read and I agree to the <a href="terms.php" target="_blank" class="terms-link">Terms and Conditions</a> of FirstStep. *</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary company-btn" id="submitBtn" disabled>Create Company Account</button>
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
    const checkbox = document.getElementById('acceptTerms');
    const submitBtn = document.getElementById('submitBtn');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-disabled');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-disabled');
        }
    });
</script>