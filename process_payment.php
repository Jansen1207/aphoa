<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dues = isset($_POST['dues']) ? $_POST['dues'] : []; 
    $mode_of_payment = isset($_POST['mode_of_payment']) ? $_POST['mode_of_payment'] : '';
    $total = count($dues) * 40; 

    // Loop over the selected dues to check if payment is already pending or paid
    foreach ($dues as $key => $year) {
        list($month, $year) = explode('_', $key);

        // Query to check if there is an existing 'pending' or 'paid' payment for this month/year
        $sqlCheckPayment = "SELECT * FROM payments WHERE member_id = ? AND month = ? AND year = ? AND status IN ('pending', 'paid')";
        $stmt = $conn->prepare($sqlCheckPayment);
        $stmt->bind_param("sss", $_SESSION['member_id'], $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            // If a payment is found with 'pending' or 'paid' status, prevent the submission
            echo "<script>alert('You have already submitted or are pending payment for $month $year.'); window.location.href='monthly_dues.php';</script>";
            exit();
        }
    }

    // Proceed with payment processing if no conflicting payment exists
    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $filename = basename($_FILES["proof_of_payment"]["name"]);
        $target_file = $target_dir . $filename;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["proof_of_payment"]["tmp_name"]);
        if ($check === false) {
            $uploadOk = 0;
            echo "File is not an image.";
        }

        // Check file size (max 5MB)
        if ($_FILES["proof_of_payment"]["size"] > 5000000) { 
            $uploadOk = 0;
            echo "Sorry, your file is too large.";
        }

        // Allow only certain file formats
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $uploadOk = 0;
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // If everything is okay, upload the file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
                // Insert payment records into the database
                foreach ($dues as $key => $year) {
                    list($month, $year) = explode('_', $key); 
                    $amount = 40; 

                    // Insert payment into the database
                    $stmt = $conn->prepare("INSERT INTO payments (member_id, month, year, amount, proof_of_payment, status, mode_of_payment, created_at) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())");
                    if ($stmt) {
                        $stmt->bind_param("isssss", $_SESSION['member_id'], $month, $year, $amount, $filename, $mode_of_payment);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        echo "Error preparing statement: " . $conn->error;
                    }
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File upload failed.";
        }
    } else {
        echo "File upload error or no file uploaded.";
    }

    // Redirect to the Payment_MDues page
    header('Location: Payment_MDues.php');
    exit();
}
?>
