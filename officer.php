<?php
require('config.php');
session_start();

$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";

$result = $conn->query($sql);
$memberData = $result->fetch_assoc();

if ($_SESSION['is_officer'] != 'y') {
    header('Location: member.php');
    exit;
}


$complaints_sql = "SELECT title, description, created_at, status, category FROM complaints"; 
$complaints_result = $conn->query($complaints_sql);


$complaint_data = [
    'Resolved' => 0,
    'Under Review' => 0,
];

while ($row = $complaints_result->fetch_assoc()) {
    if (isset($complaint_data[$row['status']])) {
        $complaint_data[$row['status']]++;
    }
}


$complaints_result->data_seek(0);


$category_sql = "SELECT category, COUNT(*) as count 
                 FROM complaints 
                 GROUP BY category";
$category_result = $conn->query($category_sql);


$category_data = [];
while ($row = $category_result->fetch_assoc()) {
    $category_data[$row['category']] = (int)$row['count'];
}


$currentYear = date('Y');
$financial_sql = "SELECT MONTH(created_at) AS month, SUM(amount) AS total 
                  FROM payments 
                  WHERE YEAR(created_at) = $currentYear AND status = 'approved' 
                  GROUP BY MONTH(created_at)";
$financial_result = $conn->query($financial_sql);


$financial_data = array_fill(0, 12, 0);
while ($row = $financial_result->fetch_assoc()) {
    $financial_data[$row['month'] - 1] = (float)$row['total'];
}



$announcements_sql = "SELECT * FROM announcements ORDER BY created_at DESC"; 
$announcements_result = $conn->query($announcements_sql);




$total_payments_sql = "SELECT SUM(amount) AS total_payments FROM payments 
                       WHERE YEAR(created_at) = $currentYear AND status = 'approved'";
$total_payments_result = $conn->query($total_payments_sql);
$total_payments = $total_payments_result->fetch_assoc()['total_payments'];




$total_members_sql = "SELECT COUNT(*) as total FROM members";
$total_members_result = $conn->query($total_members_sql);
$total_members = $total_members_result->fetch_assoc()['total'];


$active_members_sql = "SELECT COUNT(*) as active_count FROM information WHERE active = 1";
$active_members_result = $conn->query($active_members_sql);
$active_count = $active_members_result->fetch_assoc()['active_count'];


$inactive_count = $total_members - $active_count;






$currentMonth = date('n'); 
$currentYear = date('Y'); 

$due_payments_sql = "SELECT status 
                     FROM dues 
                     WHERE month = $currentMonth AND year = $currentYear";

$due_payments_result = $conn->query($due_payments_sql);

$due_paid = 0;
$due_unpaid = 0;

while ($row = $due_payments_result->fetch_assoc()) {
    if ($row['status'] === 'paid') {
        $due_paid++;
    } else {
        $due_unpaid++;
    }
}


$due_data = [
    'Paid' => $due_paid,
    'Unpaid' => $due_unpaid,
];






