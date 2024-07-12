<?php
require 'vendor/autoload.php'; // for PHPMailer and Twilio

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'HOA');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $payment_date = date('Y-m-d');

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["proof_of_payment"]["name"]);
    if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
        // Insert payment into database
        $stmt = $conn->prepare("INSERT INTO payments (user_id, amount, payment_date, payment_method, proof_of_payment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $user_id, $amount, $payment_date, $payment_method, $target_file);
        $stmt->execute();
        
        // Fetch user details
        $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $email = $user['email'];
            $phone = $user['phone'];

            // Send email notification
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.example.com'; // Replace with your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@example.com'; // Replace with your email address
                $mail->Password = 'your_password'; // Replace with your email password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('your_email@example.com', 'HOA'); // Replace with your email address
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Payment Confirmation';
                $mail->Body    = 'Thank you for your payment of ' . $amount . '. Your payment method: ' . $payment_method;

                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            // Send SMS notification
            $sid = 'your_twilio_sid'; // Replace with your Twilio SID
            $token = 'your_twilio_token'; // Replace with your Twilio token
            $client = new Client($sid, $token);

            $client->messages->create(
                $phone,
                array(
                    'from' => 'your_twilio_number', // Replace with your Twilio number
                    'body' => 'Thank you for your payment of ' . $amount . '. Your payment method: ' . $payment_method
                )
            );

            echo "Payment submitted successfully!";
        } else {
            echo "User not found.";
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$conn->close();
?>
