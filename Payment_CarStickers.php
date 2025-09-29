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
    <link rel="stylesheet" href="./css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }

        .container {
            display: flex;
            margin-top: 85px; /* Adjust this to match the header height */
			
            height: calc(100vh - 75px);
			
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .carstickers-container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 20px;
            max-width: 800px;
            margin: auto;

        }

        .carstickers-container h3 {
            background-color: #337AB7;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
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

        .radio-container {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .radio-container label {
            margin-bottom: 10px;
            margin-right: 380px;
        }

        .radio-container input[type="radio"] {
            margin-right: -380px; /* Adjust margin as needed */
        }

        .radio-container .radio-group {
            display: flex;
            align-items: center;
            margin-left: -380px;
        }

        .vehicle-quantity {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .vehicle-quantity input[type="number"] {
            width: 50px;
            margin-left: 10px;
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

            var radios = document.querySelectorAll('.radio-container input[type="radio"]');
            var totalInput = document.getElementById('total');
            var quantities = document.querySelectorAll('.vehicle-quantity input[type="number"]');
            var vehiclePrices = {
                motor: 0,
                car: 0,
                truck: 0
            };

            radios.forEach(function(radio) {
                radio.addEventListener('change', updateTotal);
            });

            quantities.forEach(function(quantity) {
                quantity.addEventListener('input', updateTotal);
            });

            function updateTotal() {
                var selectedPrice = 0;
                radios.forEach(function(radio) {
                    if (radio.checked) {
                        selectedPrice = parseInt(radio.value, 10);
                    }
                });

                var total = 0;
                quantities.forEach(function(quantity) {
                    var vehicleType = quantity.name.split('_')[0];
                    var quantityValue = parseInt(quantity.value, 10) || 0;
                    total += selectedPrice * quantityValue;
                });

                totalInput.value = total;
            }
        });
    </script>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Car Stickers</h1>
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
            <div class="carstickers-container">
                <h3>Car Stickers</h3>
                
                <form action="process_payment.php" method="POST" enctype="multipart/form-data">
                    <div class="radio-container form-group">
                        <label class="radio-group"><input type="radio" name="car_sticker_type" value="50" required> Homeowner/Sharer (₱50)</label>
                        <label class="radio-group"><input type="radio" name="car_sticker_type" value="70" required> Free Occupier/Renter (₱70)</label>
                    </div>
                    
                    <div class="form-group">
                        <label>Choose Vehicle Type and Quantity:</label>
                        <div class="vehicle-quantity">
                            <label for="motor">Motorcycle</label>
                            <input type="number" name="motor_quantity" min="0" value="0">
                        </div>
                        <div class="vehicle-quantity">
                            <label for="car">Car</label>
                            <input type="number" name="car_quantity" min="0" value="0">
                        </div>
                        <div class="vehicle-quantity">
                            <label for="truck">SUV</label>
                            <input type="number" name="truck_quantity" min="0" value="0">
                        </div>
						<div class="vehicle-quantity">
                            <label for="others">others</label>
                            <input type="number" name="others" min="0" value="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mode_of_payment">Mode of Payment:</label>
                        <select name="mode_of_payment" id="mode_of_payment" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">G-Cash</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="total">Total Amount:</label>
                        <input type="text" id="total" name="total" readonly>
                    </div>
                    <div class="form-group">
                        <label for="proof_of_payment">Upload Proof of Payment:</label>
                        <input type="file" name="proof_of_payment" id="proof_of_payment" accept=".jpg,.jpeg,.png" required>
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
                                <th>Transaction ID</th>
                                <th>Certificate Type</th>
                                <th>Amount</th>
                                <th>Payment Mode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Certificate of Residency</td>
                                <td>₱50</td>
                                <td>Bank Transfer</td>
                                <td>Paid</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Certificate of Indigency</td>
                                <td>₱70</td>
                                <td>G-Cash</td>
                                <td>Paid</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
