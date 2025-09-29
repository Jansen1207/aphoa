<?php
require('config.php');
session_start();


$complaint_id = intval($_GET['id']);


$sql = "DELETE FROM complaints WHERE id = $complaint_id AND member_id = '{$_SESSION['member_id']}'";

if ($conn->query($sql) === TRUE) {
    echo "Complaint deleted successfully!";
    header("Location: complaint.php");
    exit;
} else {
    echo "Error deleting complaint: " . $conn->error;
}
?>
