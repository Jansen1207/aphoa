<?php
require('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $memberId = $_POST['member_id'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo "Passwords do not match. Please go back and try again.";
        exit;
    }

    if ($conn->connect_error) {
        die("Connection Failed : " . $conn->connect_error);
    } else {
        #$stmt = $conn->prepare("INSERT INTO members(membership_no, password) VALUES (?, ?)");
        $stmt = $conn->prepare("UPDATE members SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password, $memberId);
        
        // Execute the statement
        $execval = $stmt->execute();
        
        if ($execval === false) {
            echo "Error: " . $stmt->error;
        } else {
            // Redirect to login.php
            header("Location: login.php");
            exit;
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
