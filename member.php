<?php
require('config.php');
session_start();

$sql = "SELECT m.*, i.*, i.active FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$memberData = $result->fetch_assoc();

$complaints_sql = "SELECT title, description, created_at, status FROM complaints"; 
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

$financial_sql = "SELECT MONTH(created_at) as month, COUNT(*) as total 
                  FROM complaints 
                  WHERE YEAR(created_at) = 2023 
                  GROUP BY MONTH(created_at)";
$financial_result = $conn->query($financial_sql);

$financial_data = array_fill(0, 12, 0); 
while ($row = $financial_result->fetch_assoc()) {
    $financial_data[$row['month'] - 1] = (int)$row['total']; 
}

$category_sql = "SELECT category, COUNT(*) as count 
                 FROM complaints 
                 GROUP BY category";
$category_result = $conn->query($category_sql);

$category_data = [];
while ($row = $category_result->fetch_assoc()) {
    $category_data[$row['category']] = (int)$row['count'];
}

$announcements_sql = "SELECT * FROM announcements ORDER BY created_at DESC"; 
$announcements_result = $conn->query($announcements_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Membership Dashboard</title>
    <link rel="stylesheet" href="./css/styles.css">
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

        .announcement-container, .complaints-list-container, .analytics-container {
            margin: 10px 0;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            overflow: hidden;
        }

        .announcement-container h3, .complaints-list-container h3, .analytics-container h3 {
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
            justify-content: left;
            margin-top: 20px;
        }

        .financial-container, .complaints-container {
            margin: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 30px 15px;
            width: calc(50% - 40px);
            height: 300px;
        }

        canvas {
            max-width: 100%;
            height: auto;
        }

        /* Added CSS for active/inactive status */
        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-inactive {
            color: red;
            font-weight: bold;
        }
        
        /* Responsive Design for iPhone XR */
@media screen and (max-width: 828px) {
    body {
        font-size: 14px; /* Adjusted base font size for smaller screens */
    }

    .container {
        flex-direction: column;
    }

    .sidebar {
        width: 220px; /* Slightly narrower for smaller screens */
        font-size: 14px; /* Reduce font size in sidebar */
    }

    .dashboard-body {
        margin-left: 220px; /* Match the sidebar's width */
    }

    .announcement-container,
    .complaints-list-container,
    .analytics-container {
        padding: 15px; /* Reduced padding for smaller containers */
        width: 90%; /* Full width with small margins */
    }

    .announcement-container h3,
    .complaints-list-container h3,
    .analytics-container h3 {
        font-size: 18px; /* Smaller headings */
    }

    table {
        font-size: 12px; /* Reduce font size in tables */
    }

    th,
    td {
        padding: 6px; /* Adjust table cell padding */
    }

    .graphs-container {
        gap: 15px; /* Add spacing between graphs */
    }

    .financial-container,
    .complaints-container {
        width: 100%; /* Stretch to full width */
        height: auto; /* Dynamic height */
        padding: 20px; /* Reduced padding */
    }

    canvas {
        width: 100%;
        height: auto;
    }
}
      
      
        
    </style>
</head>
<body>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var paymentLink = document.querySelector(".sidebar ul li a.current");
            paymentLink.addEventListener("click", function (e) {
                e.preventDefault();
                var parentLi = this.parentElement;
                parentLi.classList.toggle("active");
            });
        });
    </script>
    
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
                            <option><?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']); ?></option>
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
            <!-- Active -->
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
            <!-- Inactive-->
            <li><a href="monthly_dues.php">Monthly Dues Update</a></li>
            <li><a href="Payment_MDues.php">Monthly Dues</a></li>
        <?php endif; ?>
    </ul>
</div>


        
        <div class="dashboard-body">
            <h3>Membership No: <?php echo htmlspecialchars($memberData['membership_no']); ?></h3>
            <h2>Welcome, <?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']); ?></h2>
            <!-- Status with color -->
            <h3 class="<?php echo $memberData['active'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                Status: 
                <?php 
                echo $memberData['active'] == 1 ? 'Active' : 'Inactive'; 
                ?>
            </h3>

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
                <h3>Analytics Overview</h3>
                <div class="graphs-container">
                    <div>
                        <h4>Complaints Overview</h4>
                        <canvas id="complaintsPieChart"></canvas>
                    </div>
                    
                    <div>
         
                    </div>
                    
                    <div>
                        <h4>Complaint Categories</h4>
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
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

        const categoryLabels = <?php echo json_encode(array_keys($category_data)); ?>;
        const categoryCounts = <?php echo json_encode(array_values($category_data)); ?>;

        const categoryData = {
            labels: categoryLabels,
            datasets: [{
                data: categoryCounts,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
            }]
        };

        const categoryCtx = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: categoryData
        });
    </script>
</body>
</html>
