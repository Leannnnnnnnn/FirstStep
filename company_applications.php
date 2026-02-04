<?php
require_once 'config.php';
require_company();

// Get filter
$filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : 'all';

// Build query based on filter
$query = "
    SELECT a.*, ip.job_title, 
           s.first_name, s.middle_name, s.surname, s.email, s.school, s.course, s.year_level, s.skills, s.resume_path
    FROM applications a 
    JOIN internship_postings ip ON a.posting_id = ip.posting_id 
    JOIN students s ON a.student_id = s.student_id 
    WHERE a.company_id = ?";

if ($filter !== 'all') {
    $query .= " AND a.status = ?";
}

$query .= " ORDER BY a.applied_at DESC";

$stmt = $conn->prepare($query);

if ($filter !== 'all') {
    $stmt->bind_param("is", $_SESSION['user_id'], $filter);
} else {
    $stmt->bind_param("i", $_SESSION['user_id']);
}

$stmt->execute();
$applications = $stmt->get_result();
$stmt->close();

// Count by status
$pending = $reviewed = $shortlisted = $accepted = $rejected = 0;
$count_stmt = $conn->prepare("
    SELECT status, COUNT(*) as count 
    FROM applications 
    WHERE company_id = ? 
    GROUP BY status
");
$count_stmt->bind_param("i", $_SESSION['user_id']);
$count_stmt->execute();
$result = $count_stmt->get_result();
while ($row = $result->fetch_assoc()) {
    switch($row['status']) {
        case 'pending': $pending = $row['count']; break;
        case 'reviewed': $reviewed = $row['count']; break;
        case 'shortlisted': $shortlisted = $row['count']; break;
        case 'accepted': $accepted = $row['count']; break;
        case 'rejected': $rejected = $row['count']; break;
    }
}
$count_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - FirstStep</title>
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
                <a href="company_applications.php" style="color: var(--primary-color);">Applications</a>
                <a href="company_profile.php">Profile</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h2>All Applications</h2>
                <p>Review and manage student applications</p>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h4>Pending</h4>
                    <div class="stat-value"><?php echo $pending; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Reviewed</h4>
                    <div class="stat-value"><?php echo $reviewed; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Shortlisted</h4>
                    <div class="stat-value"><?php echo $shortlisted; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Accepted</h4>
                    <div class="stat-value"><?php echo $accepted; ?></div>
                </div>
            </div>

            <div class="dashboard-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <h3>Filter Applications</h3>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <a href="company_applications.php?status=all" class="btn-secondary" style="<?php if($filter=='all') echo 'background: var(--primary-color); color: white;'; ?>">All</a>
                        <a href="company_applications.php?status=pending" class="btn-secondary" style="<?php if($filter=='pending') echo 'background: var(--primary-color); color: white;'; ?>">Pending</a>
                        <a href="company_applications.php?status=reviewed" class="btn-secondary" style="<?php if($filter=='reviewed') echo 'background: var(--primary-color); color: white;'; ?>">Reviewed</a>
                        <a href="company_applications.php?status=shortlisted" class="btn-secondary" style="<?php if($filter=='shortlisted') echo 'background: var(--primary-color); color: white;'; ?>">Shortlisted</a>
                        <a href="company_applications.php?status=accepted" class="btn-secondary" style="<?php if($filter=='accepted') echo 'background: var(--primary-color); color: white;'; ?>">Accepted</a>
                        <a href="company_applications.php?status=rejected" class="btn-secondary" style="<?php if($filter=='rejected') echo 'background: var(--primary-color); color: white;'; ?>">Rejected</a>
                    </div>
                </div>

                <?php if ($applications->num_rows > 0): ?>
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
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4><?php echo htmlspecialchars($full_name); ?></h4>
                                        <p style="color: var(--gray); margin: 0.25rem 0;">✉️ <?php echo htmlspecialchars($application['email']); ?></p>
                                        <p class="company-name">📝 Applied for: <?php echo htmlspecialchars($application['job_title']); ?></p>
                                    </div>
                                    <?php
                                    $status_class = 'badge-warning';
                                    if ($application['status'] === 'accepted') $status_class = 'badge-success';
                                    if ($application['status'] === 'rejected') $status_class = 'badge-danger';
                                    if ($application['status'] === 'shortlisted') $status_class = 'badge-primary';
                                    if ($application['status'] === 'reviewed') $status_class = 'badge-primary';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($application['status']); ?></span>
                                </div>
                                
                                <div class="application-details">
                                    <span class="detail-item">🎓 <?php echo htmlspecialchars($application['school']); ?></span>
                                    <span class="detail-item">📚 <?php echo htmlspecialchars($application['course']); ?> - <?php echo htmlspecialchars($application['year_level']); ?></span>
                                    <span class="detail-item">📅 <?php echo date('M d, Y', strtotime($application['applied_at'])); ?></span>
                                </div>

                                <?php if ($application['skills']): ?>
                                    <div style="margin-top: 0.5rem;">
                                        <strong style="color: var(--gray);">Skills:</strong> <?php echo htmlspecialchars($application['skills']); ?>
                                    </div>
                                <?php endif; ?>

                                <div style="margin-top: 1rem;">
                                    <a href="view_application.php?id=<?php echo $application['application_id']; ?>" class="btn-primary company-btn">Review Application</a>
                                    <?php if ($application['resume_path']): ?>
                                        <a href="uploads/<?php echo htmlspecialchars($application['resume_path']); ?>" target="_blank" class="btn-secondary">View Resume</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <p style="font-size: 1.2rem; color: var(--gray);">No applications found with the selected filter.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>