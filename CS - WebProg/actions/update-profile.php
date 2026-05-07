<?php
session_start();
require "../config.php";
header("Content-Type: application/json");

ini_set("display_errors", 1);
error_reporting(E_ALL);

// Check login
if (!isset($_SESSION["userID"])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$userID = $_SESSION["userID"];
$display = $_POST["display_name"];
$password = $_POST["new_password"];
$confirm = $_POST["confirm_password"];

// Password mismatch
if (!empty($password) && $password !== $confirm) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// NO PASSWORD CHANGE → Only update DisplayName
if (empty($password)) {

    $stmt = $conn->prepare("UPDATE users SET DisplayName = ? WHERE UserID = ?");
    $stmt->bind_param("si", $display, $userID);

} else {

    // WITH PASSWORD CHANGE
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET DisplayName = ?, Password = ? WHERE UserID = ?");
    $stmt->bind_param("ssi", $display, $hash, $userID);
}

if ($stmt->execute()) {

    // Update session name so profile displays immediately
    $_SESSION["DisplayName"] = $display;

    echo json_encode(["status" => "success", "message" => "Profile updated"]);
} 
else {
    echo json_encode(["status" => "error", "message" => "Database error"]);
}
?>