?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard</title>
    <link rel="stylesheet" href="./css/member.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }

        .container {
            display: flex;
            margin-top: 75px;
            height: calc(100vh - 80px);
        }

        .sidebar a:hover {
            background-color: #555;
            border-left: 5px solid #fff;
            transform: scale(1.05);
        }

        .dashboard-body {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        .announcement-container, .complaints-list-container {
            margin: 10px 0;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            overflow: hidden;
        }

        .announcement-container h3, .complaints-list-container h3 {
            margin: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f2f2f2;
        }

        .graphs-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 5px;
            margin-top: 50px;
            
        }

        .financial-container, .complaints-container, .category-container, .due-payments-container {
            flex: 1 1 calc(45% - 20px);
            background-color: #fff;
            border-radius: 10px;
           box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            min-width: 300px;
            max-width: 500px;
        }

        canvas {
            max-width: 100%;
            height: auto;
        }

        .analytics-container {
            margin: 10px 0;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            width: 40%;
        }

        .analytics-container h2 {
            margin: 0 0 10px;
        }

        .analytics-container p {
            margin: 5px 0;
            font-size: 16px;
        }

        .financial-container h3, .complaints-container h3, .category-container h3, .due-payments-container h3 {
            text-align: center;
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
            
        }
        
        
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Dashboard</h1>
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
    
    <?php include './includes/officer_sidebar.php'; ?>
        
    <div class="dashboard-body">
        <h3>Membership No: <?php echo htmlspecialchars($memberData['membership_no']); ?></h3>
        <h2>Welcome, <?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']); ?></h2>

        <div class="announcement-container">
            <h3>Announcements</h3>
            <ul>
                <?php while ($announcement = $announcements_result->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($announcement['title']); ?></strong><br>
                        <?php echo nl2br(htmlspecialchars($announcement['message'])); ?><br>
                        <small><?php echo htmlspecialchars($announcement['created_at']); ?></small>
                    </li>
                    <hr>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="complaints-list-container">
            <h3>All Complaints</h3>
            <table>
                <thead>
                    <tr style="text-align: left;">
                        <th>Title</th>
                        <th>Description</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($complaint = $complaints_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['description']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="analytics-container">
            <h2>Member Analytics</h2>
            <p>Total Members: <strong><?php echo $total_members; ?></strong></p>
            <p>Active Members: <strong><?php echo $active_count; ?></strong></p>
            <p>Inactive Members: <strong><?php echo $inactive_count; ?></strong></p>
        </div>

        <div class="graphs-container">
            <div class="complaints-container">
                <h3>Complaints Overview</h3>
                <canvas id="complaintsPieChart"></canvas>
            </div>

            <div class="category-container">
                <h3>Complaint Categories</h3>
                <canvas id="categoryPieChart"></canvas>
            </div>

            <div class="due-payments-container">
                <h3>Due Payments This Month</h3>
                <canvas id="duePaymentsChart"></canvas>
            </div>
            
            <div class="financial-container">
                <h3>Financial Reports (<?php echo $currentYear; ?>)</h3>
                <p>Total Accumulated Payments: 
                    <strong><?php echo number_format($total_payments, 2); ?> PHP</strong>
                </p>
                <canvas id="financialBarChart"></canvas>
            </div>
        </div>
    </div>
    
    <script>
        const complaintsData = {
            labels: ['Resolved', 'Under Review'],
            datasets: [{
                data: [<?php echo implode(',', $complaint_data); ?>],
                backgroundColor: ['#36A2EB', '#FF6384'],
            }]
        };

        const complaintsCtx = document.getElementById('complaintsPieChart').getContext('2d');
        new Chart(complaintsCtx, {
            type: 'pie',
            data: complaintsData
        });

        const financialData = {
            labels: [
                'January', 'February', 'March', 'April', 'May', 
                'June', 'July', 'August', 'September', 'October', 
                'November', 'December'
            ],
            datasets: [{
                label: 'Total Payments (<?php echo $currentYear; ?>)',
                data: [<?php echo implode(',', $financial_data); ?>],
                backgroundColor: '#36A2EB',
            }]
        };

        const financialCtx = document.getElementById('financialBarChart').getContext('2d');
        new Chart(financialCtx, {
            type: 'bar',
            data: financialData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const categoryData = {
            labels: <?php echo json_encode(array_keys($category_data)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($category_data)); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
            }]
        };

        const categoryCtx = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: categoryData
        });

        const duePaymentsData = {
            labels: ['Paid', 'Unpaid'],
            datasets: [{
                data: [<?php echo implode(',', $due_data); ?>],
                backgroundColor: ['#36A2EB', '#FF6384'],
            }]
        };

        const duePaymentsCtx = document.getElementById('duePaymentsChart').getContext('2d');
        new Chart(duePaymentsCtx, {
            type: 'pie',
            data: duePaymentsData
        });
    </script>
</body>
</html>