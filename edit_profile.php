<?php
require('config.php');
$memberId = $_GET['id'];
$sql = "SELECT * FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$memberId}'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $memberData = $result->fetch_assoc();
  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <title>Edit Profile</title>
      </head>
  <body>
      <h1>Edit Profile</h1>
      <form action="update_profile.php" method="POST">
          <input type="hidden" name="id" value="<?php echo $memberData['id']; ?>">
          <label for="first_name">First Name:</label>
          <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($memberData['first_name']); ?>">
          <br><br>
          <button type="submit">Save Changes</button>
      </form>
  </body>
  </html>
  <?php
} else {
  echo "No member found with this ID.";
}
?>