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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents Officer</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/documents_officer.css"> <!-- Include the CSS file -->
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this file?");
        }
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
    <?php include './includes/officer_sidebar.php'; ?>

    <div class="main-content">
        <section class="documents-section">
            <div class="upload-section">
                <h3>Upload Document</h3>
                
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="file" name="documents[]" accept="application/pdf" multiple required>
                    <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Upload</button>
                </form>
                <small>"The file name of the PDF will be used to display its title"</small>

                <?php if (isset($uploadError)) echo "<p style='color: red;'>$uploadError</p>"; ?>
                <?php if (isset($uploadMessage)) echo "<p style='color: green;'>$uploadMessage</p>"; ?>
                <?php if (isset($deleteMessage)) echo "<p style='color: green;'>$deleteMessage</p>"; ?>
            </div>

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
</body>
</html>
