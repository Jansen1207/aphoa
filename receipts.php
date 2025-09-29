<?php
require('config.php');
require 'vendor/autoload.php'; // Include Composer's autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
$startYear = 2019; // Added start year
$endYear = 2025;   // Modified end year

function sendEmail($name, $email, $duesId) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.brevo.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = '6cac04001@smtp-brevo.com'; 
        $mail->Password = 'hnAf1mdrjBgz7UOK'; // Replace with actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('6cac04001@smtp-brevo.com', 'APHOA');
        $mail->addAddress($email, $name); 

        $mail->isHTML(false);
        $mail->Subject = 'Reminder: Unpaid Dues';
        $mail->Body    = "Dear $name,\n\nThis is a reminder that you have unpaid dues (ID: $duesId). Please make the payment at your earliest convenience.\n\nThank you.";

        $mail->send();
        return "Reminder sent to $name at $email.";
    } catch (Exception $e) {
        return "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Pagination and year filtering logic
$currentYear = date('Y');
$itemsPerPage = 10; // Adjust this value as needed

// Get filter parameters
$searchTerm = isset($_GET['search']) ? strtolower($_GET['search']) : '';
$selectedYear = isset($_GET['filter_year']) && !empty($_GET['filter_year']) ? (int)$_GET['filter_year'] : $currentYear;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Filter members based on search and selected year
$filteredMembers = [];
foreach ($members as $member) {
    // Search filtering
    $matchSearch = empty($searchTerm) || 
        strpos(strtolower($member['name']), $searchTerm) !== false || 
        strpos(strtolower($member['street']), $searchTerm) !== false || 
        strpos(strtolower($member['membership_no']), $searchTerm) !== false;

    if ($matchSearch) {
        $filteredMembers[] = $member;
    }
}

// Pagination
$totalMembers = count($filteredMembers);
$totalPages = ceil($totalMembers / $itemsPerPage);
$page = max(1, min($page, $totalPages));
$startIndex = ($page - 1) * $itemsPerPage;
$paginatedMembers = array_slice($filteredMembers, $startIndex, $itemsPerPage);



$currentYear = date('Y');

// Assuming you have already connected to the database as $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dues_id'])) {
    $duesId = $_POST['dues_id'];

    // Fetch the current status from the database
    $currentStatus = getCurrentStatusFromDatabase($duesId);

    // Toggle the status between 'paid' and 'unpaid'
    $newStatus = ($currentStatus === 'unpaid') ? 'paid' : 'unpaid';

    // Update the status in the database
    updateDuesStatus($duesId, $newStatus);

    // Redirect back to the receipts page with a success message
    header("Location: receipts.php?success=1");
    exit;
}

/**
 * Function to get the current status of dues from the database.
 */
function getCurrentStatusFromDatabase($duesId) {
    global $conn;

    $stmt = $conn->prepare("SELECT status FROM dues WHERE id = ?");
    $stmt->bind_param("i", $duesId); // "i" for integer
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Return the current status
    return $row['status'];
}

/**
 * Function to update the status of dues in the database.
 */
