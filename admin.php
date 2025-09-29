<?php
require('config.php');
session_start();
$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$memberData = $result->fetch_assoc();
if ($_SESSION['is_admin'] != 'y') {
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
$registrations_sql = "SELECT MONTH(date_submitted) AS month, COUNT(*) AS count 
                      FROM information 
                      WHERE YEAR(date_submitted) = $currentYear 
                      GROUP BY MONTH(date_submitted)";
$registrations_result = $conn->query($registrations_sql);
$registrations_data = array_fill(0, 12, 0);
while ($row = $registrations_result->fetch_assoc()) {
    $registrations_data[$row['month'] - 1] = (int)$row['count'];
}
$payment_status_sql = "SELECT status, COUNT(*) AS count FROM payments GROUP BY status";
$payment_status_result = $conn->query($payment_status_sql);
$payment_status_data = [];
while ($row = $payment_status_result->fetch_assoc()) {
    $payment_status_data[$row['status']] = (int)$row['count'];
}
$complaints_over_time_sql = "SELECT MONTH(created_at) AS month, COUNT(*) AS count 
                              FROM complaints 
                              WHERE YEAR(created_at) = $currentYear 
                              GROUP BY MONTH(created_at)";
$complaints_over_time_result = $conn->query($complaints_over_time_sql);
$complaints_over_time_data = array_fill(0, 12, 0);
while ($row = $complaints_over_time_result->fetch_assoc()) {
    $complaints_over_time_data[$row['month'] - 1] = (int)$row['count'];
}
$top_members_sql = "SELECT member_id, SUM(amount) AS total_paid 
                    FROM payments 
                    WHERE status = 'approved' 
                    GROUP BY member_id 
                    ORDER BY total_paid DESC 
                    LIMIT 5";
$top_members_result = $conn->query($top_members_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/admin.css">
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
            margin-top: 20px;
        }
        .financial-container, .complaints-container, .category-container, .registrations-container {
            margin: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 30px 15px;
            height: 50%;
        }
        .financial-container {
            width: 400px
        }
        canvas {
            max-width: 100%;
            height: auto;
        }
        .analytics-container {
            margin: 20px 0;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            width: 400px
        }
        .analytics-container h2 {
            margin: 0 0 10px;
        }
        .analytics-container p {
            margin: 5px 0;
            font-size: 16px;
        }
        .payment-status-container{
            margin: 20px 0;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            width: 400px
        }
        .new-member-container{
            margin: 20px 0;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            width: 400px
        }
        .complaints-overtime-container{
            margin: 50px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            width: 400px 
        }
    </style>
</head>
<body>
<div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Admin</h1>
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
                        <small><?php echo date('d M Y', strtotime($announcement['created_at'])); ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <div class="graphs-container">
            <div class="financial-container">
                <h3>Financial Overview</h3>
                <p>Total Payments This Year: <?php echo number_format($total_payments, 2); ?></p>
                <canvas id="financialChart"></canvas>
            </div>
            <div class="complaints-container">
                <h3>Complaints Overview</h3>
                <p>Resolved: <?php echo $complaint_data['Resolved']; ?></p>
                <p>Under Review: <?php echo $complaint_data['Under Review']; ?></p>
                <canvas id="complaintsChart"></canvas>
            </div>
        <div class="new-member-container">
            <h2>New Member Registrations (<?php echo $currentYear; ?>)</h2>
            <canvas id="registrationsBarChart"></canvas>
        </div>
        <div class="payment-status-container">
            <h2>Payment Status Overview</h2>
            <canvas id="paymentStatusChart"></canvas>
        </div>
        <div class="complaints-overtime-container" style="width: 50%">
            <h2>Complaints Over Time</h2>
            <canvas id="complaintsOverTimeChart"></canvas>
        </div>
        </div>
    </div>
</div>
<script>
const financialData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
        label: 'Total Payments (₱)',
        data: [<?php echo implode(',', $financial_data); ?>],
        backgroundColor: '#4BC0C0',
    }]
};
const financialCtx = document.getElementById('financialChart').getContext('2d');
new Chart(financialCtx, {
    type: 'line',
    data: financialData,
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
const complaintsData = {
    labels: ['Resolved', 'Under Review'],
    datasets: [{
        label: 'Complaints Status',
        data: [<?php echo $complaint_data['Resolved']; ?>, <?php echo $complaint_data['Under Review']; ?>],
        backgroundColor: ['#36A2EB', '#FF6384'],
    }]
};
const complaintsCtx = document.getElementById('complaintsChart').getContext('2d');
new Chart(complaintsCtx, {
    type: 'pie',
    data: complaintsData,
});
const registrationsData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
        label: 'New Registrations',
        data: [<?php echo implode(',', $registrations_data); ?>],
        backgroundColor: '#FFCE56',
    }]
};
const registrationsCtx = document.getElementById('registrationsBarChart').getContext('2d');
new Chart(registrationsCtx, {
    type: 'bar',
    data: registrationsData,
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
const paymentStatusData = {
    labels: Object.keys(<?php echo json_encode($payment_status_data); ?>),
    datasets: [{
        label: 'Payment Status',
        data: Object.values(<?php echo json_encode($payment_status_data); ?>),
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
    }]
};
const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
new Chart(paymentStatusCtx, {
    type: 'pie',
    data: paymentStatusData,
});
const complaintsOverTimeData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
        label: 'Complaints Over Time',
        data: [<?php echo implode(',', $complaints_over_time_data); ?>],
        backgroundColor: '#FF6384',
    }]
};
const complaintsOverTimeCtx = document.getElementById('complaintsOverTimeChart').getContext('2d');
new Chart(complaintsOverTimeCtx, {
    type: 'line',
    data: complaintsOverTimeData,
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
</body>
</html>