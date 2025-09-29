<?php
require('config.php'); 
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    $sql = "
        SELECT c.*, i.first_name, i.last_name, i.email_address AS email, i.contact_no AS phone 
        FROM complaints c
        INNER JOIN members m ON c.member_id = m.id
        INNER JOIN information i ON m.id = i.member_id
        WHERE c.id = $id
    ";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $complaint = $result->fetch_assoc();
        echo '<p><strong>Name:</strong> ' . htmlspecialchars($complaint['first_name'] . ' ' . $complaint['last_name']) . '</p>';
        echo '<p><strong>Complaint ID:</strong> ' . htmlspecialchars($complaint['id']) . '</p>';
        echo '<p><strong>Title:</strong> ' . htmlspecialchars($complaint['title']) . '</p>';
        echo '<p><strong>Description:</strong> ' . nl2br(htmlspecialchars($complaint['description'])) . '</p>';
        echo '<p><strong>Category:</strong> ' . htmlspecialchars($complaint['category']) . '</p>';
        echo '<p><strong>Address:</strong> ' . htmlspecialchars($complaint['address']) . '</p>';
        echo '<p><strong>Incident Date:</strong> ' . htmlspecialchars($complaint['incident_date']) . '</p>';
        echo '<p><strong>Status:</strong> ' . htmlspecialchars($complaint['status']) . '</p>';
        echo '<p><strong>Created At:</strong> ' . htmlspecialchars($complaint['created_at']) . '</p>';
        echo '<p><strong>Email:</strong> ' . htmlspecialchars($complaint['email']) . '</p>';
        echo '<p><strong>Phone:</strong> ' . htmlspecialchars($complaint['phone']) . '</p>';

        // Display comment
        if (!empty($complaint['comment'])) {
            echo '<p><strong>Comment:</strong> ' . nl2br(htmlspecialchars($complaint['comment'])) . '</p>';
        } else {
            echo '<p><strong>Comment:</strong> No comment provided.</p>';
        }

        // Display files if any
        if (!empty($complaint['files'])) {
            echo '<p><strong>Files:</strong> <a href="' . htmlspecialchars($complaint['files']) . '" target="_blank">View File</a></p>';
        }
    } else {
        echo '<p>No details found for this complaint.</p>';
    }
} else {
    echo '<p>Invalid request.</p>';
}
?>
