<?php
require('config.php');

if (isset($_GET['id'])) {
    $occupantId = intval($_GET['id']); 

    $stmt = $conn->prepare("SELECT * FROM occupants WHERE occupant_id = ?");
    $stmt->bind_param("i", $occupantId);
    $stmt->execute();
    $result = $stmt->get_result();
    $occupantData = $result->fetch_assoc();

    if ($occupantData) {
        echo json_encode($occupantData); 
    } else {
        echo json_encode(['error' => 'No occupant found.']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request.']);
}

$conn->close();
?>
