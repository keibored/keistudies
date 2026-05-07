<?php
require "../config.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $display = $_POST["display_name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    if ($password !== $confirm) {
        echo json_encode([
            "status" => "error",
            "message" => "Passwords do not match"
    ]);
    exit;
}

    // Check username availability
    $check = $conn->prepare("SELECT UserName FROM users WHERE UserName = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["status"=>"error","message"=>"Username already taken"]);
        exit;
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (DisplayName, UserName, Password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $display, $username, $hash);

    if ($stmt->execute()) {
        echo json_encode(["status"=>"success","message"=>"Account created successfully"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Database error"]);
    }
}
?>
