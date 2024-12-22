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
define('KHALTI_PUBLIC_KEY', 'live_secret_key_35439e1fde6143acb59006d9ca3622e9');
define('KHALTI_SECRET_KEY', 'live_secret_key_68791341fdd94846a146f0457ff7b455');
define('KHALTI_API_URL', 'https://khalti.com/api/v2/epayment/');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?> 