<?php
session_start();
require "../config.php";

header("Content-Type: application/json");

if (!isset($_SESSION["userID"])) {
    echo json_encode([
        "status" => "error",
        "message" => "You must be logged in to cancel an adoption request."
    ]);
    exit;
}

if (!isset($_POST["adoptID"]) || !is_numeric($_POST["adoptID"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid adoption request."
    ]);
    exit;
}

$adoptID = intval($_POST["adoptID"]);
$userID = intval($_SESSION["userID"]);

// Get the adoption entry to retrieve petID
$get = $conn->prepare("SELECT petID FROM adoption WHERE adoptID = ? AND userID = ?");
$get->bind_param("ii", $adoptID, $userID);
$get->execute();
$result = $get->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Adoption request not found."
    ]);
    exit;
}

$adoption = $result->fetch_assoc();
$petID = $adoption["petID"];

$delete = $conn->prepare("DELETE FROM adoption WHERE adoptID = ?");
$delete->bind_param("i", $adoptID);

if ($delete->execute()) {

    $update = $conn->prepare("UPDATE pets SET status = 'Available' WHERE petID = ?");
    $update->bind_param("i", $petID);
    $update->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Adoption request canceled successfully."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to cancel adoption request."
    ]);
}
?>
