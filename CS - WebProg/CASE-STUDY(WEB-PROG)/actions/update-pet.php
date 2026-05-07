<?php
require "../config.php";
session_start();

// Ensure user is logged in
if (!isset($_SESSION["userID"])) {
    header("Location: ../add-pet.php?login_required=true");
    exit();
}

$userID = $_SESSION["userID"];

// Ensure request is POST and petID is included
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["petID"])) {

    $petID = $_POST["petID"];

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

    // Check if new image uploaded
    $newImage = $_FILES["pet-image"]["name"];
    $imageToSave = null;

    if (!empty($newImage)) {
        // Save new image
        $imageName = time() . "-" . basename($newImage);
        $targetDir = "../images/imageupload/";
        $targetFile = $targetDir . $imageName;

        move_uploaded_file($_FILES["pet-image"]["tmp_name"], $targetFile);

        $imageToSave = $imageName;
    }

    // If image not uploaded, keep old one
    if ($imageToSave === null) {
        $sql = "UPDATE pets SET 
            petName=?, animalType=?, petBreed=?, gender=?, age=?, birthday=?, size=?, weight=?, color=?, temperament=?, goodwith=?, health=?, description=?
            WHERE petID=? AND userID=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssississssssi",
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
            $petID,
            $userID
        );

    } else {
        // Update including new image
        $sql = "UPDATE pets SET 
            petName=?, animalType=?, petBreed=?, gender=?, age=?, birthday=?, size=?, weight=?, color=?, temperament=?, goodwith=?, health=?, description=?, image=?
            WHERE petID=? AND userID=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssississssssssi",
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
            $imageToSave,
            $petID,
            $userID
        );
    }

    // Run and check update
    if ($stmt->execute()) {
        echo "<script>alert('Pet updated successfully!'); window.location='../update-delete.php';</script>";
        exit();
    } else {
        echo "Error updating pet: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>
