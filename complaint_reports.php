<?php
require('config.php');
session_start();
$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$memberData = $result->fetch_assoc();
$under_review_sql = "
    SELECT c.*, i.first_name, i.last_name 
    FROM complaints c 
    INNER JOIN members m ON c.member_id = m.id 
    INNER JOIN information i ON m.id = i.member_id 
    WHERE c.status = 'Under Review'
";
$under_review_result = $conn->query($under_review_sql);
$resolved_sql = "
    SELECT c.*, i.first_name, i.last_name 
    FROM complaints c 
    INNER JOIN members m ON c.member_id = m.id 
    INNER JOIN information i ON m.id = i.member_id 
    WHERE c.status = 'Resolved'
";
$resolved_result = $conn->query($resolved_sql);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resolve_id'])) {
    $resolve_id = intval($_POST['resolve_id']);
    $comment = isset($_POST['comment']) ? $conn->real_escape_string($_POST['comment']) : ''; // Retrieve and escape the comment
    
    // Update the database with the resolved status and comment
    $update_sql = "UPDATE complaints SET status = 'Resolved', comment = '$comment' WHERE id = $resolve_id";
    $conn->query($update_sql);

    // Redirect to the same page to prevent form resubmission
    header("Location: complaint_reports.php"); 
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints Report</title>
    <link rel="stylesheet" href="./css/member.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }
        .header {
            background-color: #165259;
            padding: 10px;
            color: white;
        }
        .reports-container {
            padding: 20px;
            margin: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .report-item {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .report-header {
        }
        .report-actions {
            margin-top: 10px;
        }
        .btn {
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
            background-color: #1b61ab;
        }
        .btn-resolved {
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: not-allowed;
            border-radius: 5px;
            background-color: #cccccc;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .resolved-list {
            margin-top: 20px;
            padding: 10px;
            border-top: 2px solid #ddd;
        }
    </style>
<style>
    #complaintModal {
        display: none; 
        position: fixed; 
        z-index: 1000; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5); 
    }
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; 
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
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
</style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Complaints Reports</h1>
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
    <div class="dashboard-body">
    <div class="reports-container">
    <h2>Complaints Under Review</h2>
<?php while ($complaint = $under_review_result->fetch_assoc()): ?>
    <div class="report-item">
        <div class="report-header">
            <span>Name: <?php echo htmlspecialchars($complaint['first_name'] . ' ' . $complaint['last_name']); ?></span><br>
            <span>Complaint ID: <?php echo htmlspecialchars($complaint['id']); ?></span><br>
            <span>Title: <?php echo htmlspecialchars($complaint['title']); ?></span><br>
            <span>Date: <?php echo htmlspecialchars($complaint['created_at']); ?></span>
        </div>
        <div class="report-actions">
            <form method="POST" id="resolveForm-<?php echo $complaint['id']; ?>" style="display:inline; opacity: 0.5;">
                <input type="hidden" name="resolve_id" value="<?php echo $complaint['id']; ?>">
                <label for="comment-<?php echo $complaint['id']; ?>">Comment:</label><br>
                <textarea name="comment" id="comment-<?php echo $complaint['id']; ?>" rows="5" cols="40" placeholder="Enter your comment here..." required disabled></textarea><br>
                <button type="submit" class="btn-resolved" id="resolveBtn-<?php echo $complaint['id']; ?>" disabled>Mark as Resolved</button>
            </form>
            <button class="btn btn-view" onclick="fetchComplaintDetails(<?php echo $complaint['id']; ?>)">View Details</button>
        </div>
    </div>
<?php endwhile; ?>

    <div class="resolved-list">
        <h2>Resolved Complaints</h2>
        <?php while ($resolved = $resolved_result->fetch_assoc()): ?>
            <div class="report-item">
                <div class="report-header">
                    <span>Name: <?php echo htmlspecialchars($resolved['first_name'] . ' ' . $resolved['last_name']); ?></span><br>
                    <span>Complaint ID: <?php echo htmlspecialchars($resolved['id']); ?></span><br>
                    <span>Title: <?php echo htmlspecialchars($resolved['title']); ?></span><br>
                    <span>Date: <?php echo htmlspecialchars($resolved['created_at']); ?></span>
                </div>
                <button class="btn btn-view" onclick="fetchComplaintDetails(<?php echo $resolved['id']; ?>)">View Details</button>
            </div>
        <?php endwhile; ?>
    </div>
</div>
    </div>
    <div id="complaintModal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Complaint Details</h2>
        <div id="modalBody"></div>
    </div>
</div>
<script>
    function fetchComplaintDetails(complaintId) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_complaint_details.php?id=' + complaintId, true);
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById('modalBody').innerHTML = this.responseText;
                document.getElementById('complaintModal').style.display = 'block';
                
                // Enable resolve form and button after viewing details
                const resolveForm = document.getElementById('resolveForm-' + complaintId);
                const resolveBtn = document.getElementById('resolveBtn-' + complaintId);
                const commentTextarea = document.getElementById('comment-' + complaintId);
                
                if (resolveForm && resolveBtn && commentTextarea) {
                    resolveForm.style.opacity = '1';
                    resolveBtn.disabled = false;
                    resolveBtn.classList.remove('btn-resolved');
                    resolveBtn.classList.add('btn');
                    commentTextarea.disabled = false;
                }
            }
        };
        xhr.send();
    }
    
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('complaintModal').style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == document.getElementById('complaintModal')) {
            document.getElementById('complaintModal').style.display = 'none';
        }
    }
</script>
</body>
</html>