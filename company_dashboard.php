<?php
require_once 'config.php';
require_company();

// Get company info
$stmt = $conn->prepare("SELECT * FROM companies WHERE company_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get company's postings
$postings_stmt = $conn->prepare("
    SELECT * FROM internship_postings 
    WHERE company_id = ? 
    ORDER BY created_at DESC
");
$postings_stmt->bind_param("i", $_SESSION['user_id']);
$postings_stmt->execute();
$postings = $postings_stmt->get_result();
$postings_stmt->close();

// Get applications for company
$applications_stmt = $conn->prepare("
    SELECT a.*, ip.job_title, s.first_name, s.middle_name, s.surname, s.email, s.school, s.course
    FROM applications a 
    JOIN internship_postings ip ON a.posting_id = ip.posting_id 
    JOIN students s ON a.student_id = s.student_id 
    WHERE a.company_id = ? 
    ORDER BY a.applied_at DESC 
    LIMIT 10
");
$applications_stmt->bind_param("i", $_SESSION['user_id']);
$applications_stmt->execute();
$applications = $applications_stmt->get_result();
$applications_stmt->close();

// Count stats
$stats_query = "SELECT COUNT(*) as total FROM internship_postings WHERE company_id = " . $_SESSION['user_id'];
$total_postings = $conn->query($stats_query)->fetch_assoc()['total'];

$active_query = "SELECT COUNT(*) as total FROM internship_postings WHERE company_id = " . $_SESSION['user_id'] . " AND status = 'active'";
$active_postings = $conn->query($active_query)->fetch_assoc()['total'];

$app_query = "SELECT COUNT(*) as total FROM applications WHERE company_id = " . $_SESSION['user_id'];
$total_applications = $conn->query($app_query)->fetch_assoc()['total'];

$pending_query = "SELECT COUNT(*) as total FROM applications WHERE company_id = " . $_SESSION['user_id'] . " AND status = 'pending'";
$pending_applications = $conn->query($pending_query)->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard - FirstStep</title>
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
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h2>Welcome, <?php echo htmlspecialchars($company['company_name']); ?>!</h2>
                <p>Manage your internship postings and applications</p>
                <?php if ($company['verification_status'] === 'pending'): ?>
                    <div class="alert alert-warning" style="margin-top: 1rem;">
                        ⚠️ Your account is pending verification. Some features may be limited.
                    </div>
                <?php endif; ?>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h4>Total Postings</h4>
                    <div class="stat-value"><?php echo $total_postings; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Active Postings</h4>
                    <div class="stat-value"><?php echo $active_postings; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Total Applications</h4>
                    <div class="stat-value"><?php echo $total_applications; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Pending Review</h4>
                    <div class="stat-value"><?php echo $pending_applications; ?></div>
                </div>
            </div>

            <div class="dashboard-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>Your Internship Postings</h3>
                    <a href="create_posting.php" class="btn-primary company-btn">+ Create New Posting</a>
                </div>
                <div class="internship-list">
                    <?php if ($postings->num_rows > 0): ?>
                        <?php while ($posting = $postings->fetch_assoc()): ?>
                            <div class="internship-card">
                                <h4><?php echo htmlspecialchars($posting['job_title']); ?></h4>
                                <div class="internship-details">
                                    <span class="detail-item">📍 <?php echo htmlspecialchars($posting['location']); ?></span>
                                    <span class="detail-item">⏱️ <?php echo htmlspecialchars($posting['duration']); ?></span>
                                    <span class="detail-item">👥 <?php echo htmlspecialchars($posting['slots_available']); ?> slots</span>
                                    <?php
                                    $status_class = $posting['status'] === 'active' ? 'badge-success' : 'badge-warning';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($posting['status']); ?></span>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars(substr($posting['job_description'], 0, 200))); ?>...</p>
                                <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                                    <a href="edit_posting.php?id=<?php echo $posting['posting_id']; ?>" class="btn-secondary">Edit</a>
                                    <a href="posting_applications.php?id=<?php echo $posting['posting_id']; ?>" class="btn-primary">View Applications</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>You haven't created any internship postings yet. <a href="create_posting.php">Create your first posting</a></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($applications->num_rows > 0): ?>
            <div class="dashboard-content" style="margin-top: 2rem;">
                <h3>Recent Applications</h3>
                <div class="application-list">
                    <?php while ($application = $applications->fetch_assoc()): 
                        // Build full name
                        $full_name = $application['first_name'];
                        if (!empty($application['middle_name'])) {
                            $full_name .= ' ' . $application['middle_name'];
                        }
                        $full_name .= ' ' . $application['surname'];
                    ?>
                        <div class="application-card">
                            <h4><?php echo htmlspecialchars($full_name); ?></h4>
                            <p class="company-name">📝 Applied for: <?php echo htmlspecialchars($application['job_title']); ?></p>
                            <div class="application-details">
                                <span class="detail-item">🎓 <?php echo htmlspecialchars($application['school']); ?></span>
                                <span class="detail-item">📚 <?php echo htmlspecialchars($application['course']); ?></span>
                                <span class="detail-item">📅 <?php echo date('M d, Y', strtotime($application['applied_at'])); ?></span>
                                <?php
                                $status_class = 'badge-warning';
                                if ($application['status'] === 'accepted') $status_class = 'badge-success';
                                if ($application['status'] === 'rejected') $status_class = 'badge-danger';
                                if ($application['status'] === 'shortlisted') $status_class = 'badge-primary';
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($application['status']); ?></span>
                            </div>
                            <div style="margin-top: 1rem;">
                                <a href="view_application.php?id=<?php echo $application['application_id']; ?>" class="btn-primary company-btn">Review Application</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>