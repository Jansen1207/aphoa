<?php
require('config.php');
session_start();

// Check if member_id is set in session
if (!isset($_SESSION['member_id'])) {
    echo "Error: Member ID is not set in session.";
    exit();
}

// Fetch member details using prepared statements to avoid SQL injection
$memberId = $_SESSION['member_id'];
$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $memberId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the member data was retrieved
if ($result->num_rows > 0) {
    $memberData = $result->fetch_assoc();
} else {
    echo "Error: Member not found.";
    exit();
}

// Fetch payment data for the current member using prepared statements
$sqlPayments = "SELECT * FROM payments WHERE member_id = ?";
$stmtPayments = $conn->prepare($sqlPayments);
$stmtPayments->bind_param("i", $memberId);
$stmtPayments->execute();
$resultPayments = $stmtPayments->get_result();
$paymentsData = $resultPayments->fetch_all(MYSQLI_ASSOC);



// Function to check if a payment has already been made for a specific month/year
function isPaymentMade($month, $year, $paymentsData) {
    foreach ($paymentsData as $payment) {
        if ($payment['month'] == $month && $payment['year'] == $year && in_array($payment['status'], ['paid', 'pending'])) {
            return true;
        }
    }
    return false;
}

// Get selected year filter (default to current year if not set)
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Function to ensure all months exist in dues table with unpaid status
function ensureAllMonths($conn, $memberId, $year) {
    // Determine the dues amount based on the year
    $amount = ($year >= 2024) ? 40 : 10;
    // First, check if any dues records exist for this member
    $checkExistingSql = "SELECT COUNT(*) as count FROM dues WHERE member_id = ?";
    $checkExistingStmt = $conn->prepare($checkExistingSql);
    $checkExistingStmt->bind_param("i", $memberId);
    $checkExistingStmt->execute();
    $existingResult = $checkExistingStmt->get_result();
    $existingCount = $existingResult->fetch_assoc()['count'];

    // If this is a new member (no existing dues records)
    if ($existingCount == 0) {
        // Bulk insert all months as unpaid
        for ($month = 1; $month <= 12; $month++) {
            $insertSql = "INSERT INTO dues (member_id, month, year, amount, status) 
                         VALUES (?, ?, ?, 40, 'unpaid')";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iii", $memberId, $month, $year);
            $insertStmt->execute();
        }
    } else {
        // For existing members, just ensure all months exist
        for ($month = 1; $month <= 12; $month++) {
            $checkSql = "SELECT * FROM dues WHERE member_id = ? AND year = ? AND month = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("iii", $memberId, $year, $month);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows == 0) {
                // Insert missing month as unpaid
                $insertSql = "INSERT INTO dues (member_id, month, year, amount, status) 
                             VALUES (?, ?, ?, 40, 'unpaid')";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("iii", $memberId, $month, $year);
                $insertStmt->execute();
            }
        }
    }
}

