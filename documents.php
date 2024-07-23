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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }

        .container {
            display: flex;
            margin-top: 100px; /* Adjust this to match the header height */
            height: calc(100vh - 85px);
			
        }

     

        .sidebar ul li a:hover {
            background-color: #555;
            border-left: 5px solid #fff;
            transform: scale(1.05);
        }

        .main-content {
            flex: 1;
            padding: 20px;
			
        }

        .documents-section {
            background-color: #fff;
            border-radius: 5px;
           box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            margin-left: 370px;
            margin-right: 200px;
        }

        .documents-section h3 {
            margin-bottom: 20px;
            background-color: #337AB7; 
            color: #fff;
            padding: 10px;
            border-radius: 5px;
        }

        .document-categories,
        .upload-document,
        .document-list,
        .additional-info {
            margin-bottom: 30px;
        }

        .document-categories .category {
            display: inline-block;
            width: 48%;
            padding: 10px;
            margin: 1%;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            cursor: pointer;
            box-sizing: border-box;
        }

        .document-categories .category:hover {
            background-color: #e1e1e1;
        }

        .upload-document input[type="file"],
        .upload-document select,
        .upload-document input[type="text"],
        .upload-document button {
            display: block;
            margin: 10px 0;
        }

        .upload-document button {
            background-color: #337AB7;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .upload-document button:hover {
            background-color: #286090;
        }

        .document-list ul {
            list-style: none;
            padding: 0;
        }

        .document-list li {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }

        .document-list li a {
            text-decoration: none;
            color: #337AB7;
        }

        .additional-info p {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Documents</h1>
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
        
        <div class="main-content">
            <section class="documents-section">
                <div class="document-categories">
                    <h3>Document Categories</h3>
                    <div class="category">Bylaws</div>
                    <div class="category">Meeting Minutes</div>
                    <div class="category">Financial Reports</div>
                    <div class="category">General Announcements</div>
                </div>
                
                <div class="upload-document">
                    <h3>Upload Document</h3>
                    <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="document" required>
                        <select name="category" required>
                            <option value="" disabled selected>Select Category</option>
                            <option value="Bylaws">Bylaws</option>
                            <option value="Meeting Minutes">Meeting Minutes</option>
                            <option value="Financial Reports">Financial Reports</option>
                            <option value="General Announcements">General Announcements</option>
                        </select>
                        <input type="text" name="title" placeholder="Document Title" required>
                        <button type="submit">Upload</button>
                    </form>
                </div>
                
                <div class="document-list">
                    <h3>Available Documents</h3>
                    <ul>
                        <li><a href="#">Bylaws - Updated 2023</a></li>
                        <li><a href="#">Meeting Minutes - January 2024</a></li>
                        <li><a href="#">Financial Report - Q1 2024</a></li>
                        <!-- Add more documents dynamically using PHP -->
                    </ul>
                </div>

                <div class="additional-info">
                    <h3>Additional Information</h3>
                    <p>Document Categories: Organized using &lt;div&gt; elements for better styling and layout.</p>
                    <p>List of Available Documents: Styled with clear links.</p>
                    <p>Upload Document Feature: Includes category selection and document title input.</p>
                    <p>Date and Time Information: This would be dynamically handled via PHP or your backend system.</p>
                    <p>Notifications for New Documents: This feature would require additional backend logic.</p>
                    <p>Download/Print Options: Handled via the browser's built-in functionality or additional backend handling.</p>
                    <p>User Permissions and Security: To be implemented in your backend for document access control.</p>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
