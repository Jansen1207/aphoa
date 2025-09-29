<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $member_id = $_POST['member_id'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $total = $_POST['total'];
    $mode_of_payment = $_POST['mode_of_payment'];

    // Handle file upload
    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $filename = basename($_FILES["proof_of_payment"]["name"]);
        $target_file = $target_dir . $filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
            // Prepare to insert into payments table
            $stmt = $conn->prepare("INSERT INTO payments (member_id, month, year, amount, proof_of_payment, status, mode_of_payment, created_at) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())");

            if ($stmt) {
                $stmt->bind_param("isssss", $member_id, $month, $year, $total, $filename, $mode_of_payment);
                if ($stmt->execute()) {
                    echo "Payment successfully recorded!";
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "Error with file upload.";
    }

    // Redirect to a confirmation page or show a success message
    header('Location: Payment_MDues.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Monthly Dues</title>
    <link rel="stylesheet" href="./css/styles.css">
    <style>
        /* Your existing styles here */
        /* ... */
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Dues</h1>
        <!-- User Logout Form -->
        <form action="logout.php" method="POST">
            <select name="logout" onchange="this.form.submit()">
                <option><?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']) ?></option>
                <option style="background-color: #337AB7;color:#fff;" value="logout">Logout</option>
            </select>
        </form>
    </div>
    
    <div class="container">
        <div class="main-content">
            <div class="payment-container">
                <h3>Monthly Dues Payment</h3>
                <form action="manual_process_payment.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="member_id">Select User:</label>
        <select id="member_id" name="member_id" required>
            <?php
            $sqlMembers = "SELECT id, first_name, last_name FROM members";
            $resultMembers = $conn->query($sqlMembers);
            while ($member = $resultMembers->fetch_assoc()) {
                echo "<option value=\"{$member['id']}\">" . ucfirst($member['first_name']) . ' ' . ucfirst($member['last_name']) . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="month">Month:</label>
        <select id="month" name="month" required>
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>"><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="year">Year:</label>
        <select id="year" name="year" required>
            <?php for ($y = 2024; $y <= 2030; $y++): ?>
                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="total">Total Amount:</label>
        <input type="text" id="total" name="total" required>
    </div>

    <div class="form-group">
        <label for="mode_of_payment">Mode of Payment:</label>
        <select id="mode_of_payment" name="mode_of_payment" required>
            <option value="cash">Cash</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="online">Online Payment</option>
        </select>
    </div>

    <div class="form-group">
        <label for="proof_of_payment">Proof of Payment:</label>
        <input type="file" id="proof_of_payment" name="proof_of_payment" required>
    </div>

    <div class="form-group">
        <button type="submit">Submit Payment</button>
    </div>
</form>

            </div>
        </div>
    </div>
</body>
</html>
