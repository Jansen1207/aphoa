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
    <title>QR Codes</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/qr_codes.css">
    <style>
       
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            text-align: center;
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
                <h1 style="margin-bottom:1px;margin-top:1px;">QR Codes</h1>
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
    <div class="chart-section">
        <h2>QR Code Generator</h2>
        <div class="chart-container">
            <form id="qrForm">
                <input type="text" id="name" placeholder="Name" required>
                <input type="text" id="address" placeholder="Address" required>
                <input type="date" id="birthday" placeholder="Birthday" required>
                <input type="text" id="contactNumber" placeholder="Contact Number" required>
                <input type="text" id="emergencyContactPerson" placeholder="Emergency Contact Person" required>
                <input type="text" id="emergencyContactNumber" placeholder="Emergency Contact Number" required>
                <select id="homeownerStatus" required>
                    <option value="">Select Homeowner Status</option>
                    <option value="owner">Owner</option>
                    <option value="sharer">Sharer</option>
                </select>
                <button type="button" onclick="generateQRCode()">Generate QR Code</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal for QR Code -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Your QR Code</h2>
        <canvas id="qrCode" width="200" height="200"></canvas>
    </div>
</div>

- <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
function generateQRCode() {
    const name = document.getElementById('name').value;
    const address = document.getElementById('address').value;
    const birthday = document.getElementById('birthday').value;
    const contactNumber = document.getElementById('contactNumber').value;
    const emergencyContactPerson = document.getElementById('emergencyContactPerson').value;
    const emergencyContactNumber = document.getElementById('emergencyContactNumber').value;
    const homeownerStatus = document.getElementById('homeownerStatus').value;

    if (name && address && birthday && contactNumber && emergencyContactPerson && emergencyContactNumber && homeownerStatus) {
        const qr = new QRious({
            element: document.getElementById('qrCode'),
            size: 200,
            value: `Name: ${name}\n| Address: ${address}\n| Birthday: ${birthday}\n| Contact: ${contactNumber}\n| Emergency Contact: ${emergencyContactPerson}\n| Emergency Number: ${emergencyContactNumber}\n| Homeowner Status: ${homeownerStatus}`
        });
        openModal();
    } else {
        alert('Please fill in all fields');
    }
}


    function openModal() {
        document.getElementById('qrModal').style.display = "block";
    }

    function closeModal() {
        document.getElementById('qrModal').style.display = "none";
    }

    
    window.onclick = function(event) {
        const modal = document.getElementById('qrModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
