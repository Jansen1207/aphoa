<?php
// Database connection settings for XAMPP localhost
$db_host = 'localhost';
$db_username = 'root';       // default XAMPP user
$db_password = '';           // default XAMPP password is empty
$db_name = 'aphoadb';        // your database name

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set timezone
date_default_timezone_set('Asia/Manila');
?>
