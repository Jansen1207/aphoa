<?php 
require('config.php');
session_start();

$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
if (!$result) {
    die("Error fetching member data: " . $conn->error);
}
$memberData = $result->fetch_assoc();

$paymentSql = "SELECT p.*, i.first_name, i.last_name, d.amount AS dues_amount 
               FROM payments p
               INNER JOIN information i ON p.member_id = i.member_id
               INNER JOIN dues d ON p.member_id = d.member_id
               WHERE p.status = 'pending' 
               AND p.year = d.year 
               AND p.month = d.month";
$paymentResult = $conn->query($paymentSql);

if (!$paymentResult) {
    die("Error fetching submitted payment verifications: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Dues Officer</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/monthly_dues_officer.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #337AB7;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header h1 {
            margin: 0;
        }
        .header img {
            border-radius: 50%;
        }
        .header select {
            background-color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px;
            cursor: pointer;
        }
        .main {
            padding: 20px;
        }
        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .filters input, .filters select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .filters input:focus, .filters select:focus {
            outline: none;
            border-color: #337AB7;
        }
        .verification-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .verification-table th,
        .verification-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .verification-table th {
            background-color: #337AB7;
            color: #fff;
        }
        .verification-table tr:hover {
            background-color: #f1f1f1;
        }
        .verification-table a {
            color: #337AB7;
            text-decoration: none;
        }
        .verification-table button {
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            margin: 2px;
            border-radius: 8px;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }
        button[name='action'][value='approve'] {
            background-color: green;
            color: white;
        }
        button[name='action'][value='approve']:hover {
            background-color: darkgreen;
        }
        button[name='action'][value='reject'] {
            background-color: red;
            color: white;
        }
        button[name='action'][value='reject']:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Monthly Dues</h1>
    <img src="./images/menicon.png" width="50" height="50">
    <form action="logout.php" method="POST">
        <select name="logout" onchange="this.form.submit()">
            <option><?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']); ?></option>
            <option style="background-color: #337AB7; color: #fff;" value="logout">Logout</option>
        </select>
    </form>
</div>

<?php include './includes/officer_sidebar.php'; ?>

<div class="main">
    <section class="payment-verification-section">
        <div class="filters">
            <input type="text" id="search-input" placeholder="Search by Member Name" onkeyup="searchByName()">
            <select id="year-filter" onchange="filterByYear()">
                <option value="All">All Years</option>
                <?php
                $yearQuery = "SELECT DISTINCT year FROM payments ORDER BY year DESC";
                $yearResult = $conn->query($yearQuery);
                if ($yearResult->num_rows > 0) {
                    while ($yearRow = $yearResult->fetch_assoc()) {
                        echo "<option value='{$yearRow['year']}'>{$yearRow['year']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <h3>Submitted Payment Verifications</h3>
        <table class="verification-table" id="verification-table">
            <thead>
                <tr>
                    <th>Member Name</th>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Amount</th>
                    <th>Proof of Payment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php
    if ($paymentResult->num_rows > 0) {
        while ($row = $paymentResult->fetch_assoc()) {
            $months = explode(', ', $row['month']);
            $monthNames = array_map(function($month) {
                return DateTime::createFromFormat('!m', $month)->format('F');
            }, $months);
            $monthDisplay = implode(', ', $monthNames);
            $paymentId = $row['id'];

            echo "<tr>
                <td class='member-name'>{$row['first_name']} {$row['last_name']}</td>
                <td>{$monthDisplay}</td>
                <td class='year'>{$row['year']}</td>
                <td>{$row['dues_amount']}</td>
                <td>
                    <a href='./uploads/{$row['proof_of_payment']}' target='_blank' onclick='viewProof({$paymentId})'>View</a>
                </td>
<td>
    <form method='POST' action='process_approval.php' onsubmit='return confirmAction({$paymentId}, event.submitter)'>
        <input type='hidden' name='payment_id' value='{$paymentId}'>
        <button type='submit' name='action' value='approve' id='approveBtn{$paymentId}'>Approve</button>
        <button type='submit' name='action' value='reject' id='rejectBtn{$paymentId}'>Reject</button>
    </form>
</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No submitted payment verifications</td></tr>";
    }
    ?>
</tbody>
        </table>
    </section>
</div>

<script>
let viewedProofs = {};

function viewProof(paymentId) {
    viewedProofs[paymentId] = true;
}

function confirmAction(paymentId, button) {
    if (!viewedProofs[paymentId]) {
        alert('Please view the proof of payment before proceeding.');
        return false;
    }

    // Determine the action from the button clicked
    const action = button.value;

    if (action === 'approve') {
        return confirm('Are you sure you want to approve this payment?');
    } else if (action === 'reject') {
        return confirm('Are you sure you want to reject this payment?');
    }
}

function searchByName() {
    const searchInput = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll('#verification-table tbody tr');

    rows.forEach(row => {
        const memberName = row.querySelector('.member-name').textContent.toLowerCase();
        row.style.display = memberName.includes(searchInput) ? '' : 'none';
    });
}

function filterByYear() {
    const year = document.getElementById('year-filter').value;
    const rows = document.querySelectorAll('#verification-table tbody tr');

    rows.forEach(row => {
        const rowYear = row.querySelector('.year').textContent;
        row.style.display = year === 'All' || rowYear === year ? '' : 'none';
    });
}
</script>
</body>
</html>