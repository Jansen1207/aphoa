<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #70acb4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            margin-top: 10px;
        }
        .submit-button {
            background-color: #4CAF50;
            color: white;
        }
        .submit-button:hover {
            background-color: #45a049;
        }
        .cancel-button {
            background-color: #ccc;
            color: black;
            text-align: center;
            text-decoration: none;
        }
        .cancel-button:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Find your account</h2>
        <form action="submit_form.php" method="post">
            <label for="membershipNumber">Please enter your Membership Number to search for your account:</label>
            <input type="text" id="membershipNumber" name="membershipNumber" placeholder="Membership No." required>
            <button type="submit" class="submit-button">Submit</button>
            <button type="button" class="cancel-button" onclick="window.location.href='login.php'">Cancel</button>
        </form>
    </div>
</body>
</html>

