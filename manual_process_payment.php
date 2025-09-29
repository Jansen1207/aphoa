<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = isset($_POST['member_id']) ? $_POST['member_id'] : $_SESSION['member_id']; // Use selected user
    $mode_of_payment = isset($_POST['mode_of_payment']) ? $_POST['mode_of_payment'] : '';
    $total = 40; // Set the total amount for one month

    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $filename = basename($_FILES["proof_of_payment"]["name"]);
        $target_file = $target_dir . $filename;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["proof_of_payment"]["tmp_name"]);
        if ($check === false) {
            $uploadOk = 0;
            echo "File is not an image.";
        }

        if ($_FILES["proof_of_payment"]["size"] > 5000000) {
            $uploadOk = 0;
            echo "Sorry, your file is too large.";
        }

        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $uploadOk = 0;
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
                // Insert the payment for the selected user
                $month = isset($_POST['month']) ? $_POST['month'] : '';
                $year = isset($_POST['year']) ? $_POST['year'] : '';

                // Prepare the statement for inserting into payments
                $stmt = $conn->prepare("INSERT INTO payments (member_id, month, year, amount, proof_of_payment, status, mode_of_payment, created_at) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())");
                if ($stmt) {
                    $stmt->bind_param("isssss", $member_id, $month, $year, $total, $filename, $mode_of_payment);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $conn->error;
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

    header('Location: Payment_MDues.php');
    exit();
}
?>
