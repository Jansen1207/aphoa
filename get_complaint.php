<?php
require('config.php');
session_start();

// Assuming you have the complaint ID already
$complaint_id = intval($_GET['id']);

// Query to fetch the complaint details, including the 'comment' column from the 'complaints' table
$sql = "SELECT * FROM complaints WHERE id = $complaint_id AND member_id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$complaint = $result->fetch_assoc();

if ($complaint) {
    // Fetch associated comments from the complaint_comments table (if any)
    $comments_sql = "SELECT * FROM complaint_comments WHERE complaint_id = $complaint_id ORDER BY created_at DESC";
    $comments_result = $conn->query($comments_sql);
    $comments = [];
    while ($comment = $comments_result->fetch_assoc()) {
        $comments[] = [
            'comment' => $comment['comment'],
            'created_at' => $comment['created_at']
        ];
    }

    // Prepare the response with the complaint details and comments
    $response = [
        'id' => $complaint['id'],
        'title' => $complaint['title'],
        'description' => $complaint['description'],
        'category' => $complaint['category'],
        'address' => $complaint['address'],
        'incident_date' => $complaint['incident_date'],
        'status' => $complaint['status'],
        'created_at' => $complaint['created_at'],
        'files' => explode(',', $complaint['files']),
        'comment' => $complaint['comment'],  // Fetch the 'comment' directly from the complaints table
        'comments' => $comments // Include comments from the complaint_comments table
    ];

    // Return the response as JSON
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Complaint not found.']);
}
?>
