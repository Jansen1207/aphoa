<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require('config.php');

$memberId = $_POST['id'];
// Sanitize user input before using in query
$firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
// Similarly sanitize other form data

$sql = "UPDATE members m 
        INNER JOIN information i ON m.id = i.member_id
        SET 
            m.first_name = '$firstName',
            -- Add a comma here
            i.last_name = '$lastName',
            -- Add more fields as needed
        WHERE m.id = '$memberId'";
if ($conn->query($sql) === TRUE) {
  echo "Profile updated successfully!";
  // Optionally, redirect back to the member information page (`get_member_info.php?id=$memberId`)
} else {
  echo "Error updating profile: " . $conn->error;
}
?>