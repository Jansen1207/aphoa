<?php

require('config.php');
session_start();
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;
$search = isset($_POST['search']) ? $_POST['search'] : '';
$sql = "SELECT m.id, m.membership_no, i.first_name, i.last_name, i.middle_name, 
               i.contact_no, i.age, i.email_address, i.occupation, i.address, 
               i.educ_attainment, i.birthdate, i.sex, i.civil_status, 
               i.homeowner_status, i.dos_status, i.length_of_stay, i.active 
        FROM members m
        INNER JOIN information i ON m.id = i.member_id";
if (!empty($search)) {
    $sql .= " WHERE i.first_name LIKE '%$search%' OR i.last_name LIKE '%$search%'";
}
$sql .= " LIMIT $start_from, $results_per_page";
$result = $conn->query($sql);
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}
$total_sql = "SELECT COUNT(*) as total FROM members m 
               INNER JOIN information i ON m.id = i.member_id";
if (!empty($search)) {
    $total_sql .= " WHERE i.first_name LIKE '%$search%' OR i.last_name LIKE '%$search%'";
}
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_members = $total_row['total'];
$total_pages = ceil($total_members / $results_per_page);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $status = $_POST['status'] == 'Active' ? 1 : 0;
    $updateSql = "UPDATE information SET active = ? WHERE member_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param('ii', $status, $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=$page&search=" . urlencode($search));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];

    // First, delete related dues records
    $deleteDuesSql = "DELETE FROM dues WHERE member_id = ?";
    $stmt = $conn->prepare($deleteDuesSql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Next, delete related occupants records
    $deleteOccupantsSql = "DELETE FROM occupants WHERE member_id = ?";
    $stmt = $conn->prepare($deleteOccupantsSql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Next, delete related information records
    $deleteInfoSql = "DELETE FROM information WHERE member_id = ?";
    $stmt = $conn->prepare($deleteInfoSql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Now delete the member
    $deleteSql = "DELETE FROM members WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Set success message
    $_SESSION['success_message'] = 'Member and all related records deleted successfully.';

    header("Location: " . $_SERVER['PHP_SELF'] . "?page=$page&search=" . urlencode($search));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email_address'];
    $contactNo = $_POST['contact_no'];
    $age = $_POST['age'];
    $occupation = $_POST['occupation'];
    $address = $_POST['address'];
    $homeownerStatus = $_POST['homeowner_status'];
    $dosStatus = $_POST['dos_status'];
    $lengthOfStay = $_POST['length_of_stay'];
    $sex = $_POST['sex'];
    $editSql = "UPDATE information SET 
                    first_name = ?, 
                    last_name = ?, 
                    email_address = ?, 
                    contact_no = ?,
                    age = ?,
                    occupation = ?, 
                    address = ?, 
                    homeowner_status = ?, 
                    dos_status = ?, 
                    length_of_stay = ?, 
                    sex = ? 
                WHERE member_id = ?";
    $stmt = $conn->prepare($editSql);
    $stmt->bind_param('ssssssssssi', $firstName, $lastName, $email, $contactNo, $occupation, $address, $homeownerStatus, $dosStatus, $lengthOfStay, $sex, $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=$page&search=" . urlencode($search));
    exit;
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
        /* Button Styling */
        .member-actions .btn-add-member {
            background: linear-gradient(120deg, #007bff, #0056b3);
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .member-actions .btn-add-member:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        /* Pagination Styling */
        .pagination {
            text-align: center;
            margin: 20px 0;
        }

        .pagination a {
            padding: 10px 20px;
            margin: 0 5px;
            text-decoration: none;
            color: #ffffff;
            border-radius: 30px;
            background: linear-gradient(120deg, #6c757d, #495057);
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .pagination a.active {
            background: linear-gradient(120deg, #28a745, #1e7e34);
            color: #ffffff;
        }

        .pagination a:hover:not(.active) {
            transform: scale(1.1);
            background: linear-gradient(120deg, #007bff, #0056b3);
        }

        /* Table Styling */
        .member-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: linear-gradient(120deg, #2c3e50, #4ca1af);
            color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .member-table th,
        .member-table td {
            padding: 15px;
            text-align: left;
        }

        .member-table th {
            background-color: #34495e;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .member-table tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .member-table tr:nth-child(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .member-table tr:hover {
            background-color: rgba(0, 255, 0, 0.1);
            transform: scale(1.02);
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        /* Charts Section Styling */
        .charts-wrapper {
            margin-top: 40px;
            padding: 20px;
            background: linear-gradient(120deg, #34495e, #2c3e50);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .chart-section {
            margin: 20px 0;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* New Styling for Status and Name */
        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-inactive {
            color: red;
            font-weight: bold;
        }

        .name-bold {
            font-weight: bold;
        }
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 14px;
        margin: 20px 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }

    .styled-table thead tr {
        background-color: #009879;
        color: #ffffff;
        text-align: left;
    }

    .styled-table th, .styled-table td {
        padding: 10px 15px;
        border: 3px solid #dddddd;
    }

    .styled-table tbody tr {
        background-color: #f9f9f9;
    }
    
    .styled-table tbody tr:hover {
        background-color: #f1f1f1;
        cursor: pointer;
    }

    .styled-table tbody tr.active-row {
        font-weight: bold;
        color: #009879;
    }
</style>
</head>
<body>
<div class="header">
    <table>
        <tr>
            <td>
                <h1 style="margin-bottom:1px;margin-top:1px;">Accounts</h1>
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
        <h2>Accounts Management</h2>
        <form method="POST" action="" style="margin-bottom: 20px;">
            <label for="searchInput">Search:</label>
            <input type="text" id="searchInput" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">
            <input type="submit" value="Search" style="padding: 8px 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
        </form>
        <table class="styled-table">
            <thead>
<tr>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Name</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Membership No</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Contact No</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Age</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Email</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Occupation</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Address</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Homeowner Status</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">DOS Status</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Length of Stay</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Status</th>
    <th style="padding: 12px; text-align: left; border: 5px solid #ddd; background-color: #f2f2f2; color: black;">Actions</th>
</tr>
            </thead>
    <tbody id="memberTableBody">
        <?php foreach ($members as $member): ?>
        <tr class="member-item" data-id="<?php echo $member['id']; ?>">
            <td class="member-name name-bold"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
            <td class="member-membership"><?php echo htmlspecialchars($member['membership_no']); ?></td>
            <td class="member-contact"><?php echo htmlspecialchars($member['contact_no']); ?></td>
            <td class="member-age"><?php echo htmlspecialchars($member['age']); ?></td>
            <td class="member-email"><?php echo htmlspecialchars($member['email_address']); ?></td>
            <td class="member-occupation"><?php echo htmlspecialchars($member['occupation']); ?></td>
            <td class="member-address"><?php echo htmlspecialchars($member['address']); ?></td>
            <td class="member-homeowner"><?php echo htmlspecialchars($member['homeowner_status']); ?></td>
            <td class="member-dos"><?php echo htmlspecialchars($member['dos_status']); ?></td>
            <td class="member-length"><?php echo htmlspecialchars($member['length_of_stay']); ?></td>
            <td class="member-status">
                <span class="<?php echo $member['active'] ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo $member['active'] ? 'Active' : 'Inactive'; ?>
                </span>
            </td>
            <td class="member-actions">
                <button type="button" class="edit-button">Edit</button>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                    <select name="status" required>
                        <option value="Active" <?php echo $member['active'] ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo !$member['active'] ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                    <button type="submit" name="update">Update</button>
                </form>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                    <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this member?');">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
        </table>
        <div class="pagination">
            <?php if ($total_members > 0): ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo $i == $page ? '
active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            <?php else: ?>
                <span>No members found.</span>
            <?php endif; ?>
        </div>
        <div id="editModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background:white; margin:15% auto; padding:20px; border:1px solid #888; width:80%;">
        <span id="closeModal" style="color:red; float:right; cursor:pointer;">&times;</span>
        <h2>Edit Member Information</h2>
        <form id="editForm" method="POST" action="">
            <input type="hidden" name="id" id="memberId">
            <label for="firstName">First Name:</label>
            <input type="text" name="first_name" id="firstName" required><br>
            <label for="lastName">Last Name:</label>
            <input type="text" name="last_name" id="lastName" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email_address" id="email" required><br>
            <label for="contactNo">Contact No:</label>
            <input type="text" name="contact_no" id="contactNo" required><br>
            <label for="age">Age:</label>
            <input type="text" name="age" id="age" required><br>
            <label for="occupation">Occupation:</label>
            <input type="text" name="occupation" id="occupation" required><br>
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" required><br>
            <label for="homeownerStatus">Homeowner Status:</label>
            <input type="text" name="homeowner_status" id="homeownerStatus" required><br>
            <label for="dosStatus">DOS Status:</label>
            <input type="text" name="dos_status" id="dosStatus" required><br>
            <label for="lengthOfStay">Length of Stay:</label>
            <input type="text" name="length_of_stay" id="lengthOfStay" required><br>
            <label for="sex">Sex:</label>
            <select name="sex" id="sex" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select><br>
            <button type="submit" name="edit">Save Changes</button>
        </form>
    </div>
</div>
    </div>
</div>
<script>
document.querySelectorAll('.edit-button').forEach(button => {
    button.onclick = function() {
        const row = this.closest('.member-item');
        const memberId = row.dataset.id;
        const firstName = row.querySelector('.member-name').innerText.split(' ')[0];
        const lastName = row.querySelector('.member-name').innerText.split(' ')[1];
        const email = row.querySelector('.member-email').innerText;
        const contactNo = row.querySelector('.member-contact').innerText;
        const age = row.querySelector('.member-age').innerText;
        const occupation = row.querySelector('.member-occupation').innerText;
        const address = row.querySelector('.member-address').innerText;
        const homeownerStatus = row.querySelector('.member-homeowner').innerText;
        const dosStatus = row.querySelector('.member-dos').innerText;
        const lengthOfStay = row.querySelector('.member-length').innerText;
        
        document.getElementById('memberId').value = memberId;
        document.getElementById('firstName').value = firstName;
        document.getElementById('lastName').value = lastName;
        document.getElementById('email').value = email;
        document.getElementById('contactNo').value = contactNo;
        document.getElementById('age').value = age;
        document.getElementById('occupation').value = occupation;
        document.getElementById('address').value = address;
        document.getElementById('homeownerStatus').value = homeownerStatus;
        document.getElementById('dosStatus').value = dosStatus;
        document.getElementById('lengthOfStay').value = lengthOfStay;
        
        document.getElementById('editModal').style.display = 'block';
    };
});

document.getElementById('closeModal').onclick = function() {
    document.getElementById('editModal').style.display = 'none';
};
</script>
</body>
</html>