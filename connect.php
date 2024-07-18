<?php
require('config.php');
session_start(); // Start the session for storing user login status

// Check if form data is submitted
if(isset($_POST['member']) && isset($_POST['password'])) {
    $membership_no = $_POST['member'];
    $password = $_POST['password'];

    // Database connection
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    } else {
        $sql = "SELECT * FROM members WHERE membership_no = '$membership_no' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $memberData = $result->fetch_assoc();
            $_SESSION['member_id'] = $memberData['id'];
            $_SESSION['membership_no'] = $membership_no;
            $_SESSION['group'] = $memberData['group']; // Assuming 'group' is a column in your 'members' table

            // Redirect based on user's group
            if ($_SESSION['group'] == 3) {
                header("Location: officerdashboard.php");
                exit;
            } else {
                header("Location: memberdashboard.php");
                exit;
            }
        } else {
            $_SESSION['login_error'] = "Invalid membership number or password. Please try again.";
            header("Location: login.php");
            exit;
        }

        $conn->close();
    }
} else {
    echo "Invalid form submission.";
}
?>
