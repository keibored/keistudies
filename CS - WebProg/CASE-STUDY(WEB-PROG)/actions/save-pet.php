<?php
require "../config.php";
session_start();

// Ensure user is logged in
if (!isset($_SESSION["userID"])) {
    header("Location: ../add-pet.php?login_required=true");
    exit();
}

$userID = $_SESSION["userID"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form data
    $petName       = $_POST["pet-name"];
    $animalType    = $_POST["pet-type"];
    $petBreed      = $_POST["pet-breed"];
    $gender        = $_POST["pet-gender"];
    $age           = $_POST["pet-age"];
    $birthday      = $_POST["pet-birthdate"];
    $size          = $_POST["pet-size"];
    $weight        = $_POST["pet-weight"];
    $color         = $_POST["pet-color"];
    $temperament   = $_POST["pet-temperament"];
    $goodwith      = $_POST["pet-goodw"];
    $health        = $_POST["pet-health"];
    $description   = $_POST["pet-description"];
    $status        = "Upload Pending"; // default status

    // Handle uploaded image
    $imageFile = $_FILES["pet-image"];
    $imageName = time() . "-" . basename($imageFile["name"]);
    $targetDir = "../images/imageupload/";
    $targetFile = $targetDir . $imageName;

    move_uploaded_file($imageFile["tmp_name"], $targetFile);

    // Insert into database
    $sql = "INSERT INTO pets 
        (userID, petName, animalType, petBreed, gender, age, birthday, size, weight, color, temperament, goodwith, health, description, status, image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssississsssss",
        $userID,
        $petName,
        $animalType,
        $petBreed,
        $gender,
        $age,
        $birthday,
        $size,
        $weight,
        $color,
        $temperament,
        $goodwith,
        $health,
        $description,
        $status,
        $imageName
    );

    if ($stmt->execute()) {
        echo "<script>alert('Pet successfully added!'); window.location='../add-pet.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
