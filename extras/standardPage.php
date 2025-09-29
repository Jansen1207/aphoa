<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }

        .container {
            display: flex;
            margin-top: 75px; /* Adjust this to match the header height */
            height: calc(100vh - 75px);
        }

    
        .sidebar a:hover {
            background-color: #555;
            border-left: 5px solid #fff;
            transform: scale(1.05);
        }

      
    </style>
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
                <!-- Member Dashboard Navigation -->
                <li><a href="MemberDashboard.php">Dashboard</a></li>
                <li><a href="Profile.php">Profile</a></li>
                <li><a href="complaint.php">My Complaint</a></li>
                <li><a href="MDues.php">Monthly Dues</a></li>
                <li><a href="paymentmain.php">Payment</a></li>
                <li><a href="Documents.php">Documents</a></li>
            </ul>
        </div>
        
      
    </div>
</body>
</html>
