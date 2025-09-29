<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['payment_id']) || !isset($_POST['action'])) {
        die("Invalid request.");
    }

    $payment_id = $_POST['payment_id'];
    $action = $_POST['action'];

    if ($action !== 'approve' && $action !== 'reject') {
        die("Invalid action.");
    }

    $status = $action === 'approve' ? 'approved' : 'declined';
    $approved_by = $_SESSION['member_id']; 
    $approved_at = date('Y-m-d H:i:s');

    $updatePaymentSql = "UPDATE payments 
                         SET status = ?, approved_by = ?, approved_at = ? 
                         WHERE id = ?";

    if ($stmt = $conn->prepare($updatePaymentSql)) {
        $stmt->bind_param('sssi', $status, $approved_by, $approved_at, $payment_id);

        if ($stmt->execute()) {
            if ($action === 'approve') {
                
                $selectSql = "SELECT member_id, month, year FROM payments WHERE id = ?";
                if ($selectStmt = $conn->prepare($selectSql)) {
                    $selectStmt->bind_param('i', $payment_id);
                    $selectStmt->execute();
                    $selectStmt->bind_result($member_id, $month, $year);
                    $selectStmt->fetch();
                    $selectStmt->close();

                    
                    $updateDueSql = "UPDATE dues 
                                     SET status = 'paid' 
                                     WHERE member_id = ? AND month = ? AND year = ?";

                    if ($updateStmt = $conn->prepare($updateDueSql)) {
                        $updateStmt->bind_param('iii', $member_id, $month, $year);

                        if ($updateStmt->execute()) {
                            echo "Due table updated successfully for month $month of year $year.<br>";
                        } else {
                            die("Error updating due table: " . $updateStmt->error);
                        }
                        $updateStmt->close();
                    } else {
                        die("Error preparing due update statement: " . $conn->error);
                    }
                } else {
                    die("Error preparing payment select statement: " . $conn->error);
                }
            }

            header('Location: monthly_dues_officer.php');
            exit();
        } else {
            die("Error executing payment update statement: " . $stmt->error);
        }
    } else {
        die("Error preparing payment update statement: " . $conn->error);
    }
}
?>
