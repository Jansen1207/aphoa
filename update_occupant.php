<?php
require('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['occupant_id']) && isset($_POST['name']) && isset($_POST['age']) && isset($_POST['relationship'])) {
        $occupantId = $_POST['occupant_id'];
        $name = $_POST['name'];
        $age = $_POST['age'];
        $relationship = $_POST['relationship'];

        
        $stmt = $conn->prepare("UPDATE occupants SET name=?, age=?, relationship=? WHERE occupant_id=?");
        $stmt->bind_param("sssi", $name, $age, $relationship, $occupantId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    }
}
$conn->close();
?>
