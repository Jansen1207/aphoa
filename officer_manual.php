<?php
require('config.php');
session_start();
$sqlMembers = "SELECT m.*, i.* FROM members m 
               INNER JOIN information i ON m.id = i.member_id";
$resultMembers = $conn->query($sqlMembers);
$membersData = $resultMembers->fetch_all(MYSQLI_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $amount = $_POST['amount'];
    $mode_of_payment = $_POST['mode_of_payment'];
    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $filename = basename($_FILES["proof_of_payment"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO payments (member_id, month, year, amount, proof_of_payment, status, mode_of_payment, created_at) VALUES (?, ?, ?, ?, ?, 'approved', ?, NOW())");
            $stmt->bind_param("isdsds", $member_id, $month, $year, $amount, $filename, $mode_of_payment);
            $stmt->execute();
            $stmt->close();
            $conn->query("UPDATE dues SET status = 'paid' WHERE member_id = '$member_id' AND month = '$month' AND year = '$year'");
            header('Location: admin_payment.php?success=true');
            exit();
        } else {
            echo "Error uploading the file.";
        }
    }
}
$unpaidDues = [];
if (isset($_POST['check_dues'])) {
    $member_id = $_POST['member_id'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $sqlDues = "SELECT * FROM dues WHERE member_id = ? AND month = ? AND year = ? AND status = 'unpaid'";
    $stmt = $conn->prepare($sqlDues);
    $stmt->bind_param("isi", $member_id, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $unpaidDues = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manual Payment</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Manual Payment for Members</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="member_id">Select Member:</label>
            <select name="member_id" required>
                <option value="">Select a member</option>
                <?php foreach ($membersData as $member): ?>
                    <option value="<?php echo $member['id']; ?>">
                        <?php echo ucfirst($member['first_name']) . ' ' . ucfirst($member['last_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="month">Month:</label>
            <select name="month" required>
                <option value="">Select Month</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>"><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?></option>
                <?php endfor; ?>
            </select>
            <label for="year">Year:</label>
            <select name="year" required>
                <option value="">Select Year</option>
                <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" name="check_dues">Check Unpaid Dues</button>
        </form>
        <?php if (!empty($unpaidDues)): ?>
            <h3>Unpaid Dues</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
                <input type="hidden" name="month" value="<?php echo $month; ?>">
                <input type="hidden" name="year" value="<?php echo $year; ?>">
                <label for="amount">Amount:</label>
                <input type="number" name="amount" value="40" readonly>
                <label for="mode_of_payment">Mode of Payment:</label>
                <select name="mode_of_payment" required>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="online">Online Payment</option>
                </select>
                <label for="proof_of_payment">Proof of Payment:</label>
                <input type="file" name="proof_of_payment" required>
                <button type="submit">Submit Payment</button>
            </form>
        <?php else: ?>
            <p>No unpaid dues found for the selected member and date.</p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p>Payment recorded successfully!</p>
        <?php endif; ?>
    </div>
</body>
</html>