<?php
require('config.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['member_id']) && isset($_POST['name']) && isset($_POST['age']) && isset($_POST['relationship'])) {
        $memberId = $_POST['member_id'];
        $name = $_POST['name'];
        $age = $_POST['age'];
        $relationship = $_POST['relationship'];
        $sql = "INSERT INTO occupants (member_id, name, age, relationship) VALUES ('$memberId', '$name', $age, '$relationship')";
        $result = $conn->query($sql);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Execution failed: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    }
}
$conn->close();
?>