<?php
    session_start(); // Start the session for storing user login status

    // Retrieve form data
    $membership_no = $_POST['member'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'aphoadb');
    if($conn->connect_error){
        die("Connection Failed : ". $conn->connect_error);
    } else {
        // Prepare SQL query to fetch user based on membership number and password
        $stmt = $conn->prepare("SELECT id, membership_no, password FROM members WHERE membership_no = ? AND password = ?");
        $stmt->bind_param("ss", $membership_no, $password);
        $stmt->execute();
        $stmt->store_result();

        // Check if user exists
        if($stmt->num_rows > 0) {
            // User exists, set session variables and redirect to dashboard
            $_SESSION['membership_no'] = $membership_no; // Store membership number in session
            header("Location: dashboard.php");
            exit();
        } else {
            // User does not exist or incorrect credentials
            echo "Login failed. Please check your membership number and password.";
        }

        $stmt->close();
        $conn->close();
    }
?>
<?php
session_start();

// Database connection details
$servername = "localhost";  // Change this if your MySQL server is hosted elsewhere
$username = "root";         // Your MySQL username
$password_db = "";          // Your MySQL password
$dbname = "aphoadb";        // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs (you should enhance this based on your specific validation needs)
    $membership_no = htmlspecialchars($_POST['member']);
    $password = $_POST['password'];

    // Insert registration data into 'registration' table
    $stmt = $conn->prepare("INSERT INTO registration (membership_no, password) VALUES (?, ?)");
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    $stmt->bind_param("ss", $membership_no, $hashed_password);

    if ($stmt->execute()) {
        // Registration successful, proceed with login validation
        // Prepare SQL statement to fetch hashed password from 'members' table
        $stmt_login = $conn->prepare("SELECT password FROM members WHERE membership_no = ?");
        $stmt_login->bind_param("s", $membership_no);

        // Execute the statement
        $stmt_login->execute();

        // Bind result variables
        $stmt_login->bind_result($hashed_password_db);

        // Fetch the password hash
        if ($stmt_login->fetch()) {
            // Verify password
            if (password_verify($password, $hashed_password_db)) {
                // Password correct, set session variables and redirect
                $_SESSION['membership_no'] = $membership_no;
                header("Location: dashboard.php"); // Redirect to dashboard or member area
                exit();
            } else {
                // Password incorrect
                echo "Login failed. Incorrect password.";
            }
        } else {
            // Membership number not found
            echo "Login failed. Membership number not found.";
        }

        // Close login statement
        $stmt_login->close();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close registration statement and connection
    $stmt->close();
}

// Close connection
$conn->close();
?>
