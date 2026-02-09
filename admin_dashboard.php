<?php
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    $_SESSION['error'] = 'Please login as admin to access this page';
    redirect('admin_login.php');
}

$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

// Get statistics
$total_posts = $conn->query("SELECT COUNT(*) as total FROM internship_postings")->fetch_assoc()['total'];
$pending_posts = $conn->query("SELECT COUNT(*) as total FROM internship_postings WHERE approval_status = 'pending'")->fetch_assoc()['total'];
$approved_posts = $conn->query("SELECT COUNT(*) as total FROM internship_postings WHERE approval_status = 'approved'")->fetch_assoc()['total'];
$rejected_posts = $conn->query("SELECT COUNT(*) as total FROM internship_postings WHERE approval_status = 'rejected'")->fetch_assoc()['total'];
$total_students = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$total_companies = $conn->query("SELECT COUNT(*) as total FROM companies")->fetch_assoc()['total'];
$total_applications = $conn->query("SELECT COUNT(*) as total FROM applications")->fetch_assoc()['total'];

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending';

// Build query based on filter
$where_clause = "WHERE ip.approval_status = 'pending'";
if ($filter === 'all') {
    $where_clause = "";
} elseif ($filter === 'approved') {
    $where_clause = "WHERE ip.approval_status = 'approved'";
} elseif ($filter === 'rejected') {
    $where_clause = "WHERE ip.approval_status = 'rejected'";
}

// Get posts
$posts_query = "
    SELECT ip.*, c.company_name, c.industry_type, c.company_email
    FROM internship_postings ip
    JOIN companies c ON ip.company_id = c.company_id
    $where_clause
    ORDER BY ip.created_at DESC
