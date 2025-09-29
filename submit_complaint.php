<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('config.php');
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);
    $address = $conn->real_escape_string($_POST['address']);
    $incident_date = $conn->real_escape_string($_POST['incident_date']);
    $member_id = $_SESSION['member_id']; 

    
    $uploaded_files = [];
    if (isset($_FILES['supporting_docs']) && !empty($_FILES['supporting_docs']['name'][0])) {
        $file_count = count($_FILES['supporting_docs']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            $file_name = $_FILES['supporting_docs']['name'][$i];
            $file_tmp = $_FILES['supporting_docs']['tmp_name'][$i];
            $file_path = 'uploads/' . basename($file_name); 

            
            if (move_uploaded_file($file_tmp, $file_path)) {
                $uploaded_files[] = $file_path;
            }
        }
    }

    $sql = "INSERT INTO complaints (member_id, title, description, category, address, incident_date, files, status)
    VALUES ('$member_id', '$title', '$description', '$category', '$address', '$incident_date', '" . implode(',', $uploaded_files) . "', 'Under Review')";

    
    if ($conn->query($sql) === TRUE) {
        echo "Complaint submitted successfully!";
            
    header('Location: complaint.php');
    } else {
        echo "Error: " . $conn->error;
    }

    
    $conn->close();
}
?>
