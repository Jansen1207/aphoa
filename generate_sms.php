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
    <title>generate_sms</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/generate_sms.css"> <!-- Include the CSS file -->
</head>
<body>
<div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Generate SMS</h1>
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

<div class="main">
            <!-- QR Code Generator Section -->
            <div class="chart-section">
                <h2>APHOA Monthly Dues Alert</h2>
                <form id="smsForm">
                    <div class="form-group">
                        <label for="month">Select Month:</label>
                        <select id="month" name="month" required>
                            <option value="">--Select Month--</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="button" onclick="sendSMS()">Send SMS</button>
                </form>
            </div>
        </div>
    </div>
    
<script>
    function sendSMS() {
        const month = document.getElementById('month').value;
        const message = document.getElementById('message').value;
        if (month && message) {
            
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const currentDate = today.toLocaleDateString(undefined, options);

            
            const smsMessage = `Dear Homeowners, please ensure your monthly dues are up to date for the month of ${month} to avoid your account becoming inactive. ${message} This notice was sent on ${currentDate}.`;

            alert(smsMessage); 
            
            
            
            
            
            
            
            
            
            
        } else {
            alert('Please select a month and enter a message.');
        }
    }
</script>
</body>
</html>