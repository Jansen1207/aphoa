<?php
require('config.php');
session_start();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination and search functionality
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$firstLetter = isset($_GET['firstLetter']) ? $_GET['firstLetter'] : '';

$search_query = !empty($search) ? "AND (i.first_name LIKE '%$search%' OR i.last_name LIKE '%$search%' OR p.mode_of_payment LIKE '%$search%')" : '';
$letter_query = !empty($firstLetter) ? "AND i.first_name LIKE '$firstLetter%'" : '';

// Fetching approved payments with sorting by first name
$sql = "SELECT i.first_name AS member_first_name, i.last_name AS member_last_name, 
               p.amount, p.approved_at, p.mode_of_payment, 
               p.month, p.year,
               i2.first_name AS approver_first_name, i2.last_name AS approver_last_name 
        FROM payments p 
        INNER JOIN members m ON p.member_id = m.id 
        INNER JOIN information i ON m.id = i.member_id
        INNER JOIN members m2 ON p.approved_by = m2.id
        INNER JOIN information i2 ON m2.id = i2.member_id
        WHERE p.status = 'approved' $search_query $letter_query
        ORDER BY i.first_name ASC 
        LIMIT $limit OFFSET $offset";
$payments_result = $conn->query($sql);

// Total entries for pagination
$total_sql = "SELECT COUNT(*) as total FROM payments p 
              INNER JOIN members m ON p.member_id = m.id 
              INNER JOIN information i ON m.id = i.member_id
              WHERE p.status = 'approved' $search_query $letter_query";
$total_result = $conn->query($total_sql);
$total_entries = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_entries / $limit);

// Fetch mode of payment counts for pie chart
$payment_modes_sql = "SELECT 
    CASE 
        WHEN mode_of_payment = 'bank_transfer' THEN 'Bank Transfer'
        WHEN mode_of_payment = 'CASH' THEN 'Cash'
        WHEN mode_of_payment = 'online' THEN 'Online'
        ELSE mode_of_payment 
    END AS mode_of_payment, 
    COUNT(*) as count 
    FROM payments 
    WHERE status = 'approved' 
    GROUP BY mode_of_payment";
$payment_modes_result = $conn->query($payment_modes_sql);
$payment_modes = [];
while ($row = $payment_modes_result->fetch_assoc()) {
    $payment_modes[] = $row;
}

