<?php
session_start();
require "../config.php";

if (!isset($_SESSION["UserName"]) || $_SESSION["UserName"] !== "Admin") {
    die("Access denied.");
}

if (!isset($_POST["petID"])) {
    die("No pet ID provided.");
}

$petID = $_POST["petID"];

$sql = "UPDATE pets SET status = 'Rejected' WHERE petID = ? AND status = 'Upload Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $petID);
$stmt->execute();

header("Location: ../pending-pets.php?rejected=1");
exit();
?>
