<?php
require('config.php');

// SQL query to fetch announcements
$sql = "SELECT title, message, DATE_FORMAT(created_at, '%M %e, %Y') AS formatted_date, member_id FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    ?> 
    <div class="announcement-title">
    <h3 style="margin: 3px;">Announcements</h3>

    </div>
    <div class="announcement-content">
        <ul class="announcement-list">
            <?php
            while ($row = $result->fetch_assoc()) {
                $memberID = $row['member_id'];
                $sql2 = "SELECT * FROM information WHERE member_id = '$memberID'";
                $result2 = $conn->query($sql2);
                $membersData = $result2->fetch_assoc(); 
                ?>
                <br>
                <li class="announcement-item">
                    <div class="announcement-details">

                        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                        <p><?php echo htmlspecialchars($row['message']); ?></p>
                        <p><b>Posted by: <?php echo $membersData['first_name'] . ' ' . $membersData['last_name']; ?> </p></b>

                    </div>
                    <div class="announcement-date">
                        <p><?php echo htmlspecialchars($row['formatted_date']); ?></p>
                    </div>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
    <?php
} else {
    ?>
    <div class="announcement-title">
        <h3>Announcements</h3>
    </div>
    <div class="announcement-content">
        <p>No announcements yet.</p>
    </div>
    <?php
}

// Close database connection
$conn->close();
?>
