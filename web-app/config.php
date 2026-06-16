<?php

session_start();

// Database configuration
define('DB_HOST', 'mysql.railway.internal');
define('DB_USER', 'root'); // Change this to your database username
define('DB_PASS', 'nxBAvidZaLlfLSBMvdPxCWUYwwjkEiIk'); // Change this to your database password
define('DB_NAME', 'railway');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Site URL
define('SITE_URL', 'mini-project-production-80d2.up.railway.app/');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>
