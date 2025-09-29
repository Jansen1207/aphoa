<?php
require('config.php');
session_start();


$complaint_id = intval($_GET['id']);


$sql = "SELECT * FROM complaints WHERE id = $complaint_id AND member_id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$complaint = $result->fetch_assoc();

if (!$complaint) {
    echo "Complaint not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaint</title>
</head>
<body>
    <h2>Complaint Details</h2>
    <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($complaint['category']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($complaint['address']); ?></p>
    <p><strong>Date of Incident:</strong> <?php echo htmlspecialchars($complaint['incident_date']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($complaint['status']); ?></p>
    <p><strong>Date Submitted:</strong> <?php echo htmlspecialchars($complaint['created_at']); ?></p>

    <?php if ($complaint['files']): ?>
        <h3>Supporting Documents/Images:</h3>
        <ul>
            <?php foreach (explode(',', $complaint['files']) as $file): ?>
                <li><a href="<?php echo htmlspecialchars($file); ?>" target="_blank">View File</a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="complaint.php">Back to Complaints</a>
</body>
</html>
