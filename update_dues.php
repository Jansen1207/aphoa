<?php
// Include database connection
include('config.php');  // Make sure this path is correct to where your DB connection is defined

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and capture form inputs
    $year = $_POST['year']; // Year selected in the form
    $set_amount = $_POST['set_amount']; // Set amount inputted by the user

    // Ensure year and set_amount are provided
    if (!empty($year) && !empty($set_amount)) {
        // Prepare the update SQL statement
        $update_sql = "UPDATE dues SET amount = ? WHERE year = ?";
        

        // Check if the statement is prepared correctly
        if ($stmt = $conn->prepare($update_sql)) {
            // Bind parameters to the prepared statement
            $stmt->bind_param("di", $set_amount, $year);  // "d" is for double (set_amount), "i" is for integer (year)
            
            // Execute the statement
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Monthly dues updated successfully!";
                } else {
                    echo "No changes made. Please check if the year exists or if the amount is the same.";
                }
            } else {
                echo "Error executing the update query: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error preparing statement
            echo "Error preparing the update query: " . $conn->error;
        }
    } else {
        echo "Please fill in both the year and the set amount.";
    }
} else {
    echo "Invalid request method.";
}

// Close the database connection if it's open
$conn->close();
?>