function getMonthName($monthNumber) {
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
    return isset($months[$monthNumber]) ? $months[$monthNumber] : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Reports</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/financial_reports.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Approved Payments Section Container Styling */
        .section-container {
            margin: 20px 10;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        #paymentModeChartCanvas {
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="header">
    <table>
        <tr>
            <td>
                <h1 style="margin-bottom:1px;margin-top:1px;">Financial Reports</h1>
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
<?php include './includes/admin_sidebar.php'; ?>
<div class="main">
    <!-- Approved Payments Section -->
    <div class="section-container">
        <div class="section" id="paymentsSection">
            <h2>Approved Payments</h2>
            <div class="search-container" style="margin: 20px 0; text-align: center;">
                <form action="" method="GET">
                    <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 10px; width: 200px; border: 1px solid #ddd; border-radius: 5px;">
                    <button type="submit" style="padding: 10px; border: none; background-color: #007bff; color: white; border-radius: 5px;">Search</button>
                    <select name="firstLetter" onchange="this.form.submit()" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">All</option>
                        <?php
                        foreach (range('A', 'Z') as $letter) {
                            echo '<option value="' . $letter . '"' . ($letter === $firstLetter ? ' selected' : '') . '>' . $letter . '</option>';
                        }
                        ?>
                    </select>
                </form>
            </div>
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <thead>
                    <tr>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f2f2f2;">Member Name</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f2f2f2;">Amount</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f2f2f2;">Approved Date</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f2f2f2;">Mode of Payment</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f2f2f2;">Month</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f2f2f2;">Year</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd; background-color: #f2f2f2;">Approved By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($payments_result->num_rows > 0) {
                        while ($payment = $payments_result->fetch_assoc()) {
                            $approvedDate = date('F j, Y', strtotime($payment['approved_at']));
                            $monthName = getMonthName((int)$payment['month']);
                            
                            // Modify mode of payment to uppercase and transform 'bank_transfer' and 'CA SH'
                            $paymentMode = strtoupper($payment['mode_of_payment'] === 'bank_transfer' ? 'BANK TRANSFER' : 
                                               ($payment['mode_of_payment'] === 'CA SH' ? 'CASH' : $payment['mode_of_payment']));
                            
                            echo '
                                <tr>
                                    <td style="padding: 12px; border: 1px solid #ddd;"><strong>' . htmlspecialchars($payment['member_first_name']) . ' ' . htmlspecialchars($payment['member_last_name']) . '</strong></td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">₱' . number_format($payment['amount'], 2) . '</td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">' . htmlspecialchars($approvedDate) . '</td>
                                    <td style="padding: 12px; border: 1px solid #ddd;"><strong>' . htmlspecialchars($paymentMode) . '</strong></td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">' . htmlspecialchars($monthName) . '</td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">' . htmlspecialchars($payment['year']) . '</td>
                                    <td style="padding: 12px; border: 1px solid #ddd;"><strong>' . htmlspecialchars($payment['approver_first_name']) . ' ' . htmlspecialchars($payment['approver_last_name']) . '</strong></td>
                                </tr>
                            ';
                        }
                    } else {
                        echo '<tr><td colspan="7" style="padding: 12px; border: 1px solid #ddd; text-align: center;">No approved payments found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
	
	
	
	
    
    <div class="section-container">
    <div class="section">
        <h2>Update Monthly Dues</h2>
<form method="POST" action="update_dues.php">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <label for="year">Year:</label>
                <select id="year" name="year" style="padding: 10px; width: 100%; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="" disabled selected>Select Year</option>
                    <?php
                    $startYear = 2019;
                    $currentYear = date("Y");

                    for ($i = $startYear; $i <= $currentYear + 5; $i++) {
                        echo "<option value=\"$i\">$i</option>";
                    }
                    ?>
                </select>
            </td>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <label for="previous_dues">Previous Monthly Dues:</label>
                <input type="text" id="previous_dues" name="previous_dues" value="₱<?php echo number_format(10, 2); ?>" readonly style="padding: 10px; width: 95%; border-radius: 5px; border: 1px solid #ddd; background-color: #f2f2f2;">
            </td>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <label for="set_amount">Set Amount (₱):</label>
                <input type="number" id="set_amount" name="set_amount" placeholder="Set Amount" step="5" style="padding: 10px; width: 95%; border-radius: 5px; border: 1px solid #ddd;" required>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding: 10px; text-align: center;">
                <button type="butto" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px;" onclick="confirmUpdate()">Update Dues</button>
            </td>
        </tr>
    </table>
</form>

    </div>
</div>
	
	
	
	
	
	
	
	
    <!-- Pie Chart Section -->
    <div class="section-container">
        <div class="section" id="paymentsSection">
        <h2>Mode of Payment Statistics</h2>
        <canvas id="paymentModeChartCanvas"></canvas>
        <script>
    // Decode the PHP variable into a JavaScript array
    const paymentModes = <?php echo json_encode($payment_modes); ?>;
    const labels = paymentModes.map(item => item.mode_of_payment);
    const data = paymentModes.map(item => item.count);

    // Render the chart only once when the page loads
    new Chart(document.getElementById('paymentModeChartCanvas'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Payment Modes',
                data: data,
                backgroundColor: ['#FF6384', '#32CD32', '#36A2EB'], // Colors for chart
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom', // Position of the chart legend
                }
            }
        }
    });

    // This function handles the button click
    function confirmUpdate() {
        // Show confirmation dialog
        if (confirm("Are you sure you want to update it?")) {
            // Proceed with the update
            alert("Update confirmed!");
            // You can add additional update logic here
        } else {
            alert("Update canceled.");
        }
    }
</script>
    </div>
</div>
</body>
</html>