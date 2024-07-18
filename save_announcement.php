<?php
require('config.php');
session_start();

// Retrieve form data
$title = $_POST['title'];
$message = $_POST['message'];
$created_at = date('Y-m-d H:i:s'); // Current timestamp
$member_id = $_SESSION['member_id'];

/*
$_SESSION['member_id'] = $memberData['id'];
$_SESSION['membership_no'] = $membership_no;
$_SESSION['group'] = $memberData['group']; // Assuming 'group' is a column in your 'members' table
*/


// SQL query to insert data into announcements table
$sql = "INSERT INTO announcements (title, message, created_at, member_id) VALUES ('$title', '$message', '$created_at', '$member_id')";

if ($conn->query($sql) === TRUE) {
    echo "New announcement created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection (optional since it's handled by config.php)
//$conn->close();
?>
