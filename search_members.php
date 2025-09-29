<?php
require('config.php');
session_start();
$searchId = isset($_GET['search_id']) ? $_GET['search_id'] : '';
$sql = "SELECT m.*, i.* 
        FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.membership_no LIKE '%$searchId%' 
        OR i.first_name LIKE '%$searchId%' 
        OR i.last_name LIKE '%$searchId%'
        OR i.contact_no LIKE '%$searchId%' 
        OR i.address LIKE '%$searchId%' 
        OR i.dos_status LIKE '%$searchId%' 
        OR i.length_of_stay LIKE '%$searchId%'";
error_log("SQL Query: " . $sql);
$result = $conn->query($sql);
if ($result) {
    $membersData = [];
    while ($row = $result->fetch_assoc()) {
        $membersData[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($membersData);
} else {
    echo json_encode(['error' => $conn->error]);
}
?>