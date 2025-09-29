<?php
require('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['complaint_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $address = $_POST['address'];
    $incident_date = $_POST['incident_date'];

    $sql = "UPDATE complaints SET title = ?, description = ?, category = ?, address = ?, incident_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $description, $category, $address, $incident_date, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
