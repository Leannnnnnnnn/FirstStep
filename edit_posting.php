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
    } elseif (!empty($application_deadline) && strtotime($application_deadline) < strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = 'Application deadline cannot be in the past. Please select today or a future date.';

    } else {
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
                        <input type="text" name="jobTitle" value="<?php echo htmlspecialchars($posting['job_title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Job Description *</label>
                        <textarea name="jobDescription" rows="6" required><?php echo htmlspecialchars($posting['job_description']); ?></textarea>
                        <span class="helper-text">Provide a detailed description of the internship role</span>
                    </div>
                    <div class="form-group">
                        <label>Requirements *</label>
                        <textarea name="requirements" rows="6" required><?php echo htmlspecialchars($posting['requirements']); ?></textarea>
                        <span class="helper-text">Specify what skills and qualifications are needed</span>
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
                            <input type="text" name="location" value="<?php echo htmlspecialchars($posting['location']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Duration *</label>
                            <input type="text" name="duration" value="<?php echo htmlspecialchars($posting['duration']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Stipend</label>
                            <input type="text" name="stipend" value="<?php echo htmlspecialchars($posting['stipend']); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number of Slots *</label>
                            <input type="number" name="slotsAvailable" min="1" max="100" value="<?php echo $posting['slots_available']; ?>" required style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid var(--border-color); border-radius: 6px;">
                            <span class="helper-text">Maximum number of interns to accept (1-100)</span>
                        </div>
                        <div class="form-group">
                            <label>Application Deadline</label>
                            <input type="date" name="applicationDeadline" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $posting['application_deadline']; ?>" style="width: 100%; padding: 0.75rem; font-size: 1rem; border: 1px solid var(--border-color); border-radius: 6px;">
                            <span class="helper-text">Last date students can apply</span>
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