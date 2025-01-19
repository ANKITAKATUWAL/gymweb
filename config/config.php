<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gym_management');

// Site configuration
define('SITE_URL', 'http://localhost/gymweb');
define('SITE_NAME', 'Gym Management System');

// Khalti configuration
define('KHALTI_PUBLIC_KEY', 'test_public_key_dc74e0fd57cb46cd93832aee0a390234');
define('KHALTI_SECRET_KEY', 'test_secret_key_0c2de695f73b4252b5dacf72a73c3692');
define('KHALTI_API_URL', 'https://a.khalti.com/api/v2/epayment/');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);