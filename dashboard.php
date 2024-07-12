<?php
require('config.php');
session_start();

$sql = "select m.membership_no, i.* from members m 
inner join information i on m.id = i.member_id
where m.id = '{$_SESSION['member_id']}'";

$result = $conn->query($sql);
$memberData = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOA Dashboard</title>
    <style>
	
		
        body {
            font-family: Arial, sans-serif;
            
            
        }

        .sidebar {
            background-color: #144F05;
            color: #fff;
            width: 200px;
            height: 100vh;
            padding: 30px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h3 {
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #fff;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        .content {
            padding: 20px;
            margin-left: 220px;
            background-color: #f2f2f2;
        }

        .card {
            display: inline-block;
            margin: 10px;
            padding: 20px;
            border-radius: 8px;
            width: 200px;
            height: 150px;
            text-align: center;
        }

        .card h4 {
            margin-bottom: 10px;
        }

        .card svg {
            width: 40px;
            height: 40px;
        }

        .card-green {
            background-color: #4CAF50;
        }

        .card-red {
            background-color: #f44336;
        }

        .card-blue {
            background-color: #2196F3;
        }

        .card-yellow {
            background-color: #ffeb3b;
        }
		.logo {
            display: block;
            margin: 0 auto 20px;
            width: 130px;
        }
		
		.header{
            
           max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           
            
        }
		 .profile {
            
           max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           
            
        }
		
		.Announce {
            
           max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           
            
        }
    </style>
</head>
<body>
	
    <div class="sidebar">
		<img src="anak.png" alt="Anak Pawis Logo" class="logo">
        <h3>ANAK-PAWIS HOMEOWNERS' ASSOCIATION(APHOA), INC. </h3>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1h-6z"/>
                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
            </svg>
            Dashboard
        </a>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-check" viewBox="0 0 16 16">
                <path d="M10.854 7.854a.5.5 0 0 0-.708-.708L7.5 9.793 6.354 8.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
            </svg>
            Registration
        </a>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                <path d="M7 14s-1 0-1-1 1 0 1 1h5s1 0 1-1-1 0-1-1H7zm4-6s1 0 1-1-1 0-1 1H7s-1 0-1 1 1 0 1 1h4zm-8-2s-1 0-1-1 1 0 1 1h5s1 0 1-1-1 0-1-1H1zm5-6s1 0 1-1-1 0-1 1H7s-1 0-1 1 1 0 1 1h4z"/>
            </svg>
            Members
        </a>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                <path d="M15 14s1 0 1-1-1 0-1-1H2s-1 0-1 1 1 0 1 1h13zm-13-1A7 7 0 0 0 7 0c.6 0 1.2.072 1.793.186.783.13 1.546.292 2.246.516.701.224 1.316.57 1.798.953a7.029 7.029 0 0 0 2.171 1.064c.308.079.622.121.926.121s.618-.042.926-.121a7.027 7.027 0 0 0 2.171-1.064c.482-.383.997-.729 1.798-.953.701-.224 1.546-.292 2.246-.516.593-.114 1.193-.186 1.793-.186A7 7 0 0 0 16 9v5h-1V9a6 6 0 0 1-6 6H7a6 6 0 0 1-6-6z"/>
                <path d="M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
            </svg>
            Officers
        </a>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1 0 1 1h10s1 0 1-1-1 0-1-1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            </svg>
            Admin
        </a>
    </div>
	
	
	
	
	
<div class="main">
	
		<div class="header">
		     <table width="100%">
        <tbody><tr>
        <td>
        <h3 style="margin-bottom:1px;margin-top:1px;">Dashboard</h3>
        </td>

        <td align="right">
        <img src="menicon.png" width="50" height="50" style="border-radius: 50%;">
        </td>

        <td width="120">
        &nbsp;&nbsp;&nbsp;
        
            <form action="logout.php" method="POST">
            <select name="logout" onchange="this.form.submit()">
            <option ><?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']) ?></option>
            <option style="background-color: #337AB7;color:#fff;">Logout</option>
            </select>
            </form>
            
        </td>
        </tr>
        </tbody></table>
		</div>
	
        <div class="profile">
		  <table width="100%">
                    <tbody><tr>
                    <td align="left">
                    <h3 style="margin-bottom:1px;margin-top:1px;">Profile</h3>
                    </td>
                    <td align="right">
                    <a href="HOAprofile.php">View Profile &gt;</a>
                    </td>
                    </tr>
                    </tbody></table>
                    <hr>
                    <table width="100%" border="0">
                    <tbody><tr>
                    <td valign="center" width="160">
                    <center>
                    <img src="menicon.png" width="80" height="80" style="border-radius: 50%;">
                    </center></td>

                    <td valign="top">
  
                    <br>
                        <table id="table1">
                        <tbody><tr>
                        <th>
                        Membership No : <?php echo $memberData['membership_no']; ?>
                        <br />
                        Name: <?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']) ?>
                        </th>
						
				</tr>
                        </tbody></table>

                    </td>
                </tr>
                </tbody></table>
      </div>
	
	
        <div class="Announce">
		<table width="100%">
                    <tbody><tr>
                    <td align="left">
                    <h3 style="margin-bottom:1px;margin-top:1px;">Announcement / Event</h3>
                    </td>
                    <td align="right">
                    <a href="">View All &gt;</a>
                    </td>
                    </tr>
                    </tbody></table>
                    <hr>
		</div>
		
</div>	
	
</body>
</html>