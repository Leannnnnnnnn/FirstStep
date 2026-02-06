<?php
require_once 'config.php';
require_company();

$posting_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get posting details
$stmt = $conn->prepare("SELECT * FROM internship_postings WHERE posting_id = ? AND company_id = ?");
$stmt->bind_param("ii", $posting_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Posting not found';
    redirect('company_postings.php');
}

$posting = $result->fetch_assoc();
$stmt->close();

// Handle update
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
    } 
    // ===== ADDED: LENGTH VALIDATION =====
    elseif (strlen($job_title) > 255) {
        $_SESSION['error'] = 'Job title must not exceed 255 characters';
    } elseif (strlen($job_description) < 50) {
        $_SESSION['error'] = 'Job description must be at least 50 characters';
    } elseif (strlen($job_description) > 10000) {
        $_SESSION['error'] = 'Job description must not exceed 10,000 characters';
    } elseif (strlen($requirements) < 20) {
        $_SESSION['error'] = 'Requirements must be at least 20 characters';
    } elseif (strlen($requirements) > 10000) {
        $_SESSION['error'] = 'Requirements must not exceed 10,000 characters';
    } elseif (strlen($location) > 255) {
        $_SESSION['error'] = 'Location must not exceed 255 characters';
    } elseif (strlen($duration) > 100) {
        $_SESSION['error'] = 'Duration must not exceed 100 characters';
    } elseif (!empty($stipend) && strlen($stipend) > 100) {
        $_SESSION['error'] = 'Stipend must not exceed 100 characters';
    } elseif ($slots_available < 1 || $slots_available > 100) {
        $_SESSION['error'] = 'Slots available must be between 1 and 100';
    } 
    // ===== END LENGTH VALIDATION =====
    else {
        $update_stmt = $conn->prepare("UPDATE internship_postings SET job_title=?, job_description=?, requirements=?, internship_type=?, location=?, duration=?, stipend=?, slots_available=?, application_deadline=?, status=? WHERE posting_id=? AND company_id=?");
        
        $update_stmt->bind_param("sssssssissii", $job_title, $job_description, $requirements, $internship_type, $location, $duration, $stipend, $slots_available, $application_deadline, $status, $posting_id, $_SESSION['user_id']);
        
        if ($update_stmt->execute()) {
            $_SESSION['success'] = 'Posting updated successfully!';
            redirect('company_postings.php');
        } else {
            $_SESSION['error'] = 'Failed to update posting';
        }
        $update_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Posting - FirstStep</title>
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
                <h2>Edit Internship Posting</h2>
                <p>Update your internship opportunity details</p>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <form action="edit_posting.php?id=<?php echo $posting_id; ?>" method="POST">
                <div class="form-section">
                    <h3>Job Information</h3>
                    <div class="form-group">
                        <label>Job Title *</label>
                        <input type="text" name="jobTitle" value="<?php echo htmlspecialchars($posting['job_title']); ?>" required maxlength="255">
                        <span class="helper-text">Maximum 255 characters</span>
                    </div>
                    <div class="form-group">
                        <label>Job Description *</label>
                        <textarea name="jobDescription" rows="6" required minlength="50" maxlength="10000"><?php echo htmlspecialchars($posting['job_description']); ?></textarea>
                        <span class="helper-text">Minimum 50 characters, maximum 10,000 characters. Provide a detailed description of the internship role</span>
                    </div>
                    <div class="form-group">
                        <label>Requirements *</label>
                        <textarea name="requirements" rows="6" required minlength="20" maxlength="10000"><?php echo htmlspecialchars($posting['requirements']); ?></textarea>
                        <span class="helper-text">Minimum 20 characters, maximum 10,000 characters. Specify what skills and qualifications are needed</span>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Internship Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Internship Type *</label>
                            <select name="internshipType" required>
                                <option value="On-site" <?php if($posting['internship_type']=='On-site') echo 'selected'; ?>>On-site</option>
                                <option value="Remote" <?php if($posting['internship_type']=='Remote') echo 'selected'; ?>>Remote</option>
                                <option value="Hybrid" <?php if($posting['internship_type']=='Hybrid') echo 'selected'; ?>>Hybrid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Location *</label>
                            <input type="text" name="location" value="<?php echo htmlspecialchars($posting['location']); ?>" required maxlength="255">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Duration *</label>
                            <input type="text" name="duration" value="<?php echo htmlspecialchars($posting['duration']); ?>" required maxlength="100">
                        </div>
                        <div class="form-group">
                            <label>Stipend</label>
                            <input type="text" name="stipend" value="<?php echo htmlspecialchars($posting['stipend']); ?>" maxlength="100">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number of Slots *</label>
                            <input type="number" name="slotsAvailable" min="1" max="100" value="<?php echo $posting['slots_available']; ?>" required>
                            <span class="helper-text">Enter a number between 1 and 100</span>
                        </div>
                        <div class="form-group">
                            <label>Application Deadline</label>
                            <input type="date" name="applicationDeadline" value="<?php echo $posting['application_deadline']; ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Posting Status</h3>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active" <?php if($posting['status']=='active') echo 'selected'; ?>>Active - Visible to students</option>
                            <option value="draft" <?php if($posting['status']=='draft') echo 'selected'; ?>>Draft - Save for later</option>
                            <option value="closed" <?php if($posting['status']=='closed') echo 'selected'; ?>>Closed - No longer accepting applications</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="company_postings.php" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary company-btn">Update Posting</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>