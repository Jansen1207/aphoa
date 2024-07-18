<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Dashboard - Complaint</title>
    <link rel="stylesheet" href="memberstyles.css">
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
            margin-top: 20px; /* Adjust this to match the header height */
            height: calc(100vh - 35px);
			margin-bottom: 100px	
        }

        .main-content {
			
            flex: 1;
            padding: 100px;
			
        }

        .complaint-body {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 50px;
            margin-left: 200px;
        }

        .complaint-body h3 {
            margin-bottom: 20px;
        }

        section {
            margin-bottom: 30px;
        }

        .submit-complaint form {
            display: flex;
            flex-direction: column;
        }

        .submit-complaint form label,
        .submit-complaint form input,
        .submit-complaint form textarea,
        .submit-complaint form select {
            margin-bottom: 10px;
        }

        .submit-complaint form button {
            align-self: flex-start;
            padding: 10px 20px;
            background-color: #337AB7;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-complaint form button:hover {
            background-color: #555;
        }

        .view-complaints table {
            width: 100%;
            border-collapse: collapse;
        }

        .view-complaints table th,
        .view-complaints table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .view-complaints table th {
            background-color: #f2f2f2;
        }

        .view-complaints table td button {
            padding: 5px 10px;
            background-color: #337AB7;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .view-complaints table td button:hover {
            background-color: #555;
        }

        .view-complaints table td button.edit {
            background-color: #ff9900;
        }

        .view-complaints table td button.delete {
            background-color: #ff3333;
        }

        .icon {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }

        .submit-complaint h3, .view-complaints h3 {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Complaint</h1>
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
        
        <div class="main-content">
            <section class="complaint-body">
                <div class="submit-complaint">
                    <h2><img src="comp3.png" alt="Submit Icon" class="icon"> Submit a New Complaint</h2>
                    <form action="submit_complaint.php" method="POST" enctype="multipart/form-data">
                        <label for="title">Title/Subject:</label>
                        <input type="text" id="title" name="title" required>
                        
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                        
                        <label for="category">Category/Type:</label>
                        <select id="category" name="category" required>
                            <option value="maintenance">Maintenance</option>
                            <option value="noise">Noise</option>
                            <option value="security">Security</option>
                            <option value="others">Others</option>
                            <!-- Add more categories as needed -->
                        </select>
                        
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" required>
                        
                        <label for="incident_date">Date of Incident:</label>
                        <input type="date" id="incident_date" name="incident_date" required>
                        
                        <label for="supporting_docs">Supporting Documents/Images:</label>
                        <input type="file" id="supporting_docs" name="supporting_docs[]" multiple>
                        
                        <button type="submit">Submit Complaint</button>
                    </form>
                </div>
                
                <div class="view-complaints">
                    <h3><img src="comp2.png" alt="View Icon" class="icon"> Your Complaints</h3>
                    <table>
                        <thead>
                            <tr>
                                <th style="background-color: #337AB7; color: #fff;"> Complaint ID</th>
                                <th style="background-color: #337AB7; color: #fff;">Title/Subject</th>
                                <th style="background-color: #337AB7; color: #fff;">Date Submitted</th>
                                <th style="background-color: #337AB7; color: #fff;">Status</th>
                                <th style="background-color: #337AB7; color: #fff;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example Complaint Row -->
                            <tr>
                                <td>12345</td>
                                <td>Noise Complaint</td>
                                <td>2023-07-01</td>
                                <td>Under Review</td>
                                <td>
                                    <button class="edit">View Details</button>
                                    <button class="edit">Edit</button>
                                    <button class="delete">Delete</button>
                                </td>
                            </tr>
                            <!-- Add rows dynamically using PHP -->
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