";
$posts_result = $conn->query($posts_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FirstStep</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <style>
        .dashboard-container { max-width: 1600px !important; width: 95% !important; margin: 0 auto !important; }
        main { padding: 1rem 0.5rem !important; }
        .admin-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; }
        .filter-tabs { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .filter-tab { padding: 0.75rem 1.5rem; border: 2px solid var(--border-color); border-radius: 6px; background: white; cursor: pointer; text-decoration: none; color: var(--dark); font-weight: 600; transition: all 0.3s; }
        .filter-tab:hover { border-color: var(--primary-color); background: var(--light-gray); }
        .filter-tab.active { background: var(--primary-color); color: white; border-color: var(--primary-color); }
        .post-card { background: white; border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; }
        .post-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; }
        .post-meta { display: flex; gap: 1rem; flex-wrap: wrap; margin: 1rem 0; font-size: 0.9rem; color: var(--gray); }
        .post-actions { display: flex; gap: 1rem; margin-top: 1rem; }
        .btn-approve { background: var(--success-color); color: white; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-reject { background: var(--danger-color); color: white; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-view { background: var(--primary-color); color: white; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 600; }
        .btn-delete { background: #dc2626; color: white; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .status-badge { padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <div class="logo-text">
                <h1>FirstStep Admin</h1>
                <p>Administration Portal</p>
            </div>
            <nav class="nav-menu">
                <a href="admin_dashboard.php">Dashboard</a>
                <span style="color: var(--gray);">|</span>
                <span style="color: var(--dark); font-weight: 600;"><?php echo htmlspecialchars($admin_name); ?></span>
                <a href="admin_logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <div class="admin-header">
                <h2>👨‍💼 Welcome, <?php echo htmlspecialchars($admin_name); ?>!</h2>
                <p>Manage internship postings and moderate content</p>
            </div>

            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Pending Approval</h3>
                    <p class="stat-number" style="color: #f59e0b;"><?php echo $pending_posts; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Approved Posts</h3>
                    <p class="stat-number" style="color: #10b981;"><?php echo $approved_posts; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <p class="stat-number"><?php echo $total_students; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Companies</h3>
                    <p class="stat-number"><?php echo $total_companies; ?></p>
                </div>
            </div>

            <div class="filter-tabs">
                <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                    ⏳ Pending (<?php echo $pending_posts; ?>)
                </a>
                <a href="?filter=approved" class="filter-tab <?php echo $filter === 'approved' ? 'active' : ''; ?>">
                    ✅ Approved (<?php echo $approved_posts; ?>)
                </a>
                <a href="?filter=rejected" class="filter-tab <?php echo $filter === 'rejected' ? 'active' : ''; ?>">
                    ❌ Rejected (<?php echo $rejected_posts; ?>)
                </a>
                <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                    📋 All Posts (<?php echo $total_posts; ?>)
                </a>
            </div>

            <div class="dashboard-content">
                <h3>Internship Postings 
                    <?php if ($posts_result->num_rows > 0): ?>
                        <span style="color: var(--gray); font-size: 0.9rem; font-weight: normal;">(<?php echo $posts_result->num_rows; ?> results)</span>
                    <?php endif; ?>
                </h3>

                <?php if ($posts_result->num_rows > 0): ?>
                    <?php while ($post = $posts_result->fetch_assoc()): ?>
                        <div class="post-card">
                            <div class="post-header">
                                <div>
                                    <h4 style="margin: 0 0 0.5rem 0; font-size: 1.3rem;"><?php echo htmlspecialchars($post['job_title']); ?></h4>
                                    <p style="margin: 0; color: var(--gray);">
                                        🏢 <?php echo htmlspecialchars($post['company_name']); ?>
                                        <?php if ($post['industry_type']): ?>
                                            • <?php echo htmlspecialchars($post['industry_type']); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="status-badge status-<?php echo $post['approval_status']; ?>">
                                    <?php echo ucfirst($post['approval_status']); ?>
                                </span>
                            </div>

                            <div class="post-meta">
                                <span>📍 <?php echo htmlspecialchars($post['location']); ?></span>
                                <span>⏱️ <?php echo htmlspecialchars($post['duration']); ?></span>
                                <span>💰 <?php echo htmlspecialchars($post['stipend']); ?></span>
                                <span>👥 <?php echo $post['slots_available']; ?> slots</span>
                                <span>📅 Posted: <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                            </div>

                            <div style="margin: 1rem 0;">
                                <strong>Description:</strong>
                                <p style="margin: 0.5rem 0; color: var(--dark);">
                                    <?php echo nl2br(htmlspecialchars(substr($post['job_description'], 0, 300))); ?>
                                    <?php if (strlen($post['job_description']) > 300): ?>...<?php endif; ?>
                                </p>
                            </div>

                            <?php if ($post['approval_status'] === 'rejected' && $post['rejection_reason']): ?>
                                <div style="background: #fee2e2; padding: 1rem; border-radius: 6px; border-left: 4px solid #dc2626; margin: 1rem 0;">
                                    <strong style="color: #991b1b;">Rejection Reason:</strong>
                                    <p style="margin: 0.5rem 0 0 0; color: #7f1d1d;"><?php echo htmlspecialchars($post['rejection_reason']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="post-actions">
                                <?php if ($post['approval_status'] === 'pending'): ?>
                                    <button onclick="approvePost(<?php echo $post['posting_id']; ?>)" class="btn-approve">
                                        ✅ Approve
                                    </button>
                                    <button onclick="showRejectModal(<?php echo $post['posting_id']; ?>)" class="btn-reject">
                                        ❌ Reject
                                    </button>
                                <?php endif; ?>
                                <a href="admin_view_post.php?id=<?php echo $post['posting_id']; ?>" class="btn-view">
                                    👁️ View Full Details
                                </a>
                                <button onclick="deletePost(<?php echo $post['posting_id']; ?>)" class="btn-delete">
                                    🗑️ Delete
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; background: var(--light-gray); border-radius: 10px;">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">📭</p>
                        <p style="font-size: 1.2rem; color: var(--dark);">No posts found</p>
                        <p style="color: var(--gray);">There are no <?php echo $filter; ?> posts at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Rejection Modal -->
    <div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 500px; width: 90%;">
            <h3 style="margin-top: 0;">Reject Post</h3>
            <form id="rejectForm" action="admin_process_post.php" method="POST">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="posting_id" id="reject_posting_id">
                <div class="form-group">
                    <label>Reason for Rejection *</label>
                    <textarea name="rejection_reason" rows="4" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;"></textarea>
                    <span class="helper-text">Explain why this post is being rejected</span>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn-reject" style="flex: 1;">Reject Post</button>
                    <button type="button" onclick="closeRejectModal()" style="flex: 1; padding: 0.6rem 1.2rem; background: var(--light-gray); color: var(--dark); border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function approvePost(postingId) {
            if (confirm('Are you sure you want to approve this post? It will be visible to all students.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin_process_post.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'approve';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'posting_id';
                idInput.value = postingId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showRejectModal(postingId) {
            document.getElementById('reject_posting_id').value = postingId;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        function deletePost(postingId) {
            if (confirm('⚠️ WARNING: Are you sure you want to PERMANENTLY DELETE this post? This action cannot be undone!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin_process_post.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'posting_id';
                idInput.value = postingId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>