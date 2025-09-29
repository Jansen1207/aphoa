<?php
require('config.php');
session_start();


$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$memberData = $result->fetch_assoc();


$complaints_sql = "SELECT * FROM complaints WHERE member_id = '{$_SESSION['member_id']}' ORDER BY created_at DESC";
$complaints_result = $conn->query($complaints_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Dashboard - Complaint</title>
    <link rel="stylesheet" href="./css/styles.css">
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
            display: none;
        }

        .sidebar ul ul li {
            margin-bottom: 0;
        }

        .sidebar ul li.active > ul {
            display: block;
        }




        .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.7);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}






    </style>
</head>
<body>
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
                    <h1 style="margin-bottom:1px;margin-top:1px;">My Complaint</h1>
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
            <section class="complaint-body">
                <div class="submit-complaint">
                    <h2>Submit a New Complaint</h2>
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
                        </select>
                        <label for="address">Address:</label>
                         <select type="text" id="address" name="address" required>
            <option value="">Select a street</option>
            <option value="Oriole St">Oriole St</option>
            <option value="Lark St">Lark St</option>
            <option value="BlackBird St">BlackBird St</option>
            <option value="Seagull St">Seagull St</option>
            <option value="Kingbird St">Kingbird St</option>
            <option value="Hornbill St">Hornbill St</option>
            <option value="Flamingo St">Flamingo St</option>
            <option value="Eagle St">Eagle St</option>
            <option value="Heron St">Heron St</option>
            <option value="Woodpecker St">Woodpecker St</option>
            <option value="Bluejay St">Bluejay St</option>
            <option value="Robin St">Robin St</option>
            <option value="Lovebird St">Lovebird St</option>
            <option value="Pelican St">Pelican St</option>
            <option value="Roadrunner St">Roadrunner St</option>
            <option value="Cardinal St">Cardinal St</option>
            <option value="Yellowbird St">Yellowbird St</option>
            <option value="Pintail St">Pintail St</option>
            <option value="Woodcock St">Woodcock St</option>
            <option value="Sparrow St">Sparrow St</option>
            <option value="Quail St">Quail St</option>
            <option value="Golden Plover St">Golden Plover St</option>
            <option value="Skylark St">Skylark St</option>
            <option value="Hummingbird St">Hummingbird St</option>
            <option value="Nighthawk St">Nighthawk St</option>
            <option value="Swan St">Swan St</option>
            <option value="Phoenix St">Phoenix St</option>
        </select>
                        <label for="house_number">House Number:</label>
                        <input type="text" id="house_number" name="house_number" required>
                        <label for="incident_date">Date of Incident:</label>
                        <input type="date" id="incident_date" name="incident_date" required>
                        <label for="supporting_docs">Supporting Documents/Images:</label>
                        <input type="file" id="supporting_docs" name="supporting_docs[]" multiple>
                        <button type="submit">Submit Complaint</button>
                    </form>
                </div>

                <div class="view-complaints">
                    <h3>Your Complaints</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Complaint ID</th>
                                <th>Title/Subject</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($complaint = $complaints_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $complaint['id']; ?></td>
                                <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                                <td><?php echo $complaint['created_at']; ?></td>
                                <td><?php echo htmlspecialchars($complaint['status']); ?></td>
                                <td>
                                    <button class="view-btn" data-id="<?php echo $complaint['id']; ?>">View</button>
                                    <button class="edit-btn" data-id="<?php echo $complaint['id']; ?>">Edit</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Complaint Details</h2>
        <div id="viewDetails"></div>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Complaint</h2>
        <form id="editForm" method="POST">
            <input type="hidden" name="complaint_id" id="complaint_id">
            <label for="edit_title">Title:</label>
            <input type="text" id="edit_title" name="title" required>
            <label for="edit_description">Description:</label>
            <textarea id="edit_description" name="description" required></textarea>
            <label for="edit_category">Category:</label>
            <select id="edit_category" name="category" required>
                <option value="maintenance">Maintenance</option>
                <option value="noise">Noise</option>
                <option value="security">Security</option>
                <option value="others">Others</option>
            </select>
            <label for="edit_address">Address:</label>
            <input type="text" id="edit_address" name="address" required>
            <label for="edit_incident_date">Date of Incident:</label>
            <input type="date" id="edit_incident_date" name="incident_date" required>
            <button type="submit">Update Complaint</button>
        </form>
    </div>
</div>


    <script>



document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const formData = new FormData(this);
    fetch('edit_complaint.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Complaint updated successfully!');
            location.reload(); 
        } else {
            alert('Error updating complaint. Please try again.');
        }
    });
});






        
        document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', function() {
        const complaintId = this.getAttribute('data-id');
        fetch(`get_complaint.php?id=${complaintId}`)
            .then(response => response.json())
            .then(data => {
                let commentSection = '';
                
                // Handle the 'comment' from the 'complaints' table
                if (data.comment) {
                    commentSection = `<h3>Officer Comment:</h3><p>${data.comment}</p>`;
                }

                // Handle additional comments from the 'complaint_comments' table
                if (data.comments && data.comments.length > 0) {
                    commentSection += '<h3>Additional Comments:</h3><ul>';
                    data.comments.forEach(comment => {
                        commentSection += `<li><strong>${comment.created_at}:</strong> ${comment.comment}</li>`;
                    });
                    commentSection += '</ul>';
                } else {
                    commentSection += '<p></p>';
                }

                // Display the details in the modal
                document.getElementById('viewDetails').innerHTML = `
                    <p><strong>Title:</strong> ${data.title}</p>
                    <p><strong>Description:</strong> ${data.description}</p>
                    <p><strong>Category:</strong> ${data.category}</p>
                    <p><strong>Address:</strong> ${data.address}</p>
                    <p><strong>Date of Incident:</strong> ${data.incident_date}</p>
                    <p><strong>Status:</strong> ${data.status}</p>
                    <p><strong>Date Submitted:</strong> ${data.created_at}</p>
                    ${data.files.length > 0 ? '<h3>Supporting Documents/Images:</h3><ul>' + data.files.map(file => `<li><a href="${file}" target="_blank">View File</a></li>`).join('') + '</ul>' : ''}
                    ${commentSection}  <!-- Displaying comments here -->
                `;
                document.getElementById('viewModal').style.display = 'block';
            });
    });
});



        
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const complaintId = this.getAttribute('data-id');
                fetch(`get_complaint.php?id=${complaintId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('complaint_id').value = data.id;
                        document.getElementById('edit_title').value = data.title;
                        document.getElementById('edit_description').value = data.description;
                        document.getElementById('edit_category').value = data.category;
                        document.getElementById('edit_address').value = data.address;
                        document.getElementById('edit_incident_date').value = data.incident_date;
                        document.getElementById('editModal').style.display = 'block';
                    });
            });
        });

        
        document.querySelectorAll('.close').forEach(span => {
            span.addEventListener('click', function() {
                this.parentElement.parentElement.style.display = 'none';
            });
        });

        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        };
    </script>
</body>
</html>