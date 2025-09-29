<?php
require('config.php');
session_start();
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM members");
$totalMembers = $totalResult->fetch_assoc()['total'];
$membersPerPage = 5; 
$totalPages = ceil($totalMembers / $membersPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages)); 
$offset = ($currentPage - 1) * $membersPerPage;
$sql = "SELECT m.*, i.* FROM members m 
        LEFT JOIN information i ON m.id = i.member_id 
        LIMIT $offset, $membersPerPage";
$result = $conn->query($sql);
$membersData = [];
while ($row = $result->fetch_assoc()) {
    $membersData[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/member_management.css">
    <style>
#edit-form {
    width: 100%;
    max-width: 600px;
    margin: 20px auto;
}
table {
    width: 100%;
    border-collapse: collapse;
}
td {
    padding: 10px;
    vertical-align: middle;
}
label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}
input[type="text"],
input[type="email"],
input[type="date"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}
@media (max-width: 600px) {
    td {
        display: block;
        width: 100%;
    }
}
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
            }
        }
    </style>
    <style>
        .member-container {
            padding: 20px;
            height: 400vh;
        }
        .member-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        .member-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px;
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
            text-align: center;
        }
        .member-card:hover {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .member-card h3 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .member-card p {
            margin: 5px 0;
        }
        .btn-view-profile, .btn-deactivate {
            margin: 5px;
        }
        @media (max-width: 768px) {
            .member-card {
                width: calc(50% - 30px);
            }
        }
        @media (max-width: 480px) {
            .member-card {
                width: calc(100% - 30px);
            }
        }
    </style>
    <style>
        .header table {
            width: 100%;
            table-layout: fixed;
        }
        .header td {
            padding: 10px;
        }
        .search-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .search-bar input {
            flex: 1;
            min-width: 200px;
        }
        .search-bar button {
            flex-shrink: 0;
        }
        .btn-active {
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
    background-color: #f44336;
}
.btn-inactive {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
}
    .status-active {
        color: green;
        font-weight: bold;
    }
    .status-inactive {
        color: red;
        font-weight: bold;
    }
    </style>
</head>
<body>
<div class="header">
    <table>
        <tr>
            <td>
                <h1 style="margin-bottom:1px;margin-top:1px;">Member Management</h1>
            </td>
            <td align="right">
                <img src="./images/menicon.png" width="50" height="50" style="border-radius: 50%;">
            </td>
            <td width="120">
                &nbsp;&nbsp;&nbsp;
                <form action="logout.php" method="POST">
                    <select name="logout" onchange="this.form.submit()">
                    <option>Logout</option>
                    <option style="background-color: #337AB7;color:#fff;" value="logout">Logout</option>
                    </select>
                </form>
            </td>
        </tr>
    </table>
</div>
<?php include './includes/officer_sidebar.php'; ?>
<div class="member-container">
    <h2>Manage Members</h2>
    <div class="search-bar">
        <input type="text" id="search-name" placeholder="Search by Name, Membership ID, Phone, Address, etc..." />
        <button class="btn-search" onclick="searchMembers()">Search</button>
    </div>
    <div class="search-results" style="margin-top: 20px; display: none;" id="search-results-container">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Membership ID</th>
                <th>Address</th>
                <th>Contact No</th>
                <th>Age</th>
                <th>Homeowner Status</th>
                <th>Deed of Sale Status</th>
                <th>Length of Stay (Years)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="search-results-body">
            <!-- Search results will be inserted here -->
        </tbody>
    </table>
</div>
    <div class="member-actions">
        <button class="btn-add-member" onclick="window.location.href='terms.php';">Add New Member</button>
    </div>
    <!-- Member Table -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <thead>
        <tr>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Name</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Status</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Membership ID</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Address</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Contact No</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Age</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Homeowner Status</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Deed of Sale Status</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Length of Stay (Years)</th>
            <th style="background-color: #c1930c; color: white; padding: 10px; border: 1px solid #ddd; text-align: left;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($membersData)): ?>
            <?php foreach ($membersData as $memberData): ?>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($memberData['first_name'] . ' ' . $memberData['middle_name'] . ' ' . $memberData['last_name']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;" class="<?php echo htmlspecialchars($memberData['active']) ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo htmlspecialchars($memberData['active']) ? 'Active' : 'Inactive'; ?>
                    </td>                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($memberData['membership_no']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($memberData['address']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($memberData['contact_no']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($memberData['age']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($memberData['homeowner_status']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($memberData['dos_status']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($memberData['length_of_stay']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <button class="btn-view-profile" onclick="openEditModal('<?php echo htmlspecialchars($memberData['member_id']); ?>')">Edit / View Profile</button>
                        <button id="status-btn-<?php echo htmlspecialchars($memberData['member_id']); ?>"
                                class="<?php echo htmlspecialchars($memberData['active']) ? 'btn-active' : 'btn-inactive'; ?>"
                                onclick="toggleStatus('<?php echo htmlspecialchars($memberData['member_id']); ?>', <?php echo htmlspecialchars($memberData['active']); ?>)">
                            <?php echo htmlspecialchars($memberData['active']) ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align: center;">No members found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
    <!-- Pagination Controls -->
<!-- Pagination Controls -->
<div class="pagination" style="margin-top: 20px; display: flex; justify-content: center; align-items: center;">
    <?php if ($currentPage > 1): ?>
        <a href="?page=1" style="margin-right: 5px; padding: 10px 15px; border: 1px solid #c1930c; border-radius: 5px; background-color: #c1930c; color: white; text-decoration: none; transition: background-color 0.3s;">&laquo; First</a>
    <?php endif; ?>
    <?php
    $maxDisplayPages = 5; 
    $startPage = max(1, $currentPage - floor($maxDisplayPages / 2));
    $endPage = min($totalPages, $startPage + $maxDisplayPages - 1);
    if ($endPage - $startPage < $maxDisplayPages - 1) {
        $startPage = max(1, $endPage - $maxDisplayPages + 1);
    }
    for ($i = $startPage; $i <= $endPage; $i++): ?>
        <a href="?page=<?php echo $i; ?>" style="margin: 0 5px; padding: 10px 15px; border: 1px solid #c1930c; border-radius: 5px; background-color: <?php echo ($i === $currentPage) ? '#c1930c' : '#f8f9fa'; ?>; color: <?php echo ($i === $currentPage) ? 'white' : '#c1930c'; ?>; text-decoration: none; transition: background-color 0.3s;">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    <?php if ($currentPage < $totalPages): ?>
        <a href="?page=<?php echo $totalPages; ?>" style="margin-left: 5px; padding: 10px 15px; border: 1px solid #c1930c; border-radius: 5px; background-color: #c1930c; color: white; text-decoration: none; transition: background-color 0.3s;">Last &raquo;</a>
    <?php endif; ?>
</div>
<!-- Modal -->
<div id="memberModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modal-body">
            <!-- Member details will be loaded here -->
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editMemberModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <div id="edit-modal-body">
        <form id="edit-form">
    <input type="hidden" name="id" value="${memberData.member_id}">
    <label>First Name:</label><input type="text" name="first_name" value="${memberData.first_name}">
    <label>Last Name:</label><input type="text" name="last_name" value="${memberData.last_name}">
    <!-- Add other fields as needed -->
    <button type="submit">Save Changes</button>
</form>
        </div>
    </div>
</div>
<script>
function openEditModal(memberId) {
    var modal = document.getElementById("editMemberModal");
    var modalBody = document.getElementById("edit-modal-body");
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "get_member_info.php?id=" + memberId, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                var memberData = JSON.parse(xhr.responseText);
                modalBody.innerHTML = `
<form id="edit-form">
    <input type="hidden" name="id" value="${memberData.id}">
    <table>
        <tr>
            <td><label for="first_name">First Name:</label></td>
            <td><input type="text" name="first_name" id="first_name" value="${memberData.first_name}" required></td>
        </tr>
        <tr>
            <td><label for="last_name">Last Name:</label></td>
            <td><input type="text" name="last_name" id="last_name" value="${memberData.last_name}" required></td>
        </tr>
        <tr>
            <td><label for="middle_name">Middle Name:</label></td>
            <td><input type="text" name="middle_name" id="middle_name" value="${memberData.middle_name}"></td>
        </tr>
        <tr>
            <td><label for="contact_no">Contact No:</label></td>
            <td><input type="text" name="contact_no" id="contact_no" value="${memberData.contact_no}" required></td>
        </tr>
        <tr>
            <td><label for="age">Age:</label></td>
            <td><input type="number" name="age" id="age" value="${memberData.age}" required></td>
        </tr>
        <tr>
            <td><label for="email_address">Email Address:</label></td>
            <td><input type="email" name="email_address" id="email_address" value="${memberData.email_address}" required></td>
        </tr>
        <tr>
            <td><label for="occupation">Occupation:</label></td>
            <td><input type="text" name="occupation" id="occupation" value="${memberData.occupation}"></td>
        </tr>
        <tr>
            <td><label for="address">Address:</label></td>
            <td>
<select name="address" id="address" required>
    <option value="">Select a street</option>
    <option value="Oriole St" ${memberData.address === 'Oriole St' ? 'selected' : ''}>Oriole St</option>
    <option value="Lark St" ${memberData.address === 'Lark St' ? 'selected' : ''}>Lark St</option>
    <option value="BlackBird St" ${memberData.address === 'BlackBird St' ? 'selected' : ''}>BlackBird St</option>
    <option value="Seagull St" ${memberData.address === 'Seagull St' ? 'selected' : ''}>Seagull St</option>
    <option value="Kingbird St" ${memberData.address === 'Kingbird St' ? 'selected' : ''}>Kingbird St</option>
    <option value="Hornbill St" ${memberData.address === 'Hornbill St' ? 'selected' : ''}>Hornbill St</option>
    <option value="Flamingo St" ${memberData.address === 'Flamingo St' ? 'selected' : ''}>Flamingo St</option>
    <option value="Eagle St" ${memberData.address === 'Eagle St' ? 'selected' : ''}>Eagle St</option>
    <option value="Heron St" ${memberData.address === 'Heron St' ? 'selected' : ''}>Heron St</option>
    <option value="Woodpecker St" ${memberData.address === 'Woodpecker St' ? 'selected' : ''}>Woodpecker St</option>
    <option value="Bluejay St" ${memberData.address === 'Bluejay St' ? 'selected' : ''}>Bluejay St</option>
    <option value="Robin St" ${memberData.address === 'Robin St' ? 'selected' : ''}>Robin St</option>
    <option value="Lovebird St" ${memberData.address === 'Lovebird St' ? 'selected' : ''}>Lovebird St</option>
    <option value="Pelican St" ${memberData.address === 'Pelican St' ? 'selected' : ''}>Pelican St</option>
    <option value="Roadrunner St" ${memberData.address === 'Roadrunner St' ? 'selected' : ''}>Roadrunner St</option>
    <option value="Cardinal St" ${memberData.address === 'Cardinal St' ? 'selected' : ''}>Cardinal St</option>
    <option value="Yellowbird St" ${memberData.address === 'Yellowbird St' ? 'selected' : ''}>Yellowbird St</option>
    <option value="Pintail St" ${memberData.address === 'Pintail St' ? 'selected' : ''}>Pintail St</option>
    <option value="Woodcock St" ${memberData.address === 'Woodcock St' ? 'selected' : ''}>Woodcock St</option>
    <option value="Sparrow St" ${memberData.address === 'Sparrow St' ? 'selected' : ''}>Sparrow St</option>
    <option value="Quail St" ${memberData.address === 'Quail St' ? 'selected' : ''}>Quail St</option>
    <option value="Golden Plover St" ${memberData.address === 'Golden Plover St' ? 'selected' : ''}>Golden Plover St</option>
    <option value="Skylark St" ${memberData.address === 'Skylark St' ? 'selected' : ''}>Skylark St</option>
    <option value="Hummingbird St" ${memberData.address === 'Hummingbird St' ? 'selected' : ''}>Hummingbird St</option>
    <option value="Nighthawk St" ${memberData.address === 'Nighthawk St' ? 'selected' : ''}>Nighthawk St</option>
    <option value="Swan St" ${memberData.address === 'Swan St' ? 'selected' : ''}>Swan St</option>
    <option value="Phoenix St" ${memberData.address === 'Phoenix St' ? 'selected' : ''}>Phoenix St</option>
</select>
            </td>
        </tr>
        <tr>
            <td><label for="educ_attainment">Educational Attainment:</label></td>
            <td><input type="text" name="educ_attainment" id="educ_attainment" value="${memberData.educ_attainment}"></td>
        </tr>
        <tr>
            <td><label for="birthdate">Birthdate:</label></td>
            <td><input type="date" name="birthdate" id="birthdate" value="${memberData.birthdate}" required></td>
        </tr>
<tr>
    <td><label for="sex">Sex:</label></td>
    <td>
        <select name="sex" id="sex" required>
            <option value="">Select</option>
            <option value="female" ${memberData.sex === 'female' ? 'selected' : ''}>Female</option>
            <option value="male" ${memberData.sex === 'male' ? 'selected' : ''}>Male</option>
        </select>
    </td>
</tr>
<tr>
    <td><label for="civil_status">Civil Status:</label></td>
    <td>
        <select name="civil_status" id="civil_status" required>
            <option value="">Select</option>
            <option value="single" ${memberData.civil_status === 'single' ? 'selected' : ''}>Single</option>
            <option value="married" ${memberData.civil_status === 'married' ? 'selected' : ''}>Married</option>
            <option value="separated" ${memberData.civil_status === 'separated' ? 'selected' : ''}>Separated</option>
            <option value="widow(er)" ${memberData.civil_status === 'widow(er)' ? 'selected' : ''}>Widow(er)</option>
        </select>
    </td>
</tr>
<tr>
    <td><label for="homeowner_status">Homeowner Status:</label></td>
    <td>
        <select name="homeowner_status" id="homeowner_status" required>
            <option value="">Select</option>
            <option value="sharer" ${memberData.homeowner_status === 'sharer' ? 'selected' : ''}>Sharer</option>
            <option value="owner" ${memberData.homeowner_status === 'owner' ? 'selected' : ''}>Owner</option>
        </select>
    </td>
</tr>
<tr>
    <td><label for="dos_status">Deed of Sale Status:</label></td>
    <td>
        <select name="dos_status" id="dos_status" required>
            <option value="">Select</option>
            <option value="with" ${memberData.dos_status === 'with' ? 'selected' : ''}>With</option>
            <option value="without" ${memberData.dos_status === 'without' ? 'selected' : ''}>Without</option>
        </select>
    </td>
</tr>
        <tr>
            <td><label for="length_of_stay">Length of Stay (years):</label></td>
            <td>
                <input type="number" name="length_of_stay" id="length_of_stay" value="${memberData.length_of_stay}">
                <p style="font-size: 9px">Number only in years.</p>
            </td>
        </tr>
        <tr>
            <td><label for="owner_name">Owner Name:</label></td>
            <td><input type="text" name="owner_name" id="owner_name" value="${memberData.owner_name}"></td>
        </tr>
        <tr>
            <td colspan="2"><button type="submit" style="background:#007bff">Save Changes</button></td>
        </tr>
    </table>
   <h4>Occupants</h4>
    <table id="occupants-table">
        <tr><th>Name</th><th>Age</th><th>Relationship</th><th>Actions</th></tr>
        ${memberData.occupants.map(occupant => `
            <tr>
                <td>${occupant.name}</td>
                <td>${occupant.age}</td>
                <td>${occupant.relationship}</td>
                <td>
<button type="button" style="background:#007bff"  onclick="editOccupant(${occupant.occupant_id})">Edit</button>
<button type="button" style="background:#f44336"  onclick="deleteOccupant(${occupant.occupant_id})">Delete</button>
                </td>
            </tr>`).join('')}
    </table>
    <button   style="margin-top: 30px" type="button"  onclick="addOccupant(${memberData.id})">Add Occupant</button>
    <tr><td colspan="2"><button type="submit" style="background:#007bff" >Save Changes</button></td></tr>
</form>
                `;
                document.getElementById('edit-form').addEventListener('submit', function(event) {
                    event.preventDefault(); 
                    var formData = new FormData(this); 
                    var updateXhr = new XMLHttpRequest();
                    updateXhr.open("POST", "update_member.php", true);
                    updateXhr.onload = function() {
                        if (updateXhr.status === 200) {
                            var response = JSON.parse(updateXhr.responseText);
                            if (response.success) {
                                alert('Profile updated successfully!');
                                modal.style.display = "none"; 
                                location.reload(); 
                            } else {
                                alert('Error updating profile: ' + response.error);
                            }
                        }
                    };
                    updateXhr.send(formData); 
                });
                modal.style.display = "block"; 
            } catch (e) {
                console.error('Error parsing JSON response:', e);
            }
        }
    };
    xhr.send();
}
function editOccupant(occupantId) {
    var newName = prompt('Enter new name:');
    var newAge = prompt('Enter new age:');
    var newRelationship = prompt('Enter new relationship:');
    if (newName !== null && newAge !== null && newRelationship !== null) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_occupant.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        var params = `occupant_id=${encodeURIComponent(occupantId)}&name=${encodeURIComponent(newName)}&age=${encodeURIComponent(newAge)}&relationship=${encodeURIComponent(newRelationship)}`;
        xhr.onload = function() {
    console.log(xhr.responseText); 
    console.log('Sending:', {
    occupant_id: occupantId,
    name: newName,
    age: newAge,
    relationship: newRelationship
});
    if (xhr.status === 200) {
        var response = JSON.parse(xhr.responseText);
        if (response.success) {
            alert('Occupant updated successfully! Refresh to see changes.');
        } else {
            alert('Error updating occupant: ' + response.error);
        }
    } else {
        alert('Failed to update occupant. Status: ' + xhr.status);
    }
};
        xhr.send(params); 
    } else {
        alert('Update canceled or invalid input.');
    }
}
function deleteOccupant(occupantId) {
    console.log("Attempting to delete occupant with ID:", occupantId); 
    if (confirm('Are you sure you want to delete this occupant?')) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_occupant_member.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                console.log(response); 
                if (response.success) {
                    alert('Occupant deleted successfully! Refresh to see changes.'); 
                } else {
                    alert('Error deleting occupant: ' + response.error); 
                }
            } else {
                alert('Request failed. Status: ' + xhr.status); 
            }
        };
        xhr.send("occupant_id=" + occupantId); 
    }
}
function addOccupant(memberId) {
    var name = prompt('Enter occupant name:');
    var age = prompt('Enter occupant age:');
    var relationship = prompt('Enter occupant relationship:');
    if (name && age && relationship) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "add_occupant_member.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        var params = `member_id=${encodeURIComponent(memberId)}&name=${encodeURIComponent(name)}&age=${encodeURIComponent(age)}&relationship=${encodeURIComponent(relationship)}`;
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('Occupant added successfully!');
                    location.reload(); 
                } else {
                    alert('Error adding occupant: ' + response.error);
                }
            } else {
                alert('Failed to add occupant. Status: ' + xhr.status);
            }
        };
        xhr.send(params); 
    } else {
        alert('All fields are required.');
    }
}
function closeEditModal() {
    var modal = document.getElementById("editMemberModal");
    modal.style.display = "none";
}
document.addEventListener('submit', function(event) {
    if (event.target.id === 'edit-form') {
        event.preventDefault();
        var formData = new FormData(event.target);
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_member.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                location.reload(); 
            }
        };
        xhr.send(formData);
    }
});
function searchMembers() {
    var searchId = document.getElementById('search-name').value;
    var xhr = new XMLHttpRequest();
    var url = 'search_members.php?search_id=' + encodeURIComponent(searchId);
    xhr.open('GET', url, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log(xhr.responseText); 
            try {
                var membersData = JSON.parse(xhr.responseText);
                var searchResultsBody = document.getElementById('search-results-body'); 
                var searchResultsContainer = document.getElementById('search-results-container'); 
                searchResultsBody.innerHTML = ''; 
                if (membersData.length > 0) {
                    searchResultsContainer.style.display = 'block'; 
                    membersData.forEach(function(memberData) {
                        var memberRow = document.createElement('tr');
                        memberRow.innerHTML = `
                            <td>${memberData.first_name} ${memberData.middle_name || ''} ${memberData.last_name}</td>
                            <td class="${memberData.active ? 'status-active' : 'status-inactive'}">${memberData.active ? 'Active' : 'Inactive'}</td>
                            <td>${memberData.membership_no}</td>
                            <td>${memberData.address}</td>
                            <td>${memberData.contact_no}</td>
                            <td>${memberData.age}</td>
                            <td>${memberData.homeowner_status}</td>
                            <td>${memberData.dos_status}</td>
                            <td>${memberData.length_of_stay}</td>
                            <td>
                                <button class="btn-view-profile" onclick="openEditModal('${memberData.member_id}')">Edit / View Profile</button>
                                <button id="status-btn-${memberData.member_id}" class="${memberData.active ? 'btn-active' : 'btn-inactive'}" onclick="toggleStatus('${memberData.member_id}', ${memberData.active})">${memberData.active ? 'Deactivate' : 'Activate'}</button>
                            </td>
                        `;
                        searchResultsBody.appendChild(memberRow);
                    });
                } else {
                    searchResultsContainer.style.display = 'none'; 
                    searchResultsBody.innerHTML = '<tr><td colspan="9" style="text-align: center;">No members found.</td></tr>';
                }
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                searchResultsBody.innerHTML = '<tr><td colspan="9" style="text-align: center;">Error parsing search results.</td></tr>';
                document.getElementById('search-results-container').style.display = 'none'; 
            }
        } else {
            console.error('Failed to fetch search results. Status:', xhr.status);
        }
    };
    xhr.send();
}
    function openModal(memberId) {
        var modal = document.getElementById("memberModal");
        var modalBody = document.getElementById("modal-body");
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_member_info.php?id=" + memberId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                modalBody.innerHTML = xhr.responseText;
                modal.style.display = "block";
            }
        };
        xhr.send();
    }
    function closeModal() {
        var modal = document.getElementById("memberModal");
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        var modal = document.getElementById("memberModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    function toggleStatus(memberId, currentStatus) {
        var newStatus = currentStatus ? 0 : 1; 
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "toggle_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                location.reload();
            }
        };
        xhr.send("id=" + encodeURIComponent(memberId) + "&status=" + encodeURIComponent(newStatus));
    }
</script>
</body>
</html>