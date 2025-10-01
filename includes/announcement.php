<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../config.php');

function submitAnnouncement($title, $message) {
    global $conn; // Use the global connection variable

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO announcements (title, message, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $title, $message);

    if ($stmt->execute()) {
        return true; // Return true on success
    } else {
        return "Error: " . $stmt->error; // Return error message on failure
    }

    $stmt->close();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (isset($_POST['title']) && isset($_POST['message'])) {
        $title = trim($_POST['title']);
        $message = trim($_POST['message']);
        
        // Call the function and handle the result
        $result = submitAnnouncement($title, $message);
        if ($result === true) {
            // Redirect to announcement_center.php after successful submission
            header("Location: /aphoa/announcement_center.php");
            exit(); // Stop script execution after redirect
        } else {
            echo $result; // Output the error message
        }
    } else {
        echo "Please fill in both title and message.";
    }
}
?>
