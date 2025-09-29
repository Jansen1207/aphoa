<?php
require('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement_id'], $_POST['announcement_text'], $_POST['title'])) {
    $announcementId = $_POST['announcement_id'];
    $announcementText = $_POST['announcement_text'];
    $announcementTitle = $_POST['title'];

    // Sanitize inputs
    $announcementId = intval($announcementId); // Ensure it's an integer
    $announcementText = $conn->real_escape_string($announcementText);
    $announcementTitle = $conn->real_escape_string($announcementTitle);

    // Prepare and execute the update statement
    $stmt = $conn->prepare("UPDATE announcements SET title = ?, message = ? WHERE id = ?");
    $stmt->bind_param("ssi", $announcementTitle, $announcementText, $announcementId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update announcement.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
