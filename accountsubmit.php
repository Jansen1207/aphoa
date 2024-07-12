<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $membershipNO = $_POST['membershipNO'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo "Passwords do not match. Please go back and try again.";
        exit;
    }

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'aphoadb');
    if ($conn->connect_error) {
        die("Connection Failed : " . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("INSERT INTO members(membership_no, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $membershipNO, $password);
        
        // Execute the statement
        $execval = $stmt->execute();
        
        if ($execval === false) {
            echo "Error: " . $stmt->error;
        } else {
            // Redirect to login.php
            header("Location: login.php");
            exit;
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
