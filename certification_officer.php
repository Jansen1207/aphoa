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
    <title>certification_officer</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/certification_officer.css"> <!-- Include the CSS file -->
</head>
<body>
<div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Certification</h1>
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

<div class="upload-container">
        <h2>Upload Certificate</h2>
        <form action="/upload" method="post" enctype="multipart/form-data">
            <input type="file" name="certificate" required><br>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>