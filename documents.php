<?php
require('config.php');
session_start();

// SQL to fetch member data
$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";

$result = $conn->query($sql);
$memberData = $result->fetch_assoc();

// Define the directory where PDFs are stored
$uploadDir = 'uploads/';
$documents = [];

// Scan the directory for PDF files
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
            $documents[] = $file;
        }
    }
}

// Handle file uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documents'])) {
    foreach ($_FILES['documents']['name'] as $key => $fileName) {
        $targetFilePath = $uploadDir . basename($fileName);
        // Check if the uploaded file is a PDF
        if (pathinfo($targetFilePath, PATHINFO_EXTENSION) === 'pdf') {
            if (move_uploaded_file($_FILES['documents']['tmp_name'][$key], $targetFilePath)) {
                $uploadMessage = "Upload complete: $fileName";
            } else {
                $uploadError = "Error uploading file: $fileName";
            }
        } else {
            $uploadError = "Only PDF files are allowed: $fileName";
        }
    }
    // Refresh the page to display new files
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $fileToDelete = $_GET['delete'];
    $filePath = $uploadDir . $fileToDelete;
    if (file_exists($filePath)) {
        unlink($filePath);
        $deleteMessage = "File deleted: $fileToDelete";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
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

        .container {
            display: flex;
            margin-top: 100px;
            height: calc(100vh - 85px);
            
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
                <li><a href="member.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="complaint.php">My Complaint</a></li>
                <li><a href="monthly_dues.php">Monthly Dues Update</a></li>
                <li>
                    <a class="current"><i class="fas fa-money-check-alt" aria-hidden="true"></i>Payment Categories<span class="arrow-down"></span></a>
                    <ul>
                        <li><a href="Payment_MDues.php"><i class="fas fa-caret-right" aria-hidden="true"></i>Monthly Dues</a></li>
                        <li><a href="Payment_Certification.php"><i class="fas fa-caret-right" aria-hidden="true"></i>Certification</a></li>
                        
                        <li><a href="Payment_CarStickers.php"><i class="fas fa-caret-right" aria-hidden="true"></i>Car Stickers</a></li>
                    </ul>
                </li>
                <li><a href="documents.php">Documents</a></li>
                <li><a href="achievements.php">Achievements</a></li>
            </ul>
        </div>
        
        <div class="main-content">
        <section class="documents-section">
           

            <div class="documents-list">
                <h3>Uploaded Documents</h3>
                <?php if (empty($documents)): ?>
                    <p>No documents uploaded yet.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <thead>
                            <tr style="background-color: #f2f2f2;">
                                <th style="padding: 10px; border: 1px solid #ddd;">Title</th>
                                <th style="padding: 10px; border: 1px solid #ddd;">View</th>
                                <th style="padding: 10px; border: 1px solid #ddd;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars(pathinfo($doc, PATHINFO_FILENAME)); ?></td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <a href="<?php echo $uploadDir . htmlspecialchars($doc); ?>" target="_blank" style="text-decoration: none; color: #007BFF;">View Document</a>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <a href="?delete=<?php echo urlencode($doc); ?>" onclick="return confirmDelete();" style="color: red; text-decoration: none;">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
        </div>
    </div>
</body>
</html>
