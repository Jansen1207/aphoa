<?php
require('config.php');
session_start();


$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$memberData = $result->fetch_assoc();


$sqlOccupants = "SELECT * FROM occupants WHERE member_id = '{$_SESSION['member_id']}'";
$resultOccupants = $conn->query($sqlOccupants);
$occupantsData = $resultOccupants->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Dashboard</title>
    <link rel="stylesheet" href="./css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }
        .profile-container {
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 5px 10px 8px 10px #165259;
            width: 80%;
        }
        .profile-details, .occupants-details {
            margin-bottom: 20px;
            overflow-x: auto;
        }
        .profile-details table, .occupants-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .profile-details th, .profile-details td, .occupants-details th, .occupants-details td {
            padding: 8px;
            border: 1px solid #ddd;
            white-space: nowrap;
        }
        .arrow-down {
            display: inline-block;
            float: right;
            margin-left: auto;
            margin-top: 7px;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #fff;
        }
        .sidebar ul ul {
            list-style: none;
            padding-left: 20px;
            display: none;
        }
        .sidebar ul ul li {
            margin-bottom: 0;
        }
        .sidebar ul li.active > ul {
            display: block;
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
        <div class="profile-container">
            <h2>Member Information</h2>
            <div class="profile-details">
                <table>
                    <tr>
                        <th style="background-color: #337AB7; color: #fff;">Field</th>
                        <th style="background-color: #337AB7; color: #fff;">Information</th>
                    </tr>
                    <tr>
                        <td>Membership No:</td>
                        <td><?php echo htmlspecialchars($memberData['membership_no']); ?></td>
                    </tr>
                    <tr>
                        <td>Full Name:</td>
                        <td><?php echo htmlspecialchars($memberData['last_name']) . ', ' . htmlspecialchars($memberData['first_name']) . ' ' . htmlspecialchars($memberData['middle_name']); ?></td>
                    </tr>
                    <tr>
                        <td>Contact No:</td>
                        <td><?php echo htmlspecialchars($memberData['contact_no']); ?></td>
                    </tr>
                    <tr>
                        <td>Email Address:</td>
                        <td><?php echo htmlspecialchars($memberData['email_address']); ?></td>
                    </tr>
                    <tr>
                        <td>Occupation:</td>
                        <td><?php echo htmlspecialchars($memberData['occupation']); ?></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td><?php echo htmlspecialchars($memberData['address']); ?></td>
                    </tr>
                    <tr>
                        <td>Educational Attainment:</td>
                        <td><?php echo htmlspecialchars($memberData['educ_attainment']); ?></td>
                    </tr>
                    <tr>
                        <td>Birthdate:</td>
                        <td><?php echo htmlspecialchars($memberData['birthdate']); ?></td>
                    </tr>
                    <tr>
                        <td>Date Submitted:</td>
                        <td><?php echo htmlspecialchars($memberData['date_submitted']); ?></td>
                    </tr>
                    <tr>
                        <td>Sex:</td>
                        <td><?php echo htmlspecialchars($memberData['sex']); ?></td>
                    </tr>
                    <tr>
                        <td>Civil Status:</td>
                        <td><?php echo htmlspecialchars($memberData['civil_status']); ?></td>
                    </tr>
                    <tr>
                        <td>Homeowner Status:</td>
                        <td><?php echo htmlspecialchars($memberData['homeowner_status']); ?></td>
                    </tr>
                    <tr>
                        <td>Deed of Sale Status:</td>
                        <td><?php echo htmlspecialchars($memberData['dos_status']); ?></td>
                    </tr>
                    <tr>
                        <td>Length of Stay:</td>
                        <td><?php echo htmlspecialchars($memberData['length_of_stay']); ?></td>
                    </tr>
                    <tr>
                        <td>Name of Owner:</td>
                        <td><?php echo htmlspecialchars($memberData['owner_name']); ?></td>
                    </tr>
                </table>
            </div>
            
            <h2>Occupants' Information</h2>
            <div class="occupants-details">
                <table>
                    <tr>
                        <th style="background-color: #337AB7; color: #fff;">Name</th>
                        <th style="background-color: #337AB7; color: #fff;">Age</th>
                        <th style="background-color: #337AB7; color: #fff;">Relationship</th>
                    </tr>
                    <?php foreach ($occupantsData as $occupant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($occupant['name']); ?></td>
                            <td><?php echo htmlspecialchars($occupant['age']); ?></td>
                            <td><?php echo htmlspecialchars($occupant['relationship']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <form action="add_occupant.php" method="POST">
                    <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($memberData['id']); ?>">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" required>
                    <label for="age">Age:</label>
                    <input type="number" name="age" id="age" required>
                    <label for="relationship">Relationship:</label>
                    <input type="text" name="relationship" id="relationship" required>
                    <button type="submit">Add Occupant</button>
                </form>
            </div>
        </div>
</div>

























    
</body>
</html>
