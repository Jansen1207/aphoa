<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fdebd7;
        }

        .container {
            display: flex;
            margin-top: 75px;
            height: calc(100vh - 50px);
        }

        .sidebar a:hover {
            background-color: #555;
            border-left: 5px solid #fff;
            transform: scale(1.05);
        }

        .dashboard-body {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        .graphs-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: left;
            margin: 30px 20px;
        }

        .financial-container {
            margin: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 30px 15px;
            width: 300px;
            height: 300px;
        }

        .complaints-container {
            margin: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 30px 15px;
            width: 300px;
            height: 400px;
        }

        .announcement-container {
            margin: 10px;
            background-color: #fff; /* Changed to white */
            border-radius: 5px;
            box-shadow: 5px 10px 8px 10px #165259;
            padding: 40px 20px;
            width: calc(100% - 50px);
            height: 300px;
            color: #333;
            position: relative;
            overflow: hidden; /* Prevents overflow issues */
        }

        .announcement-title {
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .announcement-content {
            margin-top: 20px;
            overflow-y: auto; /* Allows scrolling */
            height: 220px; /* Set fixed height for scrollable content */
        }

        canvas {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 style="margin-bottom:1px;margin-top:1px;">Dashboard</h1>
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
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="#">My Complaint</a></li>
                <li><a href="#">Monthly Dues</a></li>
                <li><a href="#">Payment</a></li>
                <li><a href="#">Documents</a></li>
            </ul>
        </div>
        
        <div class="dashboard-body">
            <h2>Welcome, [Member Name]!</h2>

            <div class="announcement-container">
                <div class="announcement-title">
                    <h3>Announcements</h3>
                </div>
                <div class="announcement-content">
                    <p>Stay tuned for important updates!</p>
                    <ul>
                        <li>Meeting on July 20th, 2023</li>
                        <li>Annual Dues Deadline: August 15th, 2023</li>
                        <li>Community Clean-Up Event: September 10th, 2023</li>
                    </ul>
                </div>
            </div>

            <div class="graphs-container">
                <!-- Complaints Overview Section -->
                <div class="complaints-container">
                    <h3>Complaints Overview</h3>
                    <canvas id="complaintsPieChart"></canvas>
                </div>

                <!-- Financial Reports Section -->
                <div class="financial-container">
                    <h3>Financial Reports (2023)</h3>
                    <canvas id="financialBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Complaints Pie Chart
        const complaintsCtx = document.getElementById('complaintsPieChart').getContext('2d');
        const complaintsPieChart = new Chart(complaintsCtx, {
            type: 'pie',
            data: {
                labels: ['Maintenance', 'Noise', 'Security', 'Others'],
                datasets: [{
                    data: [300, 50, 100, 40],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                }]
            }
        });

        // Financial Bar Chart for 2023
        const financialCtx = document.getElementById('financialBarChart').getContext('2d');
        const financialBarChart = new Chart(financialCtx, {
            type: 'bar',
            data: {
                labels: [
                    'January', 'February', 'March', 'April', 'May', 
                    'June', 'July', 'August', 'September', 'October', 
                    'November', 'December'
                ],
                datasets: [{
                    label: 'Monthly Dues Collected (2023)',
                    data: [1200, 1900, 3000, 500, 1500, 2200, 2500, 3200, 2800, 3000, 3500, 4000],
                    backgroundColor: '#36A2EB',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
