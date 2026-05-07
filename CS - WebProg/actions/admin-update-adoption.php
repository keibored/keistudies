<?php
session_start();
require "../config.php";

header("Content-Type: application/json");

// Ensure only admin can approve/reject
if (!isset($_SESSION["UserName"]) || $_SESSION["UserName"] !== "Admin") {
    echo json_encode(["status" => "error", "message" => "Unauthorized."]);
    exit;
}

if (!isset($_POST["action"], $_POST["adoptID"])) {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}

$action = $_POST["action"];
$adoptID = intval($_POST["adoptID"]);

// Get petID
$stmt = $conn->prepare("SELECT petID FROM adoption WHERE adoptID = ?");
$stmt->bind_param("i", $adoptID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Adoption request not found."]);
    exit;
}

$petID = $result->fetch_assoc()["petID"];

// Handle Approve / Reject
if ($action === "approve") {
    $newAdoptStatus = "Approved";
    $newPetStatus = "Adopted";
} else {
    $newAdoptStatus = "Rejected";
    $newPetStatus = "Available";
}

// Update adoption table
$update = $conn->prepare("UPDATE adoption SET status = ? WHERE adoptID = ?");
$update->bind_param("si", $newAdoptStatus, $adoptID);
$update->execute();

// Update pets table
$updatePet = $conn->prepare("UPDATE pets SET status = ? WHERE petID = ?");
$updatePet->bind_param("si", $newPetStatus, $petID);
$updatePet->execute();

echo json_encode([
    "status" => "success",
    "message" => "Adoption request has been $newAdoptStatus."
]);
