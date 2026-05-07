<?php
session_start();
require "../config.php";

header("Content-Type: application/json");

// Must be logged in
if (!isset($_SESSION["userID"])) {
    echo json_encode([
        "status" => "error",
        "message" => "You must be logged in to adopt a pet."
    ]);
    exit;
}

$petID = intval($_POST["petID"]);
$userID = $_SESSION["userID"];
$fullName = $_POST["fullName"];
$contact = $_POST["contactNumber"];
$email = $_POST["email"];
$payConfirm = isset($_POST["payConfirm"]) ? 1 : 0;
$status = "Pending";

// Insert adoption record
$stmt = $conn->prepare("
    INSERT INTO adoption 
    (petID, userID, fullName, contactNumber, email, payConfirm, status)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("iisssis", $petID, $userID, $fullName, $contact, $email, $payConfirm, $status);

if ($stmt->execute()) {
    $update = $conn->prepare("UPDATE pets SET status = 'Adoption Pending' WHERE petID = ?");
    $update->bind_param("i", $petID);
    $update->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Your adoption request has been submitted!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Something went wrong while submitting your request."
    ]);
}

