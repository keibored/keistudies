<?php
require "../config.php";
session_start();
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check user exists
    $hashedPass = "";
    $stmt = $conn->prepare("SELECT UserID, DisplayName, UserName, Password FROM users WHERE UserName=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status"=>"error","message"=>"Username not found"]);
        exit;
    }

    $stmt->bind_result($id, $display, $user, $hashedPass);
    $stmt->fetch();

    if (!password_verify($password, $hashedPass)) {
        echo json_encode(["status"=>"error","message"=>"Incorrect password"]);
        exit;
    }

    // Success
    $_SESSION["userID"] = $id;
    $_SESSION["UserName"] = $user;
    $_SESSION["DisplayName"] = $display;

    echo json_encode(["status"=>"success","message"=>"Login successful"]);
}

?>