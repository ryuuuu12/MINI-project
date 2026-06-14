<?php

session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u155978661_drowsiness'); // Change this to your database username
define('DB_PASS', '*6UGNd4#q'); // Change this to your database password
define('DB_NAME', 'u155978661_drowsiness');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Site URL
define('SITE_URL', 'https://mini-project-two-navy.vercel.app/');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>
