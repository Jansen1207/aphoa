<?php
require('config.php');
$query = $_GET['query'] ?? '';
$query = $conn->real_escape_string($query);
$sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM information WHERE CONCAT(first_name, ' ', last_name) LIKE '%$query%'";
$result = $conn->query($sql);
$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row;
}
echo json_encode($suggestions);
?>