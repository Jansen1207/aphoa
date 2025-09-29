<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Dues Update</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }

        .sidebar a:hover {
            background-color: #555;
            border-left: 5px solid #fff;
            transform: scale(1.05);
        }

        .container {
            display: flex;
            margin-top: 20px;
            height: calc(100vh - 20px);
            margin-bottom: 350px;
        }

        .main-content {
            flex: 1;
            padding: 100px;
        }

        .monthly-dues-section {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 50px;
            margin-left: 200px;
            position: relative;
        }

        .status {
            position: absolute;
            top: 20px;
            right: 20px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
        }

        
        .monthly-dues-section h3 {
            margin-bottom: 20px;
        }

        .year-selector {
            margin-bottom: 20px;
        }

        .monthly-dues-table {
            width: 100%;
            border-collapse: collapse;
        }

        .monthly-dues-table th, .monthly-dues-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .monthly-dues-table th {
            background-color: #f2f2f2;
        }

        .history-section {
            margin-top: 50px;
        }

        .history-section h3 {
            margin-bottom: 20px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th, .history-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .history-table th {
            background-color: #f2f2f2;
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
            display: none; /* Hide sub-menu by default */
        }

        .sidebar ul ul li {
            margin-bottom: 0;
        }

        .sidebar ul li.active > ul {
            display: block; /* Show sub-menu when parent is active */
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
        });
    </script>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Monthly Dues</h1>
                </td>
                <td align="right">
                    <img src="menicon.png" width="50" height="50" style="border-radius: 50%;">
                </td>
                <td width="120">
                    &nbsp;&nbsp;&nbsp;
                    <form action="logout.php" method="POST">
                        <select name="logout" onchange="this.form.submit()">
                            <option style="display:none;">ALVIN ARTEZA</option>
                            <option style="background-color: #337AB7;color:#fff;">Logout</option>
                        </select>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <img src="aphoa.png" alt="Anak Pawis Logo" class="logo">
            <h3>ANAK-PAWIS HOMEOWNERS' ASSOCIATION (APHOA), INC.</h3>
            <ul>
                 <li><a href="MemberDashboard.php">Dashboard</a></li>
                <li><a href="Profile.php">Profile</a></li>
                <li><a href="complaint.php">My Complaint</a></li>
                <li><a href="MDues.php">Monthly Dues Update</a></li>
                <li>
                    <a class="current"><i class="fas fa-money-check-alt" aria-hidden="true"></i>Payment Categories<span class="arrow-down"></span></a>
                    <ul>
                        <li><a href="Payment_MDues.php"><i class="fas fa-caret-right" aria-hidden="true"></i>Monthly Dues</a></li>
                        <li><a href="Payment_Certification.php"><i class="fas fa-caret-right" aria-hidden="true"></i>Certification</a></li>
                        
                        <li><a href="Payment_CarStickers.php"><i class="fas fa-caret-right" aria-hidden="true"></i>Car Stickers</a></li>
                    </ul>
                </li>
                <li><a href="Documents.php">Documents</a></li>
				<li><a href="Achievements.php">Achievements</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <section class="monthly-dues-section">
                <?php
                // Example PHP to determine status
                // Replace this with actual logic to fetch dues status from database

                $missed_payments = 4; // Example value, calculate this from actual data

                $status = ($missed_payments >= 3) ? 'inactive' : 'active';
                $status_text = ($missed_payments >= 3) ? 'Inactive' : 'Active';
                ?>

                <div class="status <?php echo $status; ?>">
                    <?php echo $status_text; ?>
                </div>

                <div class="year-selector">
                    <label for="year">Select Year: </label>
                    <select id="year" name="year">
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
                <h3>View Monthly Dues</h3>
                <table class="monthly-dues-table">
                    <thead>
                        <tr>
                            <th style="background-color: #337AB7; color: #fff;"> Month</th>
                            <th style="background-color: #337AB7; color: #fff;"> Year</th>
                            <th style="background-color: #337AB7; color: #fff;"> Due Amount</th>
                            <th style="background-color: #337AB7; color: #fff;"> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>January</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Paid</td>
                        </tr>
                        <tr>
                            <td>February</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>March</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>April</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Paid</td>
                        </tr>
                        <tr>
                            <td>May</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Paid</td>
                        </tr>
                        <tr>
                            <td>June</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>July</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Paid</td>
                        </tr>
                        <tr>
                            <td>August</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>September</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Paid</td>
                        </tr>
                        <tr>
                            <td>October</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>November</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Paid</td>
                        </tr>
                        <tr>
                            <td>December</td>
                            <td>2023</td>
                            <td>₱50</td>
                            <td>Unpaid</td>
                        </tr>
                        <!-- Add more rows dynamically using PHP -->
                    </tbody>
                </table>

                <section class="history-section">
                    <h3>Payment History</h3>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th style="background-color: #337AB7; color: #fff;"> Date</th>
                                <th style="background-color: #337AB7; color: #fff;"> Amount</th>
                                <th style="background-color: #337AB7; color: #fff;"> Payment Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>July 5, 2023</td>
                                <td>₱50</td>
                                <td>Credit Card</td>
                            </tr>
                            <tr>
                                <td>June 10, 2023</td>
                                <td>₱50</td>
                                <td>Bank Transfer</td>
                            </tr>
                            <!-- Add more rows dynamically using PHP -->
                        </tbody>
                    </table>
                </section>
            </section>
        </div>
    </div>
</body>
</html>
