<?php
require_once "../config.php"; 
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../profile.php");
    exit;
}

$userID = $_SESSION["userID"];

$stmt = $conn->prepare("DELETE FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userID);

if ($stmt->execute()) {
    session_unset();
    session_destroy();

    header("Location: ../index.php?deleted=1");
    exit;
} else {
    header("Location: ../profile.php?error=delete_failed");
    exit;
}
