<?php 
require('config.php');
session_start();

// Set how many results you want per page
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Handle sorting by first letter
$selected_letter = isset($_GET['letter']) ? $_GET['letter'] : '';
$letter_filter = !empty($selected_letter) ? " WHERE first_name LIKE '$selected_letter%'" : '';

// Handle search
$search = isset($_POST['search']) ? $_POST['search'] : '';
$search_sql = !empty($search) ? " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%'" : '';
$sql = "SELECT first_name, last_name, date_submitted 
        FROM information" 
        . (!empty($letter_filter) ? $letter_filter : $search_sql) . 
       " LIMIT $start_from, $results_per_page";
$result = $conn->query($sql);

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

// Get total members for pagination
$total_sql = "SELECT COUNT(*) as total 
              FROM information" 
              . (!empty($letter_filter) ? $letter_filter : $search_sql);
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_members = $total_row['total'];
$total_pages = ceil($total_members / $results_per_page);

// Handle year filtering
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$years = range(2019, date('Y'));

// Monthly members logic based on selected year
$monthlyMembers = [];
for ($month = 1; $month <= 12; $month++) {
    $sql = "SELECT COUNT(*) as count 
            FROM information 
            WHERE MONTH(date_submitted) = $month 
              AND YEAR(date_submitted) = $selected_year
              AND date_submitted IS NOT NULL";
    $result = $conn->query($sql);

    // Fallback to zero if the query fails
    if ($result) {
        $row = $result->fetch_assoc();
        $monthlyMembers[] = isset($row['count']) ? (int)$row['count'] : 0;
    } else {
        $monthlyMembers[] = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/membership.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .member-actions .btn-add-member {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
        }
        .pagination {
            display: inline-block;
            margin: 20px 0;
        }
        .pagination a {
            padding: 8px 16px;
            margin: 0 4px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #007bff;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
        }
        .pagination a:hover:not(.active) {
            background-color: #f1f1f1;
        }
        .member-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .member-table th, .member-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .member-table th {
            background-color: #007bff;
            color: white;
        }
        .member-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .member-table tr:hover {
            background-color: #f1f1f1;
        }
        .name-bold {
            font-weight: bold;
        }
        .charts-wrapper {
            margin-top: 40px;
        }
        .chart-section {
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="header">
    <table>
        <tr>
            <td>
                <h1 style="margin-bottom:1px;margin-top:1px;">Membership</h1>
            </td>
            <td align="right">
                <img src="./images/menicon.png" width="50" height="50" style="border-radius: 50%;">
            </td>
            <td width="120">
                &nbsp;&nbsp;&nbsp;
                <form action="logout.php" method="POST">
                    <select name="logout" onchange="this.form.submit()">
                        <option><?php echo ucfirst($_SESSION['first_name']) . ' ' . ucfirst($_SESSION['last_name']); ?></option>
                        <option style="background-color: #337AB7;color:#fff;" value="logout">Logout</option>
                    </select>
                </form>
            </td>
        </tr>
    </table>
</div>
<?php include './includes/admin_sidebar.php'; ?>

<div class="main">
    <div class="memberlist-container">
        <div class="member-actions">
            <button class="btn-add-member" onclick="window.location.href='register.php';">Add New Member</button>
        </div>

        <h2>Approved Members - Homeowners Association</h2>
        
        <!-- Sorting by first letter -->
        <form method="GET" action="" style="margin-bottom: 20px;">
            <label for="letter">Filter by First Letter: </label>
            <select name="letter" id="letter" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">
                <option value="">All</option>
                <?php foreach (range('A', 'Z') as $letter): ?>
                    <option value="<?php echo $letter; ?>" <?php echo $selected_letter === $letter ? 'selected' : ''; ?>>
                        <?php echo $letter; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <form method="POST" action="" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">
            <input type="submit" value="Search" style="padding: 8px 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
        </form>

        <table class="member-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date Approved</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member) : ?>
                    <?php 
                        $date = strtotime($member['date_submitted']);
                        if (date('Y', $date) == 1970) {
                            continue;
                        }
                        $formattedDate = date('F j, Y', $date);
                    ?>
                    <tr>
                        <td class="name-bold"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                        <td><?php echo $formattedDate; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&letter=<?php echo $selected_letter; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <div class="charts-wrapper">
        <div class="chart-section">
            <h2>New Members</h2>

            <!-- Year Filter Dropdown -->
            <form method="GET" action="" style="margin-bottom: 20px;">
                <label for="year">Filter by Year: </label>
                <select name="year" id="year" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo $year; ?>" <?php echo $selected_year === $year ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="chart-container">
                <canvas id="bar-chart"></canvas>
            </div>

            <script>
                const monthlyMembers = <?php echo json_encode($monthlyMembers); ?>;
                const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                const ctx = document.getElementById('bar-chart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: `New Members in ${<?php echo $selected_year; ?>}`,
                            data: monthlyMembers,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>
</div>
</body>
</html>