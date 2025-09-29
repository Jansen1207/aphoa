<?php
require('config.php');
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $relationship = $_POST['relationship'];
    $member_id = $_SESSION['member_id'];
    $stmt = $conn->prepare("INSERT INTO occupants (member_id, name, age, relationship) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $member_id, $name, $age, $relationship);
    $stmt->execute();
    header("Location: profile.php");
    exit();
}
?>