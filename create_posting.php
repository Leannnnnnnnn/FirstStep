<?php
require_once 'config.php';
require_company();

// Handle posting creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_title = sanitize_input($_POST['jobTitle']);
    $job_description = sanitize_input($_POST['jobDescription']);
    $requirements = sanitize_input($_POST['requirements']);
    $internship_type = sanitize_input($_POST['internshipType']);
    $location = sanitize_input($_POST['location']);
    $duration = sanitize_input($_POST['duration']);
    $stipend = sanitize_input($_POST['stipend']);
    $slots_available = intval($_POST['slotsAvailable']);
    $application_deadline = sanitize_input($_POST['applicationDeadline']);
    $status = sanitize_input($_POST['status']);

    if (empty($job_title) || empty($job_description) || empty($requirements)) {
        $_SESSION['error'] = 'Please fill in all required fields';
    } elseif (!empty($application_deadline) && strtotime($application_deadline) < strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = 'Application deadline cannot be in the past. Please select today or a future date.';

    } else {
        $stmt = $conn->prepare("INSERT INTO internship_postings (company_id, job_title, job_description, requirements, internship_type, location, duration, stipend, slots_available, application_deadline, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("isssssssiss", $_SESSION['user_id'], $job_title, $job_description, $requirements, $internship_type, $location, $duration, $stipend, $slots_available, $application_deadline, $status);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Internship posting created successfully!';
            redirect('company_postings.php');
        } else {
            $_SESSION['error'] = 'Failed to create posting: ' . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Posting - FirstStep</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <div class="logo-text">
                <h1>FirstStep</h1>
                <p>Internship Connection Platform</p>
            </div>
            <nav class="nav-menu">
                <a href="company_dashboard.php">Dashboard</a>
                <a href="company_postings.php">My Postings</a>
                <a href="company_applications.php">Applications</a>
                <a href="company_profile.php">Profile</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container registration-form">
            <div class="header-section">
                <h2>Create Internship Posting</h2>
                <p>Post a new internship opportunity</p>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <form action="create_posting.php" method="POST">
                <div class="form-section">
                    <h3>Job Information</h3>
                    <div class="form-group">
                        <label>Job Title *</label>
                        <input type="text" name="jobTitle" placeholder="e.g., Web Development Intern" required>
                    </div>
                    <div class="form-group">
                        <label>Job Description *</label>
                        <textarea name="jobDescription" rows="6" placeholder="Describe the role, responsibilities, and what the intern will learn..." required></textarea>
                        <span class="helper-text">Provide a detailed description of the internship role</span>
                    </div>
                    <div class="form-group">
                        <label>Requirements *</label>
                        <textarea name="requirements" rows="6" placeholder="List the required skills, qualifications, and experience..." required></textarea>
                        <span class="helper-text">Specify what skills and qualifications are needed</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Internship Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Internship Type *</label>
                            <select name="internshipType" required>
                                <option value="">Select type</option>
                                <option value="On-site">On-site</option>
                                <option value="Remote">Remote</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Location *</label>
                            <input type="text" name="location" placeholder="e.g., Makati City or Remote Work" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Duration *</label>
                            <input type="text" name="duration" placeholder="e.g., 3-6 months" required>
                        </div>
                        <div class="form-group">
                            <label>Allowance</label>
                            <input type="text" name="stipend" placeholder="e.g., 5,000 - 8,000 per month">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number of Slots *</label>
                            <input type="number" name="slotsAvailable" min="1" max="100" value="1" required style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid var(--border-color); border-radius: 6px;">
                            <span class="helper-text">Maximum number of interns to accept (1-100)</span>
                        </div>
                        <div class="form-group">
                            <label>Application Deadline</label>
                            <input type="date" name="applicationDeadline" min="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid var(--border-color); border-radius: 6px;">
                            <span class="helper-text">Last date students can apply</span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Posting Status</h3>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active">Active - Visible to students</option>
                            <option value="draft">Draft - Save for later</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="company_postings.php" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary company-btn">Create Posting</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>