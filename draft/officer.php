<?php
require('config.php');
session_start();

$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id 
        WHERE m.id = '{$_SESSION['member_id']}'";

$result = $conn->query($sql);
$memberData = $result->fetch_assoc();

if ($memberData['group'] != 3) {
    header('Location: member.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/announcement.css">
    <link rel="stylesheet" type="text/css" href="./css/member.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>HOA Dashboard</title>

    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background-color: #fdebd7;
}

.container {
    display: flex;
    margin-top: 75px; /* Adjust this to match the header height */
    height: calc(100vh - 75px);
}

.sidebar {
    background-color: #144F05;
    color: #fff;
    width: 200px;
    height: 100vh;
    padding: 10px;
    position: fixed;
    top: 0;
    left: 0;
}

.sidebar h3 {
    text-align: center;
    margin-bottom: 20px;
}

.sidebar ul {
    list-style: none;
    padding: 0;
    width: 100%;
}

.sidebar li {
    margin-bottom: 10px;
}

.sidebar a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #fff;
    transition: background-color 0.1s, border-left 0.1s, transform 0.1s;
}

.sidebar a:hover {
    background-color: #555;
    border-left: 5px solid #fff;
    transform: scale(1.05);
}

.logo {
    display: block;
    margin: 0 auto 20px;
    width: 130px;
}

.header {
    background-color: #c1930c;
    color: #fff;
    padding: 15px;
    position: fixed;
    top: 0;
    left: 200px; /* Adjust to create separation */
    z-index: 1000;
    height: 90px; /* Adjust the height as needed */
    box-sizing: border-box;
    width: calc(100% - 200px); /* Full width minus sidebar */
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow effect */
}

.header table {
    width: 100%;
    color: #fff;
}

.header img {
    border-radius: 50%;
}

.header select {
    background-color: #337AB7;
    color: #fff;
}

.dashboard-body {
    margin-left: 220px; /* Space for sidebar */
    padding: 20px;
    width: calc(100% - 220px); /* Full width minus sidebar */
    display: flex;
    justify-content: center; /* Center align content */
    align-items: flex-start; /* Align items at the start */
}

section {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 5px 10px 8px 10px #888888; /* Shadow effect */
    max-width: 1200px;
    width: 100%; /* Make section full width within max-width constraint */
}

.card {
    display: inline-block;
    margin: 10px;
    padding: 20px;
    border-radius: 8px;
    width: 200px;
    height: 150px;
    text-align: center;
}

.card h4 {
    margin-bottom: 10px;
}

.card svg {
    width: 40px;
    height: 40px;
}

.card-green {
    background-color: #4CAF50;
}

.card-red {
    background-color: #f44336;
}

.card-blue {
    background-color: #2196F3;
}



.graphs-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: left;
            margin: 30px 20px;
        }
/* Styling for the profile section */
/* Styling for the profile section */
.profile {
    max-width: 1500px;
    margin: 150px auto 50px; /* Space above and below */
    padding: 40px 50px; /* Adjusted padding for balanced spacing */
    background-color: #c1930c; /* Maintain the same color */
    color: #fff;
    border-radius: 12px; /* More rounded corners for a softer look */
    border: 1px solid #ddd;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Enhanced shadow for a lifted effect */
    text-align: center; /* Center-align text for a more balanced look */
}

.profile h1 {
    font-size: 3em; /* Larger heading size for prominence */
    margin-bottom: 20px; /* Space below the heading */
    font-weight: bold; /* Bold text for emphasis */
}

.profile p {
    font-size: 1.2em; /* Slightly larger font size for better readability */
    line-height: 1.7; /* Increased line height for easier reading */
    margin-bottom: 20px; /* Space below paragraphs */
}

/* Styling for the announcements section */
.announce {
    max-width: 1500px;
    margin: 0 auto; /* Center the element horizontally */
    padding: 30px 40px; /* Adjusted padding for better spacing */
    background-color: #c1930c; /* Maintain the same color */
    color: #fff;
    border-radius: 12px; /* Consistent rounded corners */
    border: 1px solid #ddd;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Consistent shadow styling */
    overflow-y: auto; /* Enable vertical scrolling if content overflows */
    height: 500px; /* Adjusted height for better content fit */
    font-family: 'Arial', sans-serif; /* Consistent and clean font */
}

.announce h2 {
    margin-top: 0; /* Remove top margin for the heading */
    font-size: 2.2em; /* Larger font size for better readability */
    border-bottom: 3px solid #ddd; /* Thicker bottom border for emphasis */
    padding-bottom: 15px; /* Space below the heading */
    margin-bottom: 20px; /* Space between the heading and content */
    font-weight: bold; /* Bold text for emphasis */
}

.announce p {
    line-height: 1.7; /* Increased line height for readability */
    margin-bottom: 20px; /* Space between paragraphs */
    font-size: 1.15em; /* Slightly larger font size for readability */
}

/* Optional: Add a responsive design for smaller screens */
@media (max-width: 768px) {
    .profile, .announce {
        padding: 20px; /* Reduce padding on smaller screens */
        height: auto; /* Remove fixed height for better adaptability */
    }

    .profile h1, .announce h2 {
        font-size: 1.8em; /* Adjust font size for smaller screens */
    }

    .profile p, .announce p {
        font-size: 1em; /* Adjust font size for readability on smaller screens */
    }
}
    
     .announcement-title {
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }







    </style>

    

    <script>
function showContent(contentId, title) {
    // Hide all content sections
    const sections = ['dashboard', 'announcementcenter', 'reports', 'qrcodes', 'monthlydues', 'receipts', 'generatesms', 'documents', 'certification'];
    sections.forEach(sec => {
        document.getElementById(sec).style.display = 'none';
    });

    // Show the selected content section
    document.getElementById(contentId).style.display = 'block';

    // Update the header title
    document.getElementById('header-title').innerText = title;
}
</script>

</head>
<body>
    <div class="sidebar">
        <img src="./images/anak.png" alt="Anak Pawis Logo" class="logo">
        <h3>ANAK-PAWIS HOMEOWNERS' ASSOCIATION (APHOA), INC.</h3>
        <a href="javascript:void(0)" onclick="showContent('dashboard', 'Dashboard')">Dashboard</a>
        <a href="javascript:void(0)" onclick="showContent('announcementcenter', 'Announcement Center')">Announcement Center</a>
        <a href="javascript:void(0)" onclick="showContent('reports', 'Reports')">Reports</a>
        <a href="javascript:void(0)" onclick="showContent('qrcodes', 'QR Codes')">QR Codes</a>
        <a href="javascript:void(0)" onclick="showContent('monthlydues', 'Monthly Dues')">Monthly Dues</a>
        <a href="javascript:void(0)" onclick="showContent('receipts', 'Receipts')">Receipts</a>
        <a href="javascript:void(0)" onclick="showContent('generatesms', 'Generate SMS')">Generate SMS</a>
        <a href="javascript:void(0)" onclick="showContent('documents', 'Documents')">Documents</a>
        <a href="javascript:void(0)" onclick="showContent('certification', 'Certification')">Certification</a>
    </div>

    
    <div class="content">
        <div class="header">
            <table width="100%">
                <tr>
                    <td>
                        <h3 id="header-title" style="margin: 1px;">Dashboard</h3>
                    </td>
                    <td align="right">
                        <img src="./images/menicon.png" width="50" height="50">
                    </td>
                    <td width="120" align="right">
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

            <div id="dashboard">
            <!-- Dashboard content -->
            <div class="profile">
                <table width="100%">
                    <tr>
                        <td align="left">
                            <h3 style="margin: 1px;">Welcome Officer!</h3>
                        </td>
                        <td align="right">
                            <a href="HOAprofile.php">View Profile &gt;</a>
                        </td>
                    </tr>
                </table>
                <hr>
                <table width="100%" border="0">
                    <tr>
                        <td valign="center" width="80">
                            <center>
                                <img src="./images/menicon.png" width="80" height="80" style="border-radius: 50%;">
                            </center>
                        </td>
                        <td valign="top">
                            <br>
                            <table id="table1">
                                <tr>
                                    <th>
                                        Membership No: <?php echo $memberData['membership_no']; ?>
                                        <br>
                                        Name: <?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']) ?>
                                    </th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="announce" style="display: block;">
                <table width="100%">
                    <tr>
                        <td align="left">
                            
                        </td>
                        <td align="right">
                            <a href="">View All &gt;</a>
                        </td>
                    </tr>
                </table>
                <?php include './includes/announcement.php'; ?>
                
            </div>
            </div>
           
        <div id="reports" style="display: none;">
            <h3>Reports</h3>
            <!-- Reports content goes here -->
        </div>

    
    <div id="announcementcenter" style="display: none;">
    <h3>Announcement Center</h3>
    <!-- announcement content goes here -->
    <div id="message" style="color: green;"></div>
    <form id="announcementForm" action="./includes/announcement.php" method="post">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="message" placeholder="Message" required></textarea>
        <button type="submit">Submit</button>
    </form>
</div>

<script>
document.getElementById('announcementForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting via the browser
    
    var formData = new FormData(this);

    // Send the form data asynchronously
    var xhr = new XMLHttpRequest();
    xhr.open('POST', './includes/announcement.php', true);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
            // Success response
            document.getElementById('message').textContent = xhr.responseText;
            document.getElementById('message').style.color = 'green';
            document.getElementById('announcementForm').reset(); // Reset form fields
            setTimeout(function() {
                document.getElementById('message').textContent = ''; // Clear message after delay
            }, 2000); // 2000 milliseconds (2 seconds) delay
        } else {
            // Error response
            console.error('Request failed with status:', xhr.status);
        }
    };

    xhr.onerror = function() {
        console.error('Request failed');
    };

    xhr.send(formData);
});
</script>



        <div id="qrcodes" style="display: none;">
            <h3>QR Codes</h3>
            <!-- QR Codes content goes here -->
        </div>

        <div id="monthlydues" style="display: none;">
            <h3>Monthly Dues</h3>
            <!-- monthly dues content goes here -->
        </div>

        <div id="receipts" style="display: none;">
            <h3>Receipts</h3>
            <!-- receipts content goes here -->
        </div>

        <div id="generatesms" style="display: none;">
            <h3>Generate SMS</h3>
            <!-- generatesms content goes here -->
            </div>

        <div id="documents" style="display: none;">
            <h3>Documents</h3>
            <!-- documents content goes here -->
            </div>

        <div id="certification" style="display: none;">
            <h3>Certification.</h3>
            <!-- certification content goes here -->
            </div>
    </div>
</body>
</html>
