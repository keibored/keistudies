<?php
session_start();
require "../config.php";

// Make sure only Admin can approve
if (!isset($_SESSION["UserName"]) || $_SESSION["UserName"] !== "Admin") {
    die("Access denied. Only Admins can approve pets.");
}

// Check if petID is sent
if (!isset($_POST["petID"])) {
    die("Error: No pet ID received.");
}

$petID = $_POST["petID"];

// Update status from "Upload Pending" to "Available"
$sql = "UPDATE pets SET status = 'Available' WHERE petID = ? AND status = 'Upload Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $petID);

if ($stmt->execute()) {
    // Redirect back to pending page with a success flag
    header("Location: ../pending-pets.php?approved=1");
    exit();
} else {
    echo "Database error: " . $stmt->error;
}
?>
