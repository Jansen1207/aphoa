<?php
require('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM announcements WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Redirect to announcement_center.php after successful deletion
            header("Location: /announcement_center.php");
            exit(); // Make sure to call exit after redirection
        } else {
            // Optionally, you could handle the error here
            echo "Error deleting announcement: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>
