<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - FirstStep</title>
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
                <h2>Student Registration</h2>
                <p>Create your student account to find internship opportunities</p>
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

            <form action="process_student_registration.php" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <h3>Account Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="firstName" placeholder="Juan" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middleName" placeholder="Santos">
                            <span class="helper-text">Optional</span>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="surname" placeholder="Dela Cruz" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" placeholder="juan@email.com" required>
                            <span class="helper-text">Use a valid email format (e.g., name@example.com)</span>
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
                    <h3>Educational Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>School / University *</label>
                            <input type="text" name="school" placeholder="University of the Philippines" required>
                        </div>
                        <div class="form-group">
                            <label>Course / Program *</label>
                            <input type="text" name="course" placeholder="BS Computer Science" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Year Level *</label>
                            <select name="yearLevel" required>
                                <option value="">Select your level</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                                <option value="5th Year">5th Year</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Location</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" placeholder="Quezon City" required>
                        </div>
                        <div class="form-group">
                            <label>Barangay *</label>
                            <input type="text" name="barangay" placeholder="Commonwealth" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Internship Preferences</h3>
                    <div class="form-group">
                        <label>Preferred Internship Types * (Select all that apply)</label>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="On-site">
                                <span>On-site</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="Remote">
                                <span>Remote</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="internshipTypes[]" value="Hybrid">
                                <span>Hybrid</span>
                            </label>
                        </div>
                        <span class="helper-text">Select at least one internship type</span>
                    </div>
                    <div class="form-group">
                        <label>Skills</label>
                        <input type="text" name="skills" placeholder="e.g., JavaScript, Python, Communication">
                        <span class="helper-text">Enter your skills separated by commas</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Documents</h3>
                    <div class="form-group">
                        <label>Upload Resume (PDF) *</label>
                        <input type="file" name="resume" accept=".pdf" required class="file-input">
                        <span class="helper-text">Supported format: PDF | Maximum file size: 5MB</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Create Student Account</button>
                </div>
            </form>

            <div class="signin-link">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </main>
</body>
</html>