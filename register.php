<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('config.php');
require('initialize_dues.php');
session_start();
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $house_number = $_POST['house_number'] ?? null;
    $street = $_POST['street'] ?? null;
    if (empty($house_number) || empty($street)) {
        die("House number and street are required.");
    }
    $address = $house_number . '-' . $street;
    $membership_no = $_POST['membership_no'] ?? null;
    $last_name = $_POST['last_name'] ?? null;
    $first_name = $_POST['first_name'] ?? null;
    $middle_name = $_POST['middle_name'] ?? null;
    $contact_no = $_POST['contact_no'] ?? null;
    $age = $_POST['age'] ?? null;
    $email_address = $_POST['email_address'] ?? null;
    $occupation = $_POST['occupation'] ?? null;
    $educ_attainment = $_POST['educ_attainment'] ?? null;
    $birthdate = $_POST['birthdate'] ?? null;
    $date_submitted = $_POST['date_submitted'] ?? null;
    $sex = $_POST['sex'] ?? null;
    $civil_status = $_POST['civil_status'] ?? null;
    $homeowner_status = $_POST['homeowner_status'] ?? null;
    $dos_status = $_POST['dos_status'] ?? null; 
    $length_of_stay = $_POST['length_of_stay'] ?? null;
    $owner_name = $_POST['owner_name'] ?? null;
    if (empty($membership_no) || empty($last_name) || empty($first_name)) {
        die("Membership number, last name, and first name are required.");
    }
    $stmt = $conn->prepare("INSERT INTO members (membership_no) VALUES (?)");
    $stmt->bind_param("s", $membership_no);
    if (!$stmt->execute()) {
        die("Error inserting member: " . $stmt->error);
    }
    
    $stmt = $conn->prepare("SELECT id FROM members WHERE membership_no = ?");
    $stmt->bind_param("s", $membership_no);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $memberData = $result->fetch_assoc();
        $_SESSION['new_member_id'] = $memberData['id'];
        $_SESSION['membership_no'] = $membership_no;
    } else {
        die("No results found.");
    }
    $stmt = $conn->prepare("INSERT INTO information (member_id, last_name, first_name, middle_name, contact_no, age, email_address, occupation, address, educ_attainment, birthdate, date_submitted, sex, civil_status, homeowner_status, dos_status, length_of_stay, owner_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssssssssis", 
        $memberData['id'], 
        $last_name, 
        $first_name, 
        $middle_name, 
        $contact_no, 
        $age,
        $email_address, 
        $occupation, 
        $address, 
        $educ_attainment, 
        $birthdate, 
        $date_submitted, 
        $sex, 
        $civil_status, 
        $homeowner_status, 
        $dos_status, 
        $length_of_stay, 
        $owner_name
    );
    if (!$stmt->execute()) {
        die("Error inserting information: " . $stmt->error);
    }
    
    initializeNewMemberDues($conn, $memberData['id']); // Call this function to initialize dues for the new member
    
    $dateSubmitted = DateTime::createFromFormat('Y-m-d', $date_submitted);
    $currentYear = $dateSubmitted->format('Y');
    $currentMonth = $dateSubmitted->format('n');
    /*$totalMonths = (int)$length_of_stay * 12;*/
    $dueStmt = $conn->prepare("INSERT INTO dues (member_id, month, year, amount) VALUES (?, ?, ?, ?)");
    $dueStmt->bind_param("iiid", $memberData['id'], $currentMonth, $currentYear, $dueAmount);
    for ($i = 0; $i < $totalMonths; $i++) {
    
    
        $dueAmount = 40.00;
        if (!$dueStmt->execute()) {
            echo "Error creating dues: " . $dueStmt->error;
            exit;
        }
        $date = new DateTime();
        $date->setDate($currentYear, $currentMonth, 1);
        $date->modify('+1 month');
        $currentYear = $date->format('Y');
        $currentMonth = $date->format('n');
    }
    $occupantInsertValues = [];
    for ($i = 1; $i <= 10; $i++) {
        if (!empty($_POST["occupant_name_$i"])) {
            $occupant_name = $_POST["occupant_name_$i"];
            $occupant_age = $_POST["occupant_age_$i"];
            $occupant_relationship = $_POST["occupant_relationship_$i"];
            $occupantInsertValues[] = "('" . $memberData['id'] . "', '$occupant_name', '$occupant_age', '$occupant_relationship')";
        }
    }
    if (count($occupantInsertValues) > 0) {
        $occupantInsertQuery = "INSERT INTO occupants (member_id, name, age, relationship) VALUES " . implode(',', $occupantInsertValues);
        if (!$conn->query($occupantInsertQuery)) {
            die("Error inserting occupants: " . $conn->error);
        }
    }
    header("Location: create_account.php?success=true");
    exit;
    $stmt->close();
    $dueStmt->close();
    $conn->close();
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
            max-width: 80%;
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
                    <h2>Part 1. Personal Information</h2>
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
                        <td>Age:</td>
                        <td><input type="text" name="age"></td>
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
    <td>House Number:</td>
    <td><input type="text" name="house_number" required></td>
</tr>
<tr>
    <td>Street:</td>
    <td>
        <select name="street" required>
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
    </td>
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
                        <th>LOT STATUS</th>
                        <th>LENGTH OF STAY IN ANAK-PAWIS (IN YEARS)</th>
                        <th>NAME OF OWNER</th>
                    </tr>
                    <tr>
                        <td>
                            <div style="display: inline-block; margin-right: 20px;">
                                <input type="radio" id="homeowner" name="homeowner_status" value="owner" required>
                                <label for="homeowner" style="font-size: 13px;">HOMEOWNER</label>
                            </div>
                            <div style="display: inline-block;">
                                <input type="radio" id="sharer" name="homeowner_status" value="sharer" required>
                                <label for="sharer" style="font-size: 13px;">SHARER</label>
                            </div>
                        </td>
                        <td>
                            <div style="display: inline-block;">
                                <input type="radio" id="with_title" name="dos_status" value="with" required>
                                <label for="with_title" style="font-size: 13px;">With Title</label>
                            </div>
                            <div style="display: inline-block;">
                                <input type="radio" id="without_deed" name="dos_status" value="without" required>
                                <label for="without_deed" style="font-size: 13px;">Without Title</label>
                            </div>
                        </td>
                        <td><input type="text" name="length_of_stay" required></td>
                        <td><input type="text" name="owner_name" required></td>
                    </tr>
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
                    <?php for ($i = 1; $i <= 11; $i++): ?>
                    <tr>
                        <td><input type="text" name="occupant_name_<?php echo $i; ?>"></td>
                        <td><input type="text" name="occupant_age_<?php echo $i; ?>"></td>
                        <td><input type="text" name="occupant_relationship_<?php echo $i; ?>"></td>
                    </tr>
                    <?php endfor; ?>
                </table>
            </div>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>