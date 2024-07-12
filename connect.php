<?php
require('config.php');
session_start(); // Start the session for storing user login status

// Retrieve form data
$membership_no = $_POST['member'];
$password = $_POST['password'];

// Database connection

if($conn->connect_error){
    die("Connection Failed : ". $conn->connect_error);
} else {
    $sql = "select * from members where membership_no = '$membership_no' and password = '$password'";
    $result = $conn->query($sql);
    
    // Check if any results were returned
    if ($result && $result->num_rows > 0) {        
        $memberData = $result->fetch_assoc();
        $_SESSION['member_id'] = $memberData['id'];
        $_SESSION['membership_no'] = $membership_no;

        header("Location: dashboard.php");
    } else {
        echo "Login failed. Please check your membership number and password.";
    }    

    $conn->close();
}
?>
