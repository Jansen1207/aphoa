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
    <title>Certification Payment</title>
    <link rel="stylesheet" href="./css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }

        .container {
            display: flex;
            margin-top: 85px;
            height: calc(100vh - 75px);
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .certification-container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            max-width: 800px;
            margin: auto;
        }

        .certification-container h3 {
            background-color: #337AB7;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
        }

        .certification-list {
            list-style: none;
            padding: 0;
        }

        .certification-list li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
        }

        .certification-list li:last-child {
            border-bottom: none;
        }

        .certification-list span {
            font-size: 16px;
            margin-right: 20px;
        }

        .certification-list .cost {
            font-weight: bold;
            color: #337AB7;
        }

        .form-group {
            margin-top: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-group button {
            background-color: #337AB7;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #285a8e;
        }

        .history-container {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }

        .history-container h4 {
            margin-top: 0;
            font-size: 18px;
            color: #007bff;
        }

        .history-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-container th, .history-container td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .history-container th {
            background-color: #f2f2f2;
        }
    </style>
	<script>
        document.addEventListener("DOMContentLoaded", function () {
            var paymentLink = document.querySelector(".sidebar ul li a.current");
            paymentLink.addEventListener("click", function (e) {
                e.preventDefault();
                var parentLi = this.parentElement;
                parentLi.classList.toggle("active");
            });

            var checkboxes = document.querySelectorAll('.checkbox-container input[type="checkbox"]');
            var totalInput = document.getElementById('total');

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    var total = 0;
                    checkboxes.forEach(function(cb) {
                        if (cb.checked) {
                            total += 40; // Each month costs 40 pesos
                        }
                    });
                    totalInput.value = total;
                });
            });
        });
    </script>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Certification Payment</h1>
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
            <div class="certification-container">
                <h3>Select Certificate</h3>
                <ul class="certification-list">
                    <li>
                        <input type="checkbox" id="cert_meralco" data-cost="100">
                        <label for="cert_meralco">
                            <span>Meralco</span>
                            <span class="cost">₱100</span>
                        </label>
                    </li>
                    <li>
                        <input type="checkbox" id="cert_residency" data-cost="100">
                        <label for="cert_residency">
                            <span>Residency</span>
                            <span class="cost">₱100</span>
                        </label>
                    </li>
                    <li>
                        <input type="checkbox" id="cert_manila_water" data-cost="100">
                        <label for="cert_manila_water">
                            <span>Manila Water</span>
                            <span class="cost">₱100</span>
                        </label>
                    </li>
                    <li>
                        <input type="checkbox" id="cert_school_requirement" data-cost="50">
                        <label for="cert_school_requirement">
                            <span>School Requirement</span>
                            <span class="cost">₱50</span>
                        </label>
                    </li>
					<li>
                        <input type="checkbox" id="cert_cagreca" data-cost="100">
                        <label for="cert_cagreca">
                            <span>Cagreca</span>
                            <span class="cost">₱100</span>
                        </label>
                    </li>
                </ul>
                <form action="process_certification.php" method="POST">
                    <div class="form-group">
                        <label for="mode_of_payment">Mode of Payment:</label>
                        <select name="mode_of_payment" id="mode_of_payment" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">G-Cash</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="total">Total Amount:</label>
                        <input type="text" id="total" name="total" value="₱0" readonly>
                    </div>
                    <div class="form-group">
                        <label for="proof_of_payment">Proof of Payment:</label>
                        <input type="file" id="proof_of_payment" name="proof_of_payment" required>
                    </div>
                    <div class="form-group">
                        <button type="submit">Submit</button>
                    </div>
                </form>
				
				<div class="history-container">
                <h4>Payment History</h4>
                <table>
                    <thead>
                        <tr>
                           <th style="background-color: #337AB7; color: #fff;">Date</th>
                           <th style="background-color: #337AB7; color: #fff;">Certificate</th>
                           <th style="background-color: #337AB7; color: #fff;">Amount</th>
                           <th style="background-color: #337AB7; color: #fff;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2024-07-19</td>
                            <td>Meralco</td>
                            <td>₱100</td>
                            <td>Rejected</td>
                        </tr>
                        <tr>
                            <td>2024-07-15</td>
                            <td>Residency</td>
                            <td>₱100</td>
                            <td>Pending</td>
                        </tr>
                        <tr>
                            <td>2024-07-10</td>
                            <td>Manila Water</td>
                            <td>₱100</td>
                            <td>Paid</td>
                        </tr>
                        <tr>
                            <td>2024-07-05</td>
                            <td>School Requirement</td>
                            <td>₱50</td>
                            <td>Paid</td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
            </div>

            <!-- History Section -->
            
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.certification-list input[type="checkbox"]');
            const totalInput = document.getElementById('total');

            function updateTotal() {
                let total = 0;
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.getAttribute('data-cost'));
                    }
                });
                totalInput.value = `₱${total}`;
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotal);
            });

            // Initialize the total amount on page load
            updateTotal();
        });
    </script>
</body>
</html>
