<?php
session_start();
require_once '../config/config.php';

// Clear all session data
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: " . SITE_URL . "/auth/login.php");
exit();
  </rewritten_file> 