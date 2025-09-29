<?php
require('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $occupant_id = $_POST['occupant_id'];

    $stmt = $conn->prepare("DELETE FROM occupants WHERE occupant_id = ?");
    $stmt->bind_param("i", $occupant_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>
