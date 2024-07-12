<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-image: url('bgaphoa.jpg');
            background-size: cover; /* Ensures the image covers the entire background */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            background-position: center center; /* Centers the image */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 350px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 30px;
            background-color: #f9f9f9;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"], input[type="tel"], input[type="password"], textarea {
            width: 100%; /* Adjust width to fit the container */
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .password-container {
            position: relative;
        }
        .password-container input[type="password"] {
            padding-right: 30px;
        }
        .password-container .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .center {
            display: block;
            margin: 20px auto;
        }
        h1 {
            font-size: 30px;
            font-weight: bold;
            font-style: monospace;
        }
        .register-link {
            margin-top: 20px;
        }
        .register-link a {
            color: blue;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .forgot-password {
            margin-top: 10px;
        }
        .forgot-password a {
            color: blue;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ANAK PAWIS HOMEOWNERS' ASSOCIATION, INC.</h1>
        <img src="anak.png" alt="Anak Pawis Logo" class="center">
        <form action="register.php" method="POST">
            <label for="member"></label>
            <input type="text" id="member" name="member" placeholder="Membership No." required>
            
            <label for="password"></label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>
            
            <input type="submit" value="Log In">
        </form>
        <div class="forgot-password">
            <p><a href="forgot_password.html">Forgot password?</a></p>
        </div>
        <div class="register-link">
            <p>Not a member? <a href="register.html">Register here!</a></p>
        </div>

    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var passwordFieldType = passwordField.getAttribute("type");
            if (passwordFieldType === "password") {
                passwordField.setAttribute("type", "text");
            } else {
                passwordField.setAttribute("type", "password");
            }
        }
    </script>
</body>
</html>
