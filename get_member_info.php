<?php
require('config.php');

$memberId = $_GET['id'];

$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$memberId}'";
$result = $conn->query($sql);
$memberData = $result->fetch_assoc();

if ($memberData) {
    $occupantsSql = "SELECT * FROM occupants WHERE member_id = '{$memberId}'";
    $occupantsResult = $conn->query($occupantsSql);
    $occupants = [];

    while ($occupant = $occupantsResult->fetch_assoc()) {
        $occupants[] = $occupant;
    }

    $memberData['occupants'] = $occupants; 
    echo json_encode($memberData); 
} else {
    echo json_encode(['error' => 'No details found for this member.']);
}
?>
