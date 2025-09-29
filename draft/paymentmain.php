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
            margin-top: 75px; /* Adjust this to match the header height */
            height: calc(100vh - 205px);
			margin-bottom: 260px;
			margin-top: 80px;
        }

        .sidebar a:hover {
            background-color: #555;
            border-left: 5px solid #fff;
            transform: scale(1.05);
        }

        .sidebar {
            width: 200px; /* Fixed width for sidebar */
        }

        .main-content {
            flex: 1;
            padding: 20px; /* Reduced padding */
            display: flex;
            justify-content: center; /* Center the content */
            align-items: flex-start; /* Align items to the top */
            flex-direction: column; /* Stack elements vertically */
            margin-top: 20px; /* Add margin for spacing */
        }

        .payment-form {
             background-color: #fff;
    border-radius: 10px;
    box-shadow: 5px 10px 8px 10px #165259;
    padding: 23px; /* Padding inside the form */
    width: 350px; /* Adjusted width */
    height: 740px; /* Adjusted height; can set a specific value like 400px */
    margin: 150px; /* Margin for spacing */
    position: relative; /* Allows positioning */
    top: 80px; /* Vertical adjustment */
	left: 400px;
	margin-bottom: 260px;
	margin-top: 250px;

        }

        .main-content .logo-top {
            display: block;
            margin: 0 auto;
            margin-bottom: 20px;
            width: 100px;
            height: 100px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group .error {
            color: red;
            font-size: 14px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #337AB7;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #555;
        }

        .payment-history {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            margin-left: 10px; /* Closer margin to main content */
            width: 300px; /* Fixed width for payment history */
            position: relative; /* Allows for positioning */
            left: -500px; /* Adjust to move left */
            margin-top: 50px; /* Adjust vertical position */
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .history-table th,
        .history-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .history-table th {
            background-color: #f2f2f2;
        }

        .checkbox-group {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function updateTotal() {
            const checkboxes = document.querySelectorAll('.month-checkbox');
            const totalDisplay = document.getElementById('totalAmount');
            const pricePerMonth = 40;
            let total = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    total += pricePerMonth;
                }
            });

            totalDisplay.textContent = `Total Amount: ₱${total}`;
        }
    </script>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Payment</h1>
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
                <li><a href="monthly_dues.php">Monthly Dues</a></li>
                <li><a href="paymentmain.php">Payment</a></li>
                <li><a href="documents.php">Documents</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="payment-form">
                <img src="./images/pay.png" alt="Payment Logo" class="logo-top">
                <h3>Submit Payment</h3>
                <form action="submit_payment.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="amount">Amount:</label>
                        <input type="text" id="amount" name="amount" value="40" readonly>
                        <span class="error"></span>
                    </div>

                    <div class="checkbox-group">
                        <label>Select Months:</label>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="January" onclick="updateTotal()"> January
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="February" onclick="updateTotal()"> February
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="March" onclick="updateTotal()"> March
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="April" onclick="updateTotal()"> April
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="May" onclick="updateTotal()"> May
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="June" onclick="updateTotal()"> June
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="July" onclick="updateTotal()"> July
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="August" onclick="updateTotal()"> August
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="September" onclick="updateTotal()"> September
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="October" onclick="updateTotal()"> October
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="November" onclick="updateTotal()"> November
                        </div>
                        <div>
                            <input type="checkbox" class="month-checkbox" value="December" onclick="updateTotal()"> December
                        </div>
                    </div>

                    <div class="form-group">
                        <div id="totalAmount">Total Amount: ₱0</div>
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Payment Method:</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="Online Bank Transfer">Online Bank Transfer</option>
                            <option value="Onsite Payment">Onsite Payment</option>
                        </select>
                        <span class="error"></span>
                    </div>

                    <div class="form-group">
                        <label for="proof_of_payment">Proof of Payment:</label>
                        <input type="file" id="proof_of_payment" name="proof_of_payment" required>
                        <span class="error"></span>
                    </div>

                    <button type="submit">Submit Payment</button>
                </form>
				
            </div>
			
        </div>

        <div class="payment-history">
            <h4>Payment History</h4>
            <table class="history-table">
                <thead>
                    <tr>
                       <th style="background-color: #337AB7; color: #fff;">Month</th>
                        <th style="background-color: #337AB7; color: #fff;">Year</th>
                       <th style="background-color: #337AB7; color: #fff;">Status</th>
                        <th style="background-color: #337AB7; color: #fff;">Amount</th>
                    </tr>
                </div>
</body>
</html>