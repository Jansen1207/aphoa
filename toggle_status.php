<?php
require('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $memberId = $_POST['id'];
    $status = $_POST['status'];

    
    $memberId = intval($memberId);
    $status = intval($status);

    
    $sql = "UPDATE information SET active = ? WHERE member_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $status, $memberId);
    
    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status.";
    }

    $stmt->close();
    $conn->close();
}
?>
