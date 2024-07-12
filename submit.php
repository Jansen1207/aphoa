<?php
require('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection    
    if ($conn->connect_error) {
        die("Connection Failed : " . $conn->connect_error);
    }

    // Prepare data for insertion (assuming the same column names as before)
    $membership_no = $_POST['membership_no'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? null;
    $contact_no = $_POST['contact_no'] ?? null;
    $email_address = $_POST['email_address'] ?? null;
    $occupation = $_POST['occupation'] ?? null;
    $address = $_POST['address'] ?? null;
    $educ_attainment = $_POST['educ_attainment'] ?? null;
    $birthdate = $_POST['birthdate'] ?? null;
    $date_submitted = $_POST['date_submitted'] ?? null;
    $sex = $_POST['sex'] ?? null;
    $civil_status = $_POST['civil_status'] ?? null;
    $homeowner_status = $_POST['homeowner_status'] ?? null;
    $type = $_POST['type'] ?? null;
    $length_of_stay = $_POST['length_of_stay'] ?? null;
    $owner_name = $_POST['owner_name'] ?? null;
    $occupant_name_1 = $_POST['occupant_name_1'] ?? null;
    $occupant_age_1 = $_POST['occupant_age_1'] ?? null;
    $occupant_relationship_1 = $_POST['occupant_relationship_1'] ?? null;

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO information (membership_no, last_name, first_name, middle_name, contact_no, email_address, occupation, address, educ_attainment, birthdate, date_submitted, sex, civil_status, homeowner_status, type, length_of_stay, owner_name, occupant_name_1, occupant_age_1, occupant_relationship_1) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssssisssis", $membership_no, $last_name, $first_name, $middle_name, $contact_no, $email_address, $occupation, $address, $educ_attainment, $birthdate, $date_submitted, $sex, $civil_status, $homeowner_status, $type, $length_of_stay, $owner_name, $occupant_name_1, $occupant_age_1, $occupant_relationship_1);

    // Execute and check if successful
    if ($stmt->execute()) {
        // Close statement
        $stmt->close();
        $conn->close();
        
        // Redirect back to createac.php with success message
        header("Location: createac.php?success=true");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
