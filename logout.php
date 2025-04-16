<?php
session_start();

// Check if it's an admin logout
if (isset($_SESSION['admin_id'])) {
    // Clear only admin session variables
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    header('Location: admin/login.php');
    exit;
}

// If not admin, clear user session variables
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['created_at']);

// Redirect to login page
header('Location: login.php');
exit;
?> 