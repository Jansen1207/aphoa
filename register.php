<?php
require('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection    
    if ($conn->connect_error) {
        die("Connection Failed : " . $conn->connect_error);
    }

    // Prepare data for insertion (assuming the same column names as before)
    $membership_no = $_POST['membership_no'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? null;
    $contact_no = $_POST['contact_no'] ?? null;
    $email_address = $_POST['email_address'] ?? null;
    $occupation = $_POST['occupation'] ?? null;
    $address = $_POST['address'] ?? null;
    $educ_attainment = $_POST['educ_attainment'] ?? null;
    $birthdate = $_POST['birthdate'] ?? null;
    $date_submitted = $_POST['date_submitted'] ?? null;
    $sex = $_POST['sex'] ?? null;
    $civil_status = $_POST['civil_status'] ?? null;
    $homeowner_status = $_POST['homeowner_status'] ?? null;
    $type = $_POST['type'] ?? null;
    $length_of_stay = $_POST['length_of_stay'] ?? null;
    $owner_name = $_POST['owner_name'] ?? null;

    $occupantData = $_POST['occupant'];

    echo '<pre>';
    print_r($occupantData);
    exit;

    $stmt = $conn->prepare("INSERT INTO members (membership_no) VALUES (?)");
    $stmt->bind_param("s", $membership_no);  
    $stmt->execute();

    $sql = "select id from members where membership_no = '$membership_no'";
    $result = $conn->query($sql);
    // Check if any results were returned
    if ($result && $result->num_rows > 0) {        
        $memberData = $result->fetch_assoc();
        $_SESSION['member_id'] = $memberData['id'];
        $_SESSION['membership_no'] = $membership_no;
    } else {
        echo "No results found.";
    }    

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO information (member_id, last_name, first_name, middle_name, contact_no, email_address, occupation, address, educ_attainment, birthdate, date_submitted, sex, civil_status, homeowner_status, type, length_of_stay, owner_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssssiss", $memberData['id'], $last_name, $first_name, $middle_name, $contact_no, $email_address, $occupation, $address, $educ_attainment, $birthdate, $date_submitted, $sex, $civil_status, $homeowner_status, $type, $length_of_stay, $owner_name);
    $stmt->execute();

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO occupant (member_id, name, age, relationship) VALUES (?, ?, ?, ?)");

    foreach($occupantData as $k => $v) {
        $stmt->bind_param("ssss", $memberData['id'], $v['name'], $v['age'], $v['relationship']);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    // Redirect back to createac.php with success message
    header("Location: create_account.php?success=true");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANAK-PAWIS HOMEOWNERS' ASSOCIATION (APHOA), INC. Membership Form</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #70acb4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            text-align: center;
            font-family: 'Arial Black', sans-serif;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        td input, td select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        td input[type="radio"], td input[type="checkbox"] {
            width: auto;
            margin-right: 5px;
        }
        .form-section {
            margin-bottom: 20px;
        }
        .form-section h2 {
            font-size: 18px;
            color: #666;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .form-section table:first-child {
            margin-top: 0;
        }
        .form-section table:last-child {
            margin-bottom: 0;
        }
        .logo {
            display: block;
            margin: -5px auto;
            max-width: 80%; /* Adjust the size of the logo */
        }
        h1 {
            font-size: 30px;
            margin-bottom: -5px;
        }
       
    </style>
</head>
<body>
    <div class="container">
        <h1>ANAK-PAWIS HOMEOWNERS' ASSOCIATION (APHOA), INC. 
            <br> 
            <br> Membership Form</h1>
        <img src="./images/anak.png" alt="Anak Pawis Logo" class="logo">
        <form action="" method="POST">
            <div class="form-section">
                <h2>Membership Information</h2>
                <table>
                	<br> <h2>Part 1. Personal Information</h2>
                    <tr>
                        <td>Membership No:</td>
                        <td><input type="text" name="membership_no" required></td>
                    </tr>
                    <tr>
                        <td>Last Name:</td>
                        <td><input type="text" name="last_name" required></td>
                    </tr>
                    <tr>
                        <td>First Name:</td>
                        <td><input type="text" name="first_name" required></td>
                    </tr>
                    <tr>
                        <td>Middle Name:</td>
                        <td><input type="text" name="middle_name"></td>
                    </tr>
                    <tr>
                        <td>Contact No(s):</td>
                        <td><input type="text" name="contact_no"></td>
                    </tr>
                    <tr>
                        <td>Email Address:</td>
                        <td><input type="email" name="email_address"></td>
                    </tr>
                    <tr>
                        <td>Occupation:</td>
                        <td><input type="text" name="occupation"></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td><input type="text" name="address"></td>
                    </tr>
                    <tr>
                        <td>Educational Attainment:</td>
                        <td><input type="text" name="educ_attainment"></td>
                    </tr>
                    <tr>
                        <td>Birthdate:</td>
                        <td><input type="date" name="birthdate"></td>
                    </tr>
                    <tr>
                        <td>Date Submitted:</td>
                        <td><input type="date" name="date_submitted"></td>
                    </tr>
                </table>
            </div>

            <div class="form-section">
                
                <table>
                    <tr>
                        <td>Sex:</td>
                        <td>
                            <input type="radio" name="sex" value="male"> Male
                            <input type="radio" name="sex" value="female"> Female
                        </td>
                    </tr>
                    <tr>
                        <td>Civil Status:</td>
                        <td>
                            <input type="radio" name="civil_status" value="single"> Single
                            <input type="radio" name="civil_status" value="married"> Married
                            <input type="radio" name="civil_status" value="separated"> Separated
                            <input type="radio" name="civil_status" value="single_parent"> Single Parent
                            <input type="radio" name="civil_status" value="widow(er)"> Widow(er)
                            <input type="radio" name="civil_status" value="live_in"> Live-in
                            <input type="radio" name="civil_status" value="others"> Others
                        </td>
                    </tr>
                </table>
            </div>

            <div class="form-section">
                <h2>Part 2. Homeowners' Status</h2>
                <table>
                    <tr>
                        <th>STATUS</th>
                        <th>TYPE</th>
                        <th>LENGTH OF STAY IN ANAK-PAWIS (IN YEARS)</th>
                        <th>NAME OF OWNER</th>
                    </tr>
                    <tr>
                        <td>
						<div style="display: inline-block; margin-right: 20px;">
    					<input type="radio" id="homeowner" name="homeowner_status" value="legitimate" required>
    					<label for="homeowner" style="font-size: 13px;">HOMEOWNER</label>
						</div>

						<div style="display: inline-block;">
    					<input type="radio" id="sharer" name="homeowner_status" value="associate" required>
    					<label for="sharer" style="font-size: 13px;">SHARER</label>
						</div>
                
                       	</td>
                        <td>
										<div style="display: inline-block;">
                            <input type="radio" name="type" value="with"> With Title
										<div style="display: inline-block;">
                            <input type="radio" name="type" value="without"> Without Deed Of Sale
                        </td>
						
                        <td><input ttype="text"  name="length_of_stay" required></td>
                        <td><input type="text" name="owner_name" required></td>
                    </tr>
                    <!-- Repeat rows for additional homeowners -->
                </table>
            </div>

            <div class="form-section">
                <h2>Part 3. Occupants' Status</h2>
                <table>
                    <tr>
                        <th>NAME</th>
                        <th>AGE</th>
                        <th>RELATIONSHIP</th>
                    </tr>
                    <!-- Repeat rows for occupants -->
                    <?php for($i=0; $i<12; $i++) { ?>
                    <tr>
                        <td><input type="text" name="occupant[<?php echo $i; ?>]['name']"></td>
                        <td><input type="text" name="occupant[<?php echo $i; ?>]['age']"></td>
                        <td><input type="text" name="occupant[<?php echo $i; ?>]['relationship']"></td>
                    </tr>                    
                    <?php } ?>
                    <!-- Repeat rows for additional occupants -->
                </table>
            </div>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
