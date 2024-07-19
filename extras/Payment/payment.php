<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Submission</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            background-image: url('bgaphoa.jpg');
            background-size: cover; /* Ensures the image covers the entire background */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            background-position: center center; /* Centers the image */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
            display: flex;
            flex-direction: column; /* Column direction to allow footer positioning */
            justify-content: center;
            align-items: center;
            height: 90vh;
        }

        .container {
            margin-top: 50px;
            padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3); /* Semi-transparent border */
        border-radius: 30px;
        background-color: rgba(249, 249, 249, 0.3); /* Semi-transparent background */
        text-align: center;
        }

        form {
            display: inline-block;
            text-align: left;
        }

        label, input, select, button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        button {
            width: auto;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 200px;
        }
    </style>
</head>
<body>

<?php
// Define variables and initialize with empty values
$user_id = $amount = $payment_method = $proof_of_payment = "";
$user_id_err = $amount_err = $payment_method_err = $proof_of_payment_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate User ID
    if (empty(trim($_POST["user_id"]))) {
        $user_id_err = "Please enter User ID.";
    } else {
        $user_id = trim($_POST["user_id"]);
    }

    // Validate Amount
    if (empty(trim($_POST["amount"]))) {
        $amount_err = "Please enter Amount.";
    } else {
        $amount = trim($_POST["amount"]);
    }

    // Validate Payment Method
    if (empty(trim($_POST["payment_method"]))) {
        $payment_method_err = "Please select Payment Method.";
    } else {
        $payment_method = trim($_POST["payment_method"]);
    }

    // Validate Proof of Payment
    if (empty($_FILES["proof_of_payment"]["name"])) {
        $proof_of_payment_err = "Please upload Proof of Payment.";
    } else {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["proof_of_payment"]["name"]);
        if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
            $proof_of_payment = $target_file;
        } else {
            $proof_of_payment_err = "Sorry, there was an error uploading your file.";
        }
    }

    // If no errors, proceed with database insertion and notifications
    if (empty($user_id_err) && empty($amount_err) && empty($payment_method_err) && empty($proof_of_payment_err)) {
        require 'process_payment.php'; // Include your payment processing logic
    }
}
?>

<div class="container">
    <img src="anakpewes.png" alt="Anak Pawis Logo" class="logo">
    <h2>Payment </h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group <?php echo (!empty($user_id_err)) ? 'has-error' : ''; ?>">
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" value="<?php echo $user_id; ?>" required>
            <span class="help-block"><?php echo $user_id_err; ?></span>
        </div>

        <div class="form-group <?php echo (!empty($amount_err)) ? 'has-error' : ''; ?>">
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" value="<?php echo $amount; ?>" required>
            <span class="help-block"><?php echo $amount_err; ?></span>
        </div>

        <div class="form-group <?php echo (!empty($payment_method_err)) ? 'has-error' : ''; ?>">
            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
               
                <option value="Online Bank Transfer" <?php echo ($payment_method == 'Online Bank Transfer') ? 'selected' : ''; ?>>Online Bank Transfer</option>
                <option value="Onsite Payment" <?php echo ($payment_method == 'Onsite Payment') ? 'selected' : ''; ?>>Onsite Payment</option>
            </select>
            <span class="help-block"><?php echo $payment_method_err; ?></span>
        </div>

        <div class="form-group <?php echo (!empty($proof_of_payment_err)) ? 'has-error' : ''; ?>">
            <label for="proof_of_payment">Proof of Payment:</label>
            <input type="file" id="proof_of_payment" name="proof_of_payment" required>
            <span class="help-block"><?php echo $proof_of_payment_err; ?></span>
        </div>

        <button type="submit">Submit Payment</button>
    </form>
</div>

</body>
</html>