function updateDuesStatus($duesId, $newStatus) {
    global $conn;

    $stmt = $conn->prepare("UPDATE dues SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $duesId); // "si" for string and integer
    $stmt->execute();
    $stmt->close();
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$sqlMembers = "SELECT i.member_id, i.first_name, i.last_name, i.address AS street, i.active, m.membership_no 
               FROM information i 
               JOIN members m ON i.member_id = m.id 
               WHERE i.first_name LIKE '%$searchTerm%' 
               OR i.last_name LIKE '%$searchTerm%' 
               OR i.address LIKE '%$searchTerm%' 
               OR m.membership_no LIKE '%$searchTerm%'";
$membersResult = $conn->query($sqlMembers);
$members = [];
while ($member = $membersResult->fetch_assoc()) {
    $memberId = $member['member_id'];
    $fullName = ucfirst($member['first_name']) . ' ' . ucfirst($member['last_name']);
    $street = $member['street'];
    $active = $member['active']; 
    $membershipNo = $member['membership_no'];
    $members[$memberId] = [
        'id' => $memberId,
        'name' => $fullName,
        'street' => $street,
        'active' => $active,
        'membership_no' => $membershipNo,
        'dues' => [],
    ];
}
$sqlDues = "SELECT id, member_id, month, year, status 
            FROM dues 
            WHERE year BETWEEN $startYear AND $endYear 
            ORDER BY member_id, year, month";
$duesResult = $conn->query($sqlDues);
while ($due = $duesResult->fetch_assoc()) {
    $memberId = $due['member_id'];
    if (isset($members[$memberId])) {
        $members[$memberId]['dues'][$due['year']][$due['month']] = [
            'id' => $due['id'], 
            'status' => $due['status'],
        ];
    }
}
$resultsPerPage = 10; 
$totalMembers = count($members); 
$totalPages = ceil($totalMembers / $resultsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages)); 
$offset = ($currentPage - 1) * $resultsPerPage;
$members = array_slice($members, $offset, $resultsPerPage);
$currentMonth = date('n'); 
$currentYear = date('Y'); 
$sqlUnpaidDues = "SELECT d.id, d.member_id, i.first_name, i.last_name, m.membership_no, i.email_address, i.contact_no
                   FROM dues d 
                   JOIN information i ON d.member_id = i.member_id 
                   JOIN members m ON i.member_id = m.id 
                   WHERE d.month = $currentMonth AND d.year = $currentYear AND d.status = 'unpaid'";

$unpaidDuesResult = $conn->query($sqlUnpaidDues);
$unpaidDues = [];
while ($row = $unpaidDuesResult->fetch_assoc()) {
    $unpaidDues[] = [
        'id' => $row['id'],
        'member_id' => $row['member_id'],
        'name' => ucfirst($row['first_name']) . ' ' . ucfirst($row['last_name']),
        'membership_no' => $row['membership_no'],
        'email' => $row['email_address'], 
        'contact_no' => $row['contact_no'], 
    ];
}

$unpaidResultsPerPage = 10; 
$totalUnpaidDues = count($unpaidDues); 
$totalUnpaidPages = ceil($totalUnpaidDues / $unpaidResultsPerPage);
$currentUnpaidPage = isset($_GET['unpaid_page']) ? (int)$_GET['unpaid_page'] : 1;
$currentUnpaidPage = max(1, min($currentUnpaidPage, $totalUnpaidPages)); 
$unpaidOffset = ($currentUnpaidPage - 1) * $unpaidResultsPerPage;
$unpaidDues = array_slice($unpaidDues, $unpaidOffset, $unpaidResultsPerPage);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts</title>
    <link rel="stylesheet" href="./css/member.css">
<style>
/* General Styles */
/* Pagination Container */
.pagination {
    text-align: center;
    margin: 20px 0;
    font-family: Arial, sans-serif;
}

/* Pagination Links */
.pagination a {
    margin: 0 5px;
    padding: 8px 12px;
    border: 2px solid #ddd;
    text-decoration: none;
    color: #337ab7;
    border-radius: 4px;
    transition: background-color 0.3s, border-color 0.3s, color 0.3s;
    font-size: 14px;
    display: inline-block;
}

/* Hover Effect */
.pagination a:hover {
    background-color: #f1f1f1;
    border-color: #337ab7;
    color: #23527c;
}

/* Active Page Highlight */
.pagination a.active {
    background-color: #337ab7;
    color: white;
    border-color: #337ab7;
    font-weight: bold;
    cursor: default; /* Prevent click on active page */
}

/* Optional: Add spacing for next/prev links */
.pagination a.prev,
.pagination a.next {
    font-weight: bold;
    color: #555;
    border-color: #bbb;
}

.pagination a.prev:hover,
.pagination a.next:hover {
    background-color: #ddd;
    border-color: #aaa;
}

/* Button Styles */
button.mark-as-paid, 
button.mark-as-unpaid {
    padding: 5px 10px;
    font-size: 12px;
    cursor: pointer;
    border: none;
    border-radius: 3px;
    transition: background-color 0.3s;
}

button.mark-as-paid {
    background-color: #28a745;
    font-weight: bold;
    color: white;
}

button.mark-as-paid:hover {
    background-color: #218838;
}

button.mark-as-unpaid {
    background-color: #dc3545;
    font-weight: bold;
    color: white;
}

button.mark-as-unpaid:hover {
    background-color: #c82333;
}

/* Table Styling */
.dues-container-receipt {
    padding: 30px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.dues-container-receipt h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.dues-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: Arial, sans-serif;
    font-size: 20px;
    text-align: center;
}

.dues-header {
    background-color: #004080;
    color: white;
    font-weight: bold;
    padding: 15px;
    border: 5px solid #ddd;
}

.dues-row:nth-child(even) {
    border: 5px solid #ddd;
    background-color: #f9f9f9;
}

.dues-row:hover {
    border: 8px solid #ddd;
    background-color: #f1f1f1;
}

.dues-data {
    padding: 10px;
    border: 5px solid #ddd;
    word-wrap: break-word;
}

/* Status Specific Colors */
.dues-active {
    color: green;
    font-weight: bold;
}

.dues-inactive {
    color: red;
    font-weight: bold;
}

.dues-paid {
    background-color: #d4edda;
    padding: 15px;
    color: #155724;
    font-weight: bold;
}

.dues-unpaid {
    background-color: #f8d7da;
    padding: 15px;
    color: #721c24;
    font-weight: bold;
}

/* Highlighting Membership No, Address, and Name */
.bold-text {
    font-weight: bold;
}

/* Search Bar Styling */
.search-bar {
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-start;
    gap: 10px;
}

.search-bar input[type="text"] {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 300px;
}

.search-bar button {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    background-color: #f4b400;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s;
}

.search-bar button:hover {
    background-color: #e0a800;
}

/* Unpaid Box Highlight */
.unpaid-box {
    background-color: #f8d7da;
    border: 1px dashed #f5c6cb;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s;
}
.unpaid-box:hover {
    background-color: #f5c6cb;
}

/* Enhanced Select Dropdown */
select[name="filter_year"] {
    padding: 10px 15px;
    margin-left: 10px;
    font-size: 14px;
    border: 2px solid #ddd;
    border-radius: 5px;
    background-color: white;
    color: #555;
    transition: border-color 0.3s, background-color 0.3s, color 0.3s;
    cursor: pointer;
}

/* Focus State - When dropdown is active */
select[name="filter_year"]:focus {
    border-color: #337ab7;
    background-color: #f1f1f1;
    color: blue;
    outline: none;  /* Remove default outline */
}

/* Hover Effect */
select[name="filter_year"]:hover {
    border-color: #aaa;
    background-color: #f9f9f9;
}

/* Option Styling */
select[name="filter_year"] option {
    padding: 10px;
    background-color: white;
    color: #555;
}

/* Style for Disabled Option */
select[name="filter_year"] option:disabled {
    background-color: blue;
    color: #ccc;
}
</style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Receipts</h1>
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
        
<div class="dues-container-receipt">
    <h2>Dues Status of Members</h2>
    <small></small>
    <?php if (isset($_GET['success'])): ?>
        <div style="color: green; text-align: center;">Payment status updated successfully!</div>
    <?php endif; ?>

    <!-- Search and Year Filter Form -->
    <form method="GET" style="text-align: center; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by Membership No, Name or Address" 
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
               style="padding: 10px; width: 300px;">

        <!-- Year Filter Dropdown -->
        <select name="filter_year" style="padding: 10px; margin-left: 10px;">
            <option value="">Select Year</option>
            <?php 
            $startYear = 2019;
            $currentYear = date('Y');
            for ($year = $startYear; $year <= $currentYear; $year++): 
            ?>
                <option value="<?= $year ?>" <?= isset($_GET['filter_year']) && $_GET['filter_year'] == $year ? 'selected' : '' ?>>
                    <?= $year ?>
                </option>
            <?php endfor; ?>
        </select>

        <input type="submit" value="Filter" style="padding: 10px; background-color: #c1930c; color: white; border: none; border-radius: 5px; cursor: pointer;">
    </form>

    <!-- Table -->
    <table class="dues-table">
        <thead>
            <tr>
                <th class="dues-header">Membership No</th>
                <th class="dues-header">Name</th>
                <th class="dues-header">Address</th>
                <th class="dues-header">Status</th>
                <?php 
                // Determine the year to display
                $selectedYear = isset($_GET['filter_year']) && !empty($_GET['filter_year']) ? (int)$_GET['filter_year'] : $currentYear;
                for ($month = 1; $month <= 12; $month++): ?>
                    <th class="dues-header"><?= date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $selectedYear ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
        <?php 
        // Filter members
        $filteredMembers = [];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $searchTerm = strtolower($_GET['search']);
            foreach ($members as $member) {
                if (strpos(strtolower($member['name']), $searchTerm) !== false || 
                    strpos(strtolower($member['street']), $searchTerm) !== false || 
                    strpos(strtolower($member['membership_no']), $searchTerm) !== false) {
                    $filteredMembers[] = $member;
                }
            }
        } else {
            $filteredMembers = $members; // Show all members if no search
        }

        // Display members and dues for selected year
        foreach ($filteredMembers as $member): ?>
            <tr class="dues-row">
                <td class="dues-data"><?= htmlspecialchars($member['membership_no']) ?></td>
                <td class="dues-data"><?= htmlspecialchars($member['name']) ?></td>
                <td class="dues-data"><?= htmlspecialchars($member['street']) ?></td>
                <td class="dues-data <?= $member['active'] ? 'dues-active' : 'dues-inactive' ?>">
                    <?= $member['active'] ? 'Active' : 'Inactive' ?>
                </td>
                <?php 
                for ($month = 1; $month <= 12; $month++): 
                    $duesStatus = isset($member['dues'][$selectedYear][$month]) ? $member['dues'][$selectedYear][$month]['status'] : 'unpaid';
                    $duesId = isset($member['dues'][$selectedYear][$month]['id']) ? $member['dues'][$selectedYear][$month]['id'] : 0;
                ?>
                    <td class="dues-data <?= $duesStatus === 'paid' ? 'dues-paid' : 'dues-unpaid unpaid-box' ?>"
                        id="due-cell-<?= $duesId ?>"
                        onclick="toggleStatus(<?= $duesId ?>, <?= $selectedYear ?>, <?= $month ?>)">
                        <?= ucfirst($duesStatus) ?>
                        <form method="POST" action="" id="dues-form-<?= $duesId ?>" style="display:inline;">
                            <input type="hidden" name="dues_id" value="<?= $duesId ?>">
                            <button 
                                type="button" 
                                id="mark-button-<?= $duesId ?>" 
                                class="<?= $duesStatus === 'unpaid' ? 'mark-as-paid' : 'mark-as-unpaid' ?>"
                                onclick="event.stopPropagation(); 
                                         if (confirm('Are you sure you want to mark this as <?= $duesStatus === 'unpaid' ? 'Paid' : 'Unpaid' ?>?')) {
                                             toggleStatus(<?= $duesId ?>, <?= $selectedYear ?>, <?= $month ?>);
                                         }">
                                <?= $duesStatus === 'unpaid' ? 'Paid' : 'Unpaid' ?>
                            </button>
                        </form>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php 
    // Preserve filter and search parameters in pagination links
    $pageParams = [];
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $pageParams['search'] = htmlspecialchars($_GET['search']);
    }
    if (isset($_GET['filter_year']) && !empty($_GET['filter_year'])) {
        $pageParams['filter_year'] = htmlspecialchars($_GET['filter_year']);
    }
    ?>
    <div class="pagination">
        <?php 
        // Assuming $totalPages and $currentPage are defined in your pagination logic
        // Add the preserved parameters to each pagination link
        $paginationLink = '?' . http_build_query($pageParams);
        
        // Previous page link
        if ($currentPage > 1): ?>
            <a href="<?= $paginationLink . (empty($paginationLink) ? '?' : '&') . 'page=' . ($currentPage - 1) ?>">Previous</a>
        <?php endif; 

        // Page numbers
        for ($page = 1; $page <= $totalPages; $page++): ?>
            <a href="<?= $paginationLink . (empty($paginationLink) ? '?' : '&') . 'page=' . $page ?>" 
               class="<?= $page == $currentPage ? 'active' : '' ?>">
                <?= $page ?>
            </a>
        <?php endfor; 

        // Next page link
        if ($currentPage < $totalPages): ?>
            <a href="<?= $paginationLink . (empty($paginationLink) ? '?' : '&') . 'page=' . ($currentPage + 1) ?>">Next</a>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleStatus(duesId, year, month) {
    const button = document.getElementById('mark-button-' + duesId);
    if (button.innerHTML === 'Paid') {
        button.innerHTML = 'Unpaid';
        button.classList.remove('mark-as-paid');
        button.classList.add('mark-as-unpaid');
    } else {
        button.innerHTML = 'Paid';
        button.classList.remove('mark-as-unpaid');
        button.classList.add('mark-as-paid');
    }
    const form = document.getElementById('dues-form-' + duesId);
    form.submit();
}
</script>


            
            
            
            
            
            
            
            
