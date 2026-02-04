<?php
require_once 'config.php';
require_company();

// Get all postings
$postings_stmt = $conn->prepare("
    SELECT ip.*, 
    (SELECT COUNT(*) FROM applications WHERE posting_id = ip.posting_id) as total_applications,
    (SELECT COUNT(*) FROM applications WHERE posting_id = ip.posting_id AND status = 'pending') as pending_applications
    FROM internship_postings ip
    WHERE ip.company_id = ? 
    ORDER BY ip.created_at DESC
");
$postings_stmt->bind_param("i", $_SESSION['user_id']);
$postings_stmt->execute();
$postings = $postings_stmt->get_result();
$postings_stmt->close();

// Handle delete posting
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $delete_stmt = $conn->prepare("DELETE FROM internship_postings WHERE posting_id = ? AND company_id = ?");
    $delete_stmt->bind_param("ii", $delete_id, $_SESSION['user_id']);
    
    if ($delete_stmt->execute()) {
        $_SESSION['success'] = 'Posting deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete posting';
    }
    $delete_stmt->close();
    redirect('company_postings.php');
}

// Handle status toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    $toggle_stmt = $conn->prepare("UPDATE internship_postings SET status = IF(status='active', 'closed', 'active') WHERE posting_id = ? AND company_id = ?");
    $toggle_stmt->bind_param("ii", $toggle_id, $_SESSION['user_id']);
    
    if ($toggle_stmt->execute()) {
        $_SESSION['success'] = 'Posting status updated';
    } else {
        $_SESSION['error'] = 'Failed to update status';
    }
    $toggle_stmt->close();
    redirect('company_postings.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Postings - FirstStep</title>
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
                <a href="company_postings.php" style="color: var(--primary-color);">My Postings</a>
                <a href="company_applications.php">Applications</a>
                <a href="company_profile.php">Profile</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <div class="dashboard-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2>My Internship Postings</h2>
                        <p>Manage all your internship opportunities</p>
                    </div>
                    <a href="create_posting.php" class="btn-primary company-btn">+ Create New Posting</a>
                </div>
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

            <div class="dashboard-content">
                <h3>All Postings (<?php echo $postings->num_rows; ?>)</h3>
                
                <?php if ($postings->num_rows > 0): ?>
                    <div class="internship-list">
                        <?php while ($posting = $postings->fetch_assoc()): ?>
                            <div class="internship-card">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4><?php echo htmlspecialchars($posting['job_title']); ?></h4>
                                    </div>
                                    <?php
                                    $status_class = 'badge-success';
                                    if ($posting['status'] === 'draft') $status_class = 'badge-warning';
                                    if ($posting['status'] === 'closed') $status_class = 'badge-danger';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($posting['status']); ?></span>
                                </div>
                                
                                <div class="internship-details">
                                    <span class="detail-item">📍 <?php echo htmlspecialchars($posting['location']); ?></span>
                                    <span class="detail-item">⏱️ <?php echo htmlspecialchars($posting['duration']); ?></span>
                                    <span class="detail-item">💰 <?php echo htmlspecialchars($posting['stipend']); ?></span>
                                    <span class="detail-item">👥 <?php echo htmlspecialchars($posting['slots_available']); ?> slots</span>
                                    <span class="badge badge-primary"><?php echo htmlspecialchars($posting['internship_type']); ?></span>
                                </div>

                                <div class="internship-details" style="margin-top: 1rem;">
                                    <span class="detail-item">📝 Applications: <strong><?php echo $posting['total_applications']; ?></strong></span>
                                    <span class="detail-item">⏳ Pending: <strong><?php echo $posting['pending_applications']; ?></strong></span>
                                    <?php if ($posting['application_deadline']): ?>
                                        <span class="detail-item">📅 Deadline: <?php echo date('M d, Y', strtotime($posting['application_deadline'])); ?></span>
                                    <?php endif; ?>
                                </div>

                                <p style="margin-top: 1rem;"><?php echo nl2br(htmlspecialchars(substr($posting['job_description'], 0, 200))); ?>...</p>

                                <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="posting_applications.php?id=<?php echo $posting['posting_id']; ?>" class="btn-primary company-btn">View Applications (<?php echo $posting['total_applications']; ?>)</a>
                                    <a href="edit_posting.php?id=<?php echo $posting['posting_id']; ?>" class="btn-secondary">Edit</a>
                                    <a href="company_postings.php?toggle=<?php echo $posting['posting_id']; ?>" class="btn-secondary" onclick="return confirm('Toggle posting status?')">
                                        <?php echo $posting['status'] === 'active' ? 'Close' : 'Activate'; ?>
                                    </a>
                                    <a href="company_postings.php?delete=<?php echo $posting['posting_id']; ?>" class="btn-secondary" style="background: var(--danger-color); color: white;" onclick="return confirm('Are you sure you want to delete this posting? This will also delete all applications.')">Delete</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <p style="font-size: 1.2rem; color: var(--gray); margin-bottom: 1rem;">You haven't created any internship postings yet.</p>
                        <a href="create_posting.php" class="btn-primary company-btn">Create Your First Posting</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>