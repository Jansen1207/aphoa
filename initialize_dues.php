<?php
function initializeNewMemberDues($conn, $memberId) {
    // Delete any existing dues records for this member (just in case)
    $deleteSql = "DELETE FROM dues WHERE member_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $memberId);
    $deleteStmt->execute();

    // Initialize all months for years 2019-2025 as unpaid
    for ($year = 2019; $year <= 2025; $year++) {
        for ($month = 1; $month <= 12; $month++) {
            $insertSql = "INSERT INTO dues (member_id, month, year, amount, status) 
                         VALUES (?, ?, ?, 40, 'unpaid')";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iii", $memberId, $month, $year);
            $insertStmt->execute();
        }
    }
}

// Usage: Call this function right after creating a new member account
// Example:
// require('initialize_dues.php');
// initializeNewMemberDues($conn, $newMemberId);
?>