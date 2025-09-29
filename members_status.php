<?php
require('config.php');
session_start();

// Set how many results you want per page
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Handle search
$search = isset($_POST['search']) ? $_POST['search'] : '';
$search_sql = !empty($search) ? " WHERE i.first_name LIKE '%$search%' OR i.last_name LIKE '%$search%'" : '';

// Add alphabet filter logic
$alphabet_filter = isset($_GET['alphabet']) ? $_GET['alphabet'] : '';
$alphabet_filter_sql = !empty($alphabet_filter) ? " AND i.first_name LIKE '$alphabet_filter%'" : '';
$search_sql .= $alphabet_filter_sql;

// Sorting logic
$order_by = isset($_GET['sort']) ? $_GET['sort'] : 'first_name';
$order_by_sql = " ORDER BY i.$order_by ASC";

// Fetch members with pagination
$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id" . $search_sql . $order_by_sql . " LIMIT $start_from, $results_per_page";
$result = $conn->query($sql);

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

// Get total members for pagination
$total_sql = "SELECT COUNT(*) as total FROM members m 
              INNER JOIN information i ON m.id = i.member_id" . $search_sql;
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_members = $total_row['total'];
$total_pages = ceil($total_members / $results_per_page);

// Count active and inactive members
$activeCount = 0;
$inactiveCount = 0;

foreach ($members as $member) {
    if ($member['active']) {
        $activeCount++;
    } else {
        $inactiveCount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Status</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/members_status.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="header">
    <table>
        <tr>
            <td>
                <h1 style="margin-bottom:1px;margin-top:1px;">Members Status</h1>
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

<?php include './includes/admin_sidebar.php'; ?>

<div class="main">
    <div class="member-status-container">
        <h2>Member Status</h2>

        <!-- Search Form -->
        <form method="POST" action="" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">
            <input type="submit" value="Search" style="padding: 8px 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
        </form>

        <!-- Alphabet Filter Dropdown -->
        <form method="GET" action="" style="margin-bottom: 20px;">
            <label for="alphabet" style="margin-right: 10px;">Filter by First Name:</label>
            <select name="alphabet" id="alphabet" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                <option value="">All</option>
                <?php foreach (range('A', 'Z') as $letter): ?>
                    <option value="<?php echo $letter; ?>" <?php echo $alphabet_filter === $letter ? 'selected' : ''; ?>>
                        <?php echo $letter; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($order_by); ?>">
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
        </form>

        <!-- Members Table -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #eaeaea; padding: 10px; background-color: #f7f7f7;">
                        <a href="?sort=first_name" style="text-decoration: none; color: inherit;">Name</a>
                    </th>
                    <th style="border: 1px solid #eaeaea; padding: 10px; background-color: #f7f7f7;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td style="border: 1px solid #eaeaea; padding: 10px;">
                            <strong><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></strong>
                        </td>
                        <td style="border: 1px solid #eaeaea; padding: 10px; color: <?php echo $member['active'] ? 'green' : 'red'; ?>;">
                            <?php echo $member['active'] ? 'Active' : 'Inactive'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination" style="margin: 20px 0;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($order_by); ?>&alphabet=<?php echo urlencode($alphabet_filter); ?>" style="padding: 8px 12px; margin: 0 4px; text-decoration: none; color: #007bff; border: 1px solid #007bff; border-radius: 5px; <?php if ($i == $page) echo 'background-color: #007bff; color: white;'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section" style="margin-top: 40px;">
        <h2>Status Chart</h2>
        <div class="chart-container" style="width: 100%; max-width: 600px; margin: auto;">
            <canvas id="pie-chart"></canvas>
        </div>
    </div>
</div>

<script>
    const pieData = {
        labels: ['Active', 'Inactive'],
        datasets: [{
            label: 'Member Status',
            data: [<?php echo $activeCount; ?>, <?php echo $inactiveCount; ?>],
            backgroundColor: ['#36A2EB', '#FF6384']
        }]
    };

    const pieCtx = document.getElementById('pie-chart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: pieData,
    });
</script>
</body>
</html>
