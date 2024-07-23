<?php
require('config.php');
session_start();

$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";

$result = $conn->query($sql);
$memberData = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Dashboard</title>
	<link rel="stylesheet" href="./css/member.css">
	<style>
	
body{
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
			
	 .sidebar a:hover {
            background-color: #555;
            border-left: 5px solid #fff;
            transform: scale(1.05);
        }
        }
.profile-container {
    margin: -800px 20px 100px 290px; /* Top: 70px, Right: 30px, Bottom: 40px, Left: 30px */
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 5px 10px 8px 10px #165259;
    width: 80%; /* Adjust the width */
    max-width: 10000px; /* Set a maximum width */
}

.profile-details {
    margin-bottom: 20px;
    overflow-x: auto; /* add horizontal scrollbar if table is too wide */
}

.profile-details table {
    width: 100%;
    border-collapse: collapse;
}

.profile-details th, .profile-details td {
    padding: 8px;
    border: 1px solid #ddd;
    white-space: nowrap; /* prevent text from wrapping */
}

.occupants-details {
    margin-bottom: 20px;
    overflow-x: auto; /* add horizontal scrollbar if table is too wide */
}

.occupants-details table {
    width: 100%;
    border-collapse: collapse;
}

.occupants-details th, .occupants-details td {
    padding: 8px;
    border: 1px solid #ddd;
    white-space: nowrap; /* prevent text from wrapping */
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
                <!-- Member Dashboard Navigation -->
                <li><a href="member.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="complaint.php">My Complaint</a></li>
                <li><a href="monthly_dues.php">Monthly Dues</a></li>
                <li><a href="paymentmain.php">Payment</a></li>
                <li><a href="documents.php">Documents</a></li>
            </ul>
        </div>
       
        
    </div>

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
                <td>[Membership No]</td>
            </tr>
            <tr>
                <td>Full Name:</td>
                <td>[Last Name], [First Name] [Middle Name]</td>
            </tr>
            <tr>
                <td>Contact No:</td>
                <td>[Contact No]</td>
            </tr>
            <tr>
                <td>Email Address:</td>
                <td>[Email Address]</td>
            </tr>
            <tr>
                <td>Occupation:</td>
                <td>[Occupation]</td>
            </tr>
            <tr>
                <td>Address:</td>
                <td>[Address]</td>
            </tr>
            <tr>
                <td>Educational Attainment:</td>
                <td>[Educational Attainment]</td>
            </tr>
            <tr>
                <td>Birthdate:</td>
                <td>[Birthdate]</td>
            </tr>
            <tr>
                <td>Date Submitted:</td>
                <td>[Date Submitted]</td>
            </tr>
            <tr>
                <td>Sex:</td>
                <td>[Sex]</td>
            </tr>
            <tr>
                <td>Civil Status:</td>
                <td>[Civil Status]</td>
            </tr>
            <tr>
                <td>Homeowner Status:</td>
                <td>[Homeowner Status]</td>
            </tr>
            <tr>
                <td>Type:</td>
                <td>[Type]</td>
            </tr>
            <tr>
                <td>Length of Stay:</td>
                <td>[Length of Stay]</td>
            </tr>
            <tr>
                <td>Name of Owner:</td>
                <td>[Name of Owner]</td>
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
            <tr>
                <td>[Occupant Name 1]</td>
                <td>[Occupant Age 1]</td>
                <td>[Occupant Relationship 1]</td>
            </tr>
            <tr>
                <td>[Occupant Name 2]</td>
                <td>[Occupant Age 2]</td>
                <td>[Occupant Relationship 2]</td>
            </tr>
            <!-- Repeat for additional occupants -->
        </table>
    </div>
</div>

</body>
</html>