// Initialize dues for years 2019-2050
for ($year = 2019; $year <= 2050; $year++) {
    ensureAllMonths($conn, $memberId, $year);
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }

        .container {
            display: flex;
            margin-top: 85px;
            margin-bottom: 30px;
            min-height: calc(100vh - 75px);
        }

        .sidebar a:hover {
            background-color: #555;
            border-left: 5px solid #fff;
            transform: scale(1.05);
        }

        .main-content {
            flex: 1;
            padding: 20px;
            margin: 0 auto;
            max-width: 1200px;
        }

        .payment-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin: 0 auto;
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #337AB7;
            color: #fff;
            border-radius: 5px;
        }

        .year-filter {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .year-filter select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .form-group button {
            background-color: #337AB7;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: #285a8e;
        }

        .transaction-history-container {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }

        .transaction-history-container h4 {
            color: #337AB7;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .verification-table {
            width: 100%;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 4px;
            overflow: hidden;
        }

        .verification-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 12px 15px;
        }

        .verification-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .payment-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    var checkboxes = document.querySelectorAll('.dues-checkbox');
    var totalInput = document.getElementById('total');

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var total = 0;
            checkboxes.forEach(function(cb) {
                if (cb.checked) {
                    total += parseFloat(cb.dataset.amount || 0); // Use the data-amount value
                }
            });
            totalInput.value = total.toFixed(2); // Format to 2 decimal places
        });
    });

    // Submit form when year filter changes
    document.getElementById('yearFilter').addEventListener('change', function() {
        this.form.submit();
    });
});

    </script>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Monthly Dues</h1>
                </td>
                <td align="right">
                    <img src="./images/menicon.png" width="50" height="50" style="border-radius: 50%;">
                </td>
                <td width="120">
                    &nbsp;&nbsp;&nbsp;
                    <form action="logout.php" method="POST">
                        <select name="logout" onchange="this.form.submit()">
                            <option><?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']) ?></option>
                            <option style="background-color: #337AB7;color:#fff;" value="logout">Logout</option>
                        </select>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <img src="./images/aphoa.png" alt="Anak Pawis Logo" class="logo">
            <h3>ANAK-PAWIS HOMEOWNERS' ASSOCIATION (APHOA), INC.</h3>
            <ul>
                <li><a href="member.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>

                <?php if ($memberData['active'] == 1): ?>
                    <li><a href="complaint.php">My Complaint</a></li>
                    <li><a href="monthly_dues.php">Monthly Dues Update</a></li>
                    <li><a class="current">Payment Categories<span class="arrow-down"></span></a>
                        <ul>
                            <li><a href="Payment_MDues.php">Monthly Dues</a></li>
                            <li><a href="Payment_Certification.php">Certification</a></li>
                            <li><a href="Payment_CarStickers.php">Car Stickers</a></li>
                        </ul>
                    </li>
                    <li><a href="documents.php">Documents</a></li>
                    <li><a href="achievements.php">Achievements</a></li>
                <?php else: ?>
                    <li><a href="monthly_dues.php">Monthly Dues Update</a></li>
                    <li><a href="Payment_MDues.php">Monthly Dues</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="main-content">
            <div class="payment-container">
                <div class="payment-header">
                    <h2 style="margin:0;">Monthly Dues</h2>
                    <h3 style="margin:0;">GCASH NUMBER: 09208918148</h3>
                    <h3 style="margin:0;">BANK ACCOUNT: 12345678798</h3>
                    <form method="GET" class="year-filter">
                        <label for="yearFilter">Filter by Year:</label>
                        <select id="yearFilter" name="year">
                            <?php
                            // Generate year options from 2019 to 2050
                            for ($year = 2019; $year <= 2050; $year++) {
                                $selected = ($year == $selectedYear) ? 'selected' : '';
                                echo "<option value='$year' $selected>$year</option>";
                            }
                            ?>
                        </select>
                    </form>
                </div>

                <form action="process_payment.php" method="POST" enctype="multipart/form-data">
                    <section class="payment-verification-section">
                        <table class="verification-table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Pay</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch the dues data for selected year
                                $sqlDues = "SELECT month, year, amount, status FROM dues 
                                          WHERE member_id = ? AND year = ? 
                                          ORDER BY month";
                                $stmtDues = $conn->prepare($sqlDues);
                                $stmtDues->bind_param("ii", $memberId, $selectedYear);
                                $stmtDues->execute();
                                $resultDues = $stmtDues->get_result();

                                while ($row = $resultDues->fetch_assoc()) {
                                    $monthName = date('F', mktime(0, 0, 0, $row['month'], 1));
                                    $isPaidOrPending = isPaymentMade($row['month'], $row['year'], $paymentsData);
                                    
                                    $statusClass = $row['status'] == 'paid' ? 'status-paid' : 
                                                ($row['status'] == 'pending' ? 'status-pending' : 'status-unpaid');
                                    
                                    // Replace checkbox with "✔" if status is 'paid'
                                    if ($row['status'] == 'paid') {
                                        $checkbox = "<span class='payment-status status-paid'>✔</span>";
                                    } else {
                                        $checkbox = $isPaidOrPending ? 
    "<span class='payment-status status-pending'>Pending Verification</span>" : 
    "<input type='checkbox' name='dues[{$row['month']}_{$row['year']}]' 
        value='{$row['year']}' 
        class='dues-checkbox' 
        data-amount='{$row['amount']}' 
        style='width: 20px; height: 20px;'>";

                                    }

                                    echo "<tr>
                                        <td>$monthName</td>
                                        <td>{$row['year']}</td>
                                        <td>{$row['amount']}</td>
                                        <td><span class='payment-status $statusClass'>" . ucfirst($row['status']) . "</span></td>
                                        <td>$checkbox</td>
                                    </tr>";
                                }
                                ?>
								</tbody>
                        </table>
                    </section>

                    <div class="form-group">
                        <label for="total">Total Amount:</label>
                        <input type="text" id="total" name="total" value="0" readonly>
                    </div>

                    <div class="form-group">
                        <label for="mode_of_payment">Mode of Payment:</label>
                        <select id="mode_of_payment" name="mode_of_payment">
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
                        <button type="submit" id="submitBtn">Submit Payment</button>
                    </div>
                </form>

                <div class="transaction-history-container">
                    <h4>Transaction History</h4>
                    <table class="verification-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Amount</th>
                                <th>Mode</th>
                                <th>Status</th>
                                <th>Approved Date</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paymentsData as $payment): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d', strtotime($payment['created_at'])); ?></td>
                                    <td><?php echo date('F', mktime(0, 0, 0, $payment['month'], 1)); ?></td>
                                    <td><?php echo htmlspecialchars($payment['year']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['dues_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['mode_of_payment']); ?></td>
                                    <td><span class="payment-status <?php echo $payment['status'] == 'paid' ? 'status-paid' : 'status-pending'; ?>">
                                        <?php echo htmlspecialchars(ucfirst($payment['status'])); ?>
                                    </span></td>
                                    <td><?php echo htmlspecialchars($payment['approved_at']); ?></td>
                                    <td>
                                        <?php if (!empty($payment['proof_of_payment'])): ?>
                                            <a href="uploads/<?php echo htmlspecialchars($payment['proof_of_payment']); ?>" target="_blank">View Receipt</a>
                                        <?php else: ?>
                                            No Receipt
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>