<?php
require_once __DIR__ . '/../config/config.php';

$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?> 