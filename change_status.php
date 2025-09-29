<?php
require('config.php');
session_start();
$currentYear = date('Y');
$endYear = $currentYear + 1; 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dues_id'])) {
    $duesId = $_POST['dues_id'];
    if (empty($duesId)) {
        echo "No dues_id received."; 
        exit;
    }
    $stmt = $conn->prepare("UPDATE dues SET status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $duesId); 
    if ($stmt->execute()) {
        header("Location: receipts.php?success=1");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();
}