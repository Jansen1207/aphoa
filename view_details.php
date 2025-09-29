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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>announcement_center</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/complaint_reports.css">
</head>
<body>
<div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Reports</h1>
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

<div class="reports-container">
    
    <div class="report-item">
        <div class="report-header">
            <span class="report-name">Name: John Doe</span>
            <span class="report-id">Complaint ID: 1</span>
            <span class="report-date">Date: 01/08/2024</span>
        </div>
        <h3 class="report-title">Complaint Title</h3>
        <p class="report-description">This is a description of the complaint. It provides details about the issue and any relevant information.</p>
        <p class="report-category">Category: General</p>
        <p class="report-address">Address: 123 Main St, City</p>
        <div class="report-attachments">
            <h4>Supporting Documents/Images:</h4>
            <a href="#" class="report-link">Document1.pdf</a>
            <a href="#" class="report-link">Image1.jpg</a>
        </div>
       <div class="report-actions">
    <div class="dropdown">
        <button class="btn btn-update dropdown-toggle">Update Status</button>
        <div class="dropdown-menu">
            <a href="#" class="dropdown-item under-review">Under Review</a>
            <a href="#" class="dropdown-item resolved">Resolved</a>

        </div>
    </div>
    <button class="btn btn-delete">Delete</button>
    <a href="complaint_reports.php" class="btn btn-back">Back</a>
</div>

        </div>
    </div>
</div>





