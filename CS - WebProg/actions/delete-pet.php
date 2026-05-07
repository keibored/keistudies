<?php
session_start();
require "../config.php";

// User must be logged in
if (!isset($_SESSION["userID"])) {
    die("Access denied.");
}


$isAdmin = isset($_SESSION["UserName"]) && $_SESSION["UserName"] === "Admin";
$userID = $_SESSION["userID"];

// Ensure petID was sent
if (!isset($_POST["petID"]) && !isset($_GET["id"])) {
    die("Error: No pet ID provided.");
}

$petID = isset($_POST["petID"]) ? $_POST["petID"] : $_GET["id"];

if ($isAdmin) {
    $sql = "DELETE FROM pets WHERE petID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $petID);

} else {
    $sql = "DELETE FROM pets WHERE petID = ? AND userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $petID, $userID);
}

if ($stmt->execute()) {

    if ($isAdmin) {
        header("Location: ../pet-list.php?deleted=1");
        exit();
    }

    header("Location: ../pending-pets.php?deleted=1");
    exit();

} else {
    echo "Database error: " . $stmt->error;
}
?>
