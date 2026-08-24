<?php
/**
 * PENNEN Admin — Secure Login Interface
 */
require_once __DIR__ . '/includes/auth.php';
startAdminSession();

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}else{
    header('Location: login.php');
}
?>