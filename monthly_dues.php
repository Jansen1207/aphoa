<?php
require('config.php');
session_start();

$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$memberData = $result->fetch_assoc();

// Get current date information
$currentDate = new DateTime();
$currentYear = (int)$currentDate->format('Y');
$currentMonth = (int)$currentDate->format('n');

// Calculate the end year (current year + 1)
$endYear = $currentYear + 1;
$startYear = 2019;

// Modified query to get all dues from 2019 up to next year
$duesSql = "SELECT * FROM dues 
            WHERE member_id = '{$_SESSION['member_id']}'
            AND year >= $startYear 
            AND year <= $endYear
            ORDER BY year, month";
$duesResult = $conn->query($duesSql);

$dueData = [];

// Initialize the dues structure for all months from 2019 to end year
for ($year = $startYear; $year <= $endYear; $year++) {
    for ($month = 1; $month <= 12; $month++) {
        $dueDate = new DateTime("$year-$month-01");
        $monthName = $dueDate->format('F');
        
        // Set default values for each month
        $dueData[$year][] = [
            'month' => $monthName,
            'year' => $year,
            'amount' => '₱40',
            'status' => 'unpaid',
            'remaining_days' => '',
            'status_class' => 'unpaid'
        ];
    }
}

// Update with actual payment data
while ($due = $duesResult->fetch_assoc()) {
    $dueDate = new DateTime("{$due['year']}-{$due['month']}-01");
    $year = (int)$due['year'];
    $monthIndex = (int)$due['month'] - 1; // Array is 0-based
    $status = $due['status'];

    if ($status === 'paid') {
        $remainingTime = '-';
    } else {
        $now = new DateTime();
        if ($dueDate > $now) {
            $interval = $now->diff($dueDate);
            $remainingTime = "{$interval->y} years, {$interval->m} months, {$interval->d} days";
        } else {
            $remainingTime = 'Overdue - Please pay to avoid having your account deactivated.';
        }
    }

    // Update the existing array with actual payment data
    if (isset($dueData[$year][$monthIndex])) {
        $dueData[$year][$monthIndex] = [
            'month' => $dueDate->format('F'),
            'year' => $year,
            'amount' => '₱40',
            'status' => $status,
            'remaining_days' => $remainingTime,
            'status_class' => ($status === 'paid') ? 'paid' : 'unpaid'
        ];
    }
}

// Calculate consecutive missed payments
$missed_payments = 0;
$consecutive_missed = 0;
$today = new DateTime();

// Create an array of all months from 2019 to current date
$allMonths = [];
for ($year = $startYear; $year <= $currentYear; $year++) {
    $endMonth = ($year == $currentYear) ? $currentMonth : 12;
    for ($month = 1; $month <= $endMonth; $month++) {
        $allMonths[] = ['year' => $year, 'month' => $month];
    }
}

// Check for consecutive missed payments in the last 3 months only
$last3Months = array_slice($allMonths, -3);
foreach ($last3Months as $monthData) {
    $year = $monthData['year'];
    $month = $monthData['month'] - 1; // Convert to 0-based index
    
    if (isset($dueData[$year][$month]) && $dueData[$year][$month]['status'] !== 'paid') {
        $consecutive_missed++;
    } else {
        $consecutive_missed = 0; // Reset if a payment is found
    }
}

// Set status based on consecutive missed payments in last 3 months
$status = ($consecutive_missed >= 3) ? 'inactive' : 'active';
$status_text = ($consecutive_missed >= 3) ? 'Inactive' : 'Active';

// Override status if member is marked as active in database
if (isset($memberData['active']) && $memberData['active'] == 1) {
    $status = 'active';
    $status_text = 'Active';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Dashboard</title>
    <link rel="stylesheet" href="./css/styles.css">
    <style>
        /* Enhanced styling for better visual presentation */
        .tabs {
            display: flex;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 5px;
        }

        .tabs button {
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .tabs button:hover {
            background-color: #e0e0e0;
        }

        .tabs button.active {
            background-color: #4CAF50;
            color: white;
            border-color: #45a049;
        }

        .monthly-dues-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .monthly-dues-table th, 
        .monthly-dues-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .monthly-dues-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .monthly-dues-table tr:hover {
            background-color: #f9f9f9;
        }

        .paid {
            color: #2ecc71;
            font-weight: bold;
        }

        .unpaid {
            color: #e74c3c;
            font-weight: bold;
        }

        .status {
            width: 200px;
            height: auto;
            border-radius: 20px;
            text-align: center;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 1.5em;
            font-weight: bold;
            color: white;
        }

        .status.active {
            background-color: #2ecc71;
        }

        .status.inactive {
            background-color: #e74c3c;
        }

        .year-dues {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive design adjustments */
        @media (max-width: 768px) {
            .tabs button {
                padding: 8px 15px;
                font-size: 14px;
            }

            .monthly-dues-table th, 
            .monthly-dues-table td {
                padding: 8px;
                font-size: 14px;
            }

            .status {
                width: 150px;
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Profile</h1>
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

        <div class="dashboard-body">
            <div class="main-content">
                <section class="monthly-dues-section">
                    <div class="status <?php echo $status; ?>">
                        <?php echo $status_text; ?>
                    </div>

                    <h3>Monthly Dues Summary</h3>
                    <div class="tabs">
                        <?php foreach ($dueData as $year => $months): ?>
                            <button class="year-tab" data-year="<?php echo $year; ?>"><?php echo $year; ?></button>
                        <?php endforeach; ?>
                    </div>

                    <div id="dues-container">
                        <?php foreach ($dueData as $year => $months): ?>
                            <div class="year-dues" id="year-<?php echo $year; ?>" style="display: none;">
                                <table class="monthly-dues-table">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Due Amount</th>
                                            <th>Status</th>
                                            <th>Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($months as $due): ?>
                                            <tr>
                                                <td><?php echo $due['month']; ?></td>
                                                <td><?php echo $due['amount']; ?></td>
                                                <td class="<?php echo $due['status_class']; ?>"><?php echo ucfirst($due['status']); ?></td>
                                                <td><?php echo $due['remaining_days']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Handle sidebar toggle
            var paymentLink = document.querySelector(".sidebar ul li a.current");
            paymentLink.addEventListener("click", function (e) {
                e.preventDefault();
                var parentLi = this.parentElement;
                parentLi.classList.toggle("active");
            });

            // Handle year tabs
            var tabs = document.querySelectorAll('.year-tab');
            var currentYear = new Date().getFullYear();

            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    // Hide all year sections
                    document.querySelectorAll('.year-dues').forEach(function(section) {
                        section.style.display = 'none';
                    });
                    
                    // Show the clicked year section
                    var year = this.getAttribute('data-year');
                    var yearSection = document.getElementById('year-' + year);
                    if (yearSection) {
                        yearSection.style.display = 'block';
                    }

                    // Update active tab
                    tabs.forEach(function(t) { 
                        t.classList.remove('active'); 
                    });
                    this.classList.add('active');
                });

                // If this tab is for the current year, make it active by default
                if (tab.getAttribute('data-year') == currentYear) {
                    tab.click();
                }
            });

            // If no tab was activated (current year not found), show the last year
            if (!document.querySelector('.year-tab.active') && tabs.length > 0) {
                tabs[tabs.length - 1].click();
            }
        });
    </script>
</body>
</html>