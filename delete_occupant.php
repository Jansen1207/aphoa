<?php
require('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $occupant_id = $_POST['occupant_id'];

    $stmt = $conn->prepare("DELETE FROM occupants WHERE occupant_id = ?");
    $stmt->bind_param("i", $occupant_id);
    $stmt->execute();

    header("Location: profile.php");
    exit();
}
?>
