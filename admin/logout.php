<?php
session_start();

// Clear only admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);

// Redirect to admin login page
header('Location: login.php');
exit;
?> 