<?php
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    $_SESSION['error'] = 'Unauthorized access';
    redirect('admin_login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize_input($_POST['action']);
    $posting_id = intval($_POST['posting_id']);
    $admin_id = $_SESSION['user_id'];

    if ($action === 'approve') {
        // Approve the post
        $stmt = $conn->prepare("UPDATE internship_postings SET approval_status = 'approved', reviewed_by = ?, reviewed_at = NOW() WHERE posting_id = ?");
        $stmt->bind_param("ii", $admin_id, $posting_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Post approved successfully! It is now visible to students.';
        } else {
            $_SESSION['error'] = 'Failed to approve post';
        }
        $stmt->close();
        
    } elseif ($action === 'reject') {
        // Reject the post with reason
        $rejection_reason = sanitize_input($_POST['rejection_reason']);
        
        if (empty($rejection_reason)) {
            $_SESSION['error'] = 'Please provide a reason for rejection';
            redirect('admin_dashboard.php');
        }
        
        $stmt = $conn->prepare("UPDATE internship_postings SET approval_status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), rejection_reason = ? WHERE posting_id = ?");
        $stmt->bind_param("isi", $admin_id, $rejection_reason, $posting_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Post rejected. Company has been notified with the reason.';
        } else {
            $_SESSION['error'] = 'Failed to reject post';
        }
        $stmt->close();
        
    } elseif ($action === 'delete') {
        // Permanently delete the post
        $stmt = $conn->prepare("DELETE FROM internship_postings WHERE posting_id = ?");
        $stmt->bind_param("i", $posting_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Post permanently deleted from the system.';
        } else {
            $_SESSION['error'] = 'Failed to delete post';
        }
        $stmt->close();
        
    } else {
        $_SESSION['error'] = 'Invalid action';
    }
    
    redirect('admin_dashboard.php');
} else {
    redirect('admin_dashboard.php');
}

$conn->close();
?>