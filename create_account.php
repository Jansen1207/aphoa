<?php
require('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $memberId = $_POST['member_id'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo "Passwords do not match. Please go back and try again.";
        exit;
    }

    if ($conn->connect_error) {
        die("Connection Failed : " . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("UPDATE members SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password, $memberId);
        
        
        $execval = $stmt->execute();
        
        if ($execval === false) {
            echo "Error: " . $stmt->error;
        } else {
            if ($_SESSION['is_officer'] == 'y') {
            
            header("Location: member_management.php");
            exit;
            } else {
            
            header("Location: login.php");
            exit;              
            }

        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>

  <style>
    body {
      font-family: Arial, sans-serif;
      background-image: url('./images/bgaphoa.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center center;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      max-width: 350px;
      padding: 20px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 30px;
      background-color: rgba(249, 249, 249, 0.3);
      text-align: center;
    }

    .create-account-form {
      padding: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
      color: #333;
      font-family: Arial, sans-serif;
    }

    .form-control {
      width: 100%;
      height: 40px;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      font-family: Arial, sans-serif;
    }

    .btn {
      width: 100%;
      height: 40px;
      padding: 10px;
      font-size: 16px;
      background-color: #4CAF50;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-family: Arial, sans-serif;
    }

    .btn:hover {
      background-color: #3e8e41;
    }

   
    .password-container {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="create-account-form">
      <h2>Create Account</h2>
      <form action="" method="POST">
        <div class="form-group">
          <label for="membershipNO">Membership No.</label>
          <input type="text" id="membershipNO" name="membershipNO" class="form-control" placeholder="Enter membership no." value="<?php echo (isset($_SESSION['membership_no']) ? $_SESSION['membership_no'] : ''); ?>" disabled="disabled">
          <?php if (isset($_SESSION['new_member_id'])) { ?>
          <input type="hidden" id="member_id" name="member_id" value="<?php echo $_SESSION['new_member_id']; ?>">
          <?php } ?>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-container">
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
          </div>
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <div class="password-container">
            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm password" required>
            <span class="toggle-password" onclick="togglePassword('confirmPassword')">👁️</span>
          </div>
        </div>
        <button type="submit" class="btn">Create Account</button>
      </form>
    </div>
  </div>

  <script>
    function togglePassword(fieldId) {
      const passwordField = document.getElementById(fieldId);
      const toggleIcon = passwordField.nextElementSibling;

      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.textContent = '🔒'; 
      } else {
        passwordField.type = 'password';
        toggleIcon.textContent = '👁️'; 
      }
    }
  </script>


</body>
</html>
