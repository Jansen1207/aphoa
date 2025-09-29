
<?php
require('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberId = $_POST['id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $middleName = $_POST['middle_name'];
    $contactNo = $_POST['contact_no'];
    $age = $_POST['age'];
    $emailAddress = $_POST['email_address'];
    $occupation = $_POST['occupation'];
    $address = $_POST['address'];
    $educAttainment = $_POST['educ_attainment'];
    $birthdate = $_POST['birthdate'];
    $sex = $_POST['sex'];
    $civilStatus = $_POST['civil_status'];
    $homeownerStatus = $_POST['homeowner_status'];
    $dos_status = $_POST['dos_status'];
    $lengthOfStay = $_POST['length_of_stay'];
    $ownerName = $_POST['owner_name'];

    
    $stmt = $conn->prepare("UPDATE information SET last_name=?, first_name=?, middle_name=?, contact_no=?, age=?, email_address=?, occupation=?, address=?, educ_attainment=?, birthdate=?, sex=?, civil_status=?, homeowner_status=?, dos_status=?, length_of_stay=?, owner_name=? WHERE member_id=?");
    $stmt->bind_param("ssssssssssssssssi", $lastName, $firstName, $middleName, $contactNo, $age, $emailAddress, $occupation, $address, $educAttainment, $birthdate, $sex, $civilStatus, $homeownerStatus, $dos_status, $lengthOfStay, $ownerName, $memberId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>