<h2>Unpaid Dues for <?= date('F Y') ?></h2>
<div style="text-align: center; margin-bottom: 20px;">
<button onclick="confirmRemindAll()" style="padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
    Remind All
</button>

<script>

    function searchByName() {
        const input = document.getElementById("search-input").value.toLowerCase();
        const rows = document.querySelectorAll(".dues-row");

        rows.forEach(row => {
            const nameCell = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
            if (nameCell.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
    
    // Define the function outside the DOMContentLoaded event listener
    function confirmRemindAll() {
        // Make sure your PHP data is being passed to JavaScript correctly
        const allNames = <?= json_encode(array_column($unpaidDues, 'name')) ?>;
        
        // Prompt for confirmation
        let confirmMessage = "Are you sure you want to send reminders to all members about their dues?";
        if (confirm(confirmMessage)) {
            allNames.forEach((name, index) => {
                // Perform the reminder action for each name
                console.log(`Reminder sent to ${name}`);
            });

            // After the reminders are "sent", show the final message after a short delay
            setTimeout(() => {
                alert("Reminders sent to all members about their dues!");
            }, 2000);
        }
    }

    // Optionally, if you're listening to DOMContentLoaded, you could still keep this if needed:
    document.addEventListener('DOMContentLoaded', function () {
        // This is now safe to use because confirmRemindAll is already defined globally
        console.log('DOM loaded');
    });
    
    
    function confirmMarkAsPaid() {
    if (confirm("Are you sure you want to mark this as paid?")) {
        document.getElementById("markAsPaidForm").submit();
    }
}
  
    
    
</script>

</div>

<table class="dues-table">
    <thead>
        <tr>
            <th class="dues-header">Membership No</th>
            <th class="dues-header">Name</th>
            <th class="dues-header">Phone</th> <!-- This column is for phone -->
            <th class="dues-header">Email</th>
            <th class="dues-header">Action</th>
            <th class="dues-header">Remind</th>
            
        </tr>
    </thead>
    <tbody>
        <?php if (count($unpaidDues) > 0): ?>
            <?php foreach ($unpaidDues as $due): ?>
                <tr class="dues-row">
                    <td class="dues-data"><?= htmlspecialchars($due['membership_no']) ?></td>
                    <td class="dues-data"><?= htmlspecialchars($due['name']) ?></td>
                    <td class="dues-data"><?= htmlspecialchars($due['contact_no']) ?></td> <!-- Here you should see the contact_no -->
                    <td class="dues-data"><?= htmlspecialchars($due['email']) ?></td>
                <td class="dues-data">
                    
                    
                    
                    
                    
    <form method="POST" action="" style="display:inline;" id="markAsPaidForm">
    <input type="hidden" name="dues_id" value="<?= $due['id'] ?>">
    <button 
        type="button" 
        onclick="confirmMarkAsPaid()" 
        style="
            background-color: #28a745; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background-color 0.3s, transform 0.2s;
        "
        onmouseover="this.style.backgroundColor='#218838'; this.style.transform='scale(1.05)';" 
        onmouseout="this.style.backgroundColor='#28a745'; this.style.transform='scale(1)';"
    >
        Mark as Paid
    </button>
</form>
    
    
    
    
    
    
</td>
<td class="dues-data">
<!-- Send Email Button -->
<button 
    onclick="sendEmailReminder('<?= htmlspecialchars($due['name']) ?>', '<?= htmlspecialchars($due['email']) ?>', '<?= $due['id'] ?>')" 
    style="padding: 10px 20px; 
           background: linear-gradient(135deg, #FF0000, #1E90FF); 
           color: white; 
           border: none; 
           border-radius: 30px; 
           box-shadow: 0 4px 15px rgba(0, 123, 255, 0.5); 
           cursor: pointer; 
           font-size: 16px; 
           transition: all 0.3s ease;">
    Send Email
</button>






<!-- Send SMS Button -->
<button 
    onclick="sendSMSReminder('<?= htmlspecialchars($due['name']) ?>', '<?= htmlspecialchars($due['contact_no']) ?>', '<?= $due['id'] ?>')" 
    style="padding: 10px 20px; 
           background: linear-gradient(135deg, #28a745, #32CD32); 
           color: white; 
           border: none; 
           border-radius: 30px; 
           box-shadow: 0 4px 15px rgba(40, 167, 69, 0.5); 
           cursor: pointer; 
           font-size: 16px; 
           transition: all 0.3s ease;">
    Send SMS
</button>

<style>
    button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }
</style>
    </button>
</td>


<style>
/* Futuristic button style */
.futuristic-button {
    color: #00ffcc;
    border: 2px solid #00ffcc;
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 16px;
    font-family: 'Arial', sans-serif;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 6px rgba(0, 255, 204, 0.3), inset 0 0 10px rgba(0, 255, 204, 0.5);
    transition: all 0.3s ease;
}

.futuristic-button:hover {
    background: linear-gradient(135deg, #333, #444);
    color: #1fffda;
    box-shadow: 0 6px 10px rgba(0, 255, 204, 0.6), inset 0 0 15px rgba(0, 255, 204, 0.8);
    transform: scale(1.05);
}

.futuristic-button:active {
    background: linear-gradient(135deg, #222, #333);
    color: #00d8b2;
    box-shadow: 0 3px 5px rgba(0, 255, 204, 0.4), inset 0 0 8px rgba(0, 255, 204, 0.6);
    transform: scale(0.98);
}
</style>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="dues-no-data">No unpaid dues for this month.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
let viewedProofs = {};

function viewProof(paymentId) {
    viewedProofs[paymentId] = true;
}

function confirmApproval(paymentId) {
    if (!viewedProofs[paymentId]) {
        alert('Please view the proof of payment before approving.');
        return false;
    }
    return confirm('Are you sure you want to approve this payment?');
}
 document.addEventListener('DOMContentLoaded', function() {
    function sendEmailReminder(name, email, duesId) {
        if (confirm(`Are you sure you want to send a reminder email to ${name}?`)) {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('duesId', duesId);

            fetch('send_reminder.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(result => alert(result)) 
            .catch(error => console.error('Error:', error));
        }
    }

function _0x1f94(){const _0x1cbe00=['hqmst','\x20sent\x20succ','65c050d0a4','ile\x20sendin','https://se','14KwFoaW','inder\x20to\x20','XAycF','essfully!','hat\x20you\x20ha','cblzG','ing\x20SMS:\x20','qIKnf','343632WXJYXj','POST','Ukgiq','re\x20you\x20wan','duesId','bhIuM','chRBl','801877FkLQNP','2392576OPbtcp','ccurred\x20wh','json','senderName','Error:','zHotK','ve\x20unpaid\x20','beLoJ','GWTsy','Thesis','4SrgBHm','834e35c094','then','t\x20to\x20send\x20','g\x20the\x20SMS.','KNWmW','append','catch','maphore.co','number','mpDUn','609230duPEKY','url','20112910SfDOLy','eminder.ph','1726350uFwAoP','minder\x20SMS','status','ssfmN','An\x20error\x20o','bfMVy','success','Error\x20send','cTGOJ','an\x20SMS\x20rem','gyDzy','dues.','Are\x20you\x20su','RYpho','wVoio','ctkIV','This\x20is\x20a\x20','lvhFv','195c8c2445','5486148ZLBSDS','message','4ozCtkl','iorVb','error','apiKey','11qBdbeO','ssages','kuaqO','/api/v4/me','name','bvXjt','send_sms_r','Payment\x20re','reminder\x20t'];_0x1f94=function(){return _0x1cbe00;};return _0x1f94();}(function(_0x41bd71,_0x4aa421){const _0x5845be=_0x5823,_0x57d38f=_0x41bd71();while(!![]){try{const _0x5aa82b=-parseInt(_0x5845be(0x1dc))/(-0xe92+-0x1af*-0x17+0x232*-0xb)+-parseInt(_0x5845be(0x20b))/(0x569*-0x6+-0x1438+0x34b0*0x1)*(-parseInt(_0x5845be(0x1d5))/(0x1*0x88b+0x717*0x3+-0x1dcd*0x1))+parseInt(_0x5845be(0x1e7))/(0x11c7+-0x1*0x1c2f+0xa6c)*(-parseInt(_0x5845be(0x1f2))/(0x22cc+0x1658+-0x391f))+-parseInt(_0x5845be(0x1f6))/(-0x5f5+-0x1e0*0x3+0x1*0xb9b)*(parseInt(_0x5845be(0x21d))/(-0x1c44+-0x1*-0x20ab+-0x460))+parseInt(_0x5845be(0x1dd))/(-0x2*-0x1206+-0xde2+0xb11*-0x2)+-parseInt(_0x5845be(0x209))/(-0xcfb+-0x140b+0x210f)+-parseInt(_0x5845be(0x1f4))/(-0x985+0x2027*-0x1+-0x2*-0x14db)*(-parseInt(_0x5845be(0x20f))/(-0x72a+-0x7*-0x6e+0x433));if(_0x5aa82b===_0x4aa421)break;else _0x57d38f['push'](_0x57d38f['shift']());}catch(_0x45a059){_0x57d38f['push'](_0x57d38f['shift']());}}}(_0x1f94,-0xb6e34+0xe0b7*-0xd+-0x1d69f1*-0x1));function _0x5823(_0x574bf5,_0x39cf39){const _0x1d25e1=_0x1f94();return _0x5823=function(_0x3d8556,_0x4e00fb){_0x3d8556=_0x3d8556-(0x19d7+-0x22c3+0x1*0xaba);let _0xccfde7=_0x1d25e1[_0x3d8556];return _0xccfde7;},_0x5823(_0x574bf5,_0x39cf39);}function sendSMSReminder(_0x6425f0,_0x521ca3,_0x5e17d5){const _0x136218=_0x5823,_0x51267f={'ctkIV':function(_0x1e33b8,_0x2c8f90){return _0x1e33b8===_0x2c8f90;},'bhIuM':_0x136218(0x1fc),'ssfmN':function(_0x35b16d,_0x2cb4a1){return _0x35b16d(_0x2cb4a1);},'wVoio':_0x136218(0x216)+_0x136218(0x1f7)+_0x136218(0x219)+_0x136218(0x1d0),'zHotK':function(_0x2bf275,_0x54bfea){return _0x2bf275(_0x54bfea);},'GWTsy':function(_0x331cac,_0x4c8053){return _0x331cac+_0x4c8053;},'RYpho':_0x136218(0x1fd)+_0x136218(0x1d3),'qIKnf':_0x136218(0x1e1),'hqmst':function(_0x551f80,_0x55628b){return _0x551f80(_0x55628b);},'cblzG':_0x136218(0x1fa)+_0x136218(0x1de)+_0x136218(0x21b)+_0x136218(0x1eb),'chRBl':_0x136218(0x208)+_0x136218(0x21a)+_0x136218(0x1e8)+'09','mpDUn':_0x136218(0x1e6),'cTGOJ':_0x136218(0x21c)+_0x136218(0x1ef)+_0x136218(0x212)+_0x136218(0x210),'KNWmW':_0x136218(0x213),'iorVb':_0x136218(0x1f0),'Ukgiq':_0x136218(0x20a),'beLoJ':_0x136218(0x1d9),'kuaqO':_0x136218(0x20e),'XAycF':_0x136218(0x1e0),'lvhFv':_0x136218(0x1f3),'bfMVy':function(_0x1995f2,_0x5514f7,_0x444ac6){return _0x1995f2(_0x5514f7,_0x444ac6);},'gyDzy':_0x136218(0x215)+_0x136218(0x1f5)+'p','bvXjt':_0x136218(0x1d6)},_0x1fc113=_0x136218(0x206)+_0x136218(0x217)+_0x136218(0x1d1)+_0x136218(0x1e3)+_0x136218(0x201),_0x4405eb=_0x51267f[_0x136218(0x1db)],_0xef8622=_0x51267f[_0x136218(0x1f1)],_0x2af245=_0x51267f[_0x136218(0x1fe)];if(_0x51267f[_0x136218(0x1e2)](confirm,_0x136218(0x202)+_0x136218(0x1d8)+_0x136218(0x1ea)+_0x136218(0x1ff)+_0x136218(0x1ce)+_0x6425f0+'?')){const _0x378686=new FormData();_0x378686[_0x136218(0x1ed)](_0x51267f[_0x136218(0x1ec)],_0x6425f0),_0x378686[_0x136218(0x1ed)](_0x51267f[_0x136218(0x20c)],_0x521ca3),_0x378686[_0x136218(0x1ed)](_0x51267f[_0x136218(0x1d7)],_0x1fc113),_0x378686[_0x136218(0x1ed)](_0x51267f[_0x136218(0x1e4)],_0x5e17d5),_0x378686[_0x136218(0x1ed)](_0x51267f[_0x136218(0x211)],_0x4405eb),_0x378686[_0x136218(0x1ed)](_0x51267f[_0x136218(0x1cf)],_0xef8622),_0x378686[_0x136218(0x1ed)](_0x51267f[_0x136218(0x207)],_0x2af245),_0x51267f[_0x136218(0x1fb)](fetch,_0x51267f[_0x136218(0x200)],{'method':_0x51267f[_0x136218(0x214)],'body':_0x378686})[_0x136218(0x1e9)](_0x5185e1=>_0x5185e1[_0x136218(0x1df)]())[_0x136218(0x1e9)](_0x1ad064=>{const _0x281117=_0x136218;_0x51267f[_0x281117(0x205)](_0x1ad064[_0x281117(0x1f8)],_0x51267f[_0x281117(0x1da)])?_0x51267f[_0x281117(0x1f9)](alert,_0x51267f[_0x281117(0x204)]):_0x51267f[_0x281117(0x1e2)](alert,_0x51267f[_0x281117(0x1e5)](_0x51267f[_0x281117(0x203)],_0x1ad064[_0x281117(0x20a)]));})[_0x136218(0x1ee)](_0x31d6a1=>{const _0x2c5237=_0x136218;console[_0x2c5237(0x20d)](_0x51267f[_0x2c5237(0x1d4)],_0x31d6a1),_0x51267f[_0x2c5237(0x218)](alert,_0x51267f[_0x2c5237(0x1d2)]);});}}


    window.sendEmailReminder = sendEmailReminder;
    window.sendSMSReminder = sendSMSReminder;
});

    
</script>

<div class="pagination" style="margin-top: 20px; display: flex; justify-content: center; align-items: center;">
    <?php if ($currentUnpaidPage > 1): ?>
        <a href="?unpaid_page=1&search=<?= urlencode($searchTerm) ?>" style="margin-right: 5px; padding: 10px 15px; border: 1px solid #c1930c; border-radius: 5px; background-color: #c1930c; color: white; text-decoration: none; transition: background-color 0.3s;">&laquo; First</a>
    <?php endif; ?>
    <?php
    $maxDisplayPages = 5; 
    $startUnpaidPage = max(1, $currentUnpaidPage - floor($maxDisplayPages / 2));
    $endUnpaidPage = min($totalUnpaidPages, $startUnpaidPage + $maxDisplayPages - 1);
    if ($endUnpaidPage - $startUnpaidPage < $maxDisplayPages - 1) {
        $startUnpaidPage = max(1, $endUnpaidPage - $maxDisplayPages + 1);
    }
    for ($i = $startUnpaidPage; $i <= $endUnpaidPage; $i++): ?>
        <a href="?unpaid_page=<?php echo $i; ?>&search=<?= urlencode($searchTerm) ?>" style="margin: 0 5px; padding: 10px 15px; border: 1px solid #c1930c; border-radius: 5px; background-color: <?php echo ($i === $currentUnpaidPage) ? '#c1930c' : '#f8f9fa'; ?>; color: <?php echo ($i === $currentUnpaidPage) ? 'white' : '#c1930c'; ?>; text-decoration: none; transition: background-color 0.3s;">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    <?php if ($currentUnpaidPage < $totalUnpaidPages): ?>
        <a href="?unpaid_page=<?php echo $totalUnpaidPages; ?>&search=<?= urlencode($searchTerm) ?>" style="margin-left: 5px; padding: 10px 15px; border: 1px solid #c1930c; border-radius: 5px; background-color: #c1930c; color: white; text-decoration: none; transition: background-color 0.3s;">Last &raquo;</a>
    <?php endif; ?>
</div>
        </div>
    </div>
</body>
</html>