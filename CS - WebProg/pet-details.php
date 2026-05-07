<?php
session_start();
require "config.php";

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h2 style='text-align:center;'>Invalid pet ID.</h2>");
}

$petID = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM pets WHERE petID = ?");
$stmt->bind_param("i", $petID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<h2 style='text-align:center;'>Pet not found.</h2>");
}

$pet = $result->fetch_assoc();

$relatedPets = [];

$relatedStmt = $conn->prepare("
    SELECT * FROM pets
    WHERE animalType = ?
    AND petID != ?
    AND status = 'Available'
    ORDER BY RAND()
    LIMIT 3
");
$relatedStmt->bind_param("si", $pet['animalType'], $pet['petID']);
$relatedStmt->execute();
$result1 = $relatedStmt->get_result();

while ($row = $result1->fetch_assoc()) {
    $relatedPets[] = $row;
}

if (count($relatedPets) < 3) {

    $needed = 3 - count($relatedPets);

    $excludeIDs = [$pet['petID']];
    foreach ($relatedPets as $p) {
        $excludeIDs[] = $p['petID'];
    }
    $excludeList = implode(",", $excludeIDs);

    $fillQuery = "
        SELECT * FROM pets
        WHERE petID NOT IN ($excludeList)
        AND status = 'Available'
        ORDER BY RAND()
        LIMIT $needed
    ";

    $result2 = $conn->query($fillQuery);

    while ($row = $result2->fetch_assoc()) {
        $relatedPets[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindYourFur | <?= $pet['petName']; ?> Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header class="header" data-animate>
        <div class="container">
            <div class="nav-brand">
                    <a href="index.php"><img src="./images/logo.png" alt=""></a>
            </div>
            <button id="navToggle" class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
            <nav class="navbar">
                <ul class="nav-links" id="navLinks">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="add-pet.php">Add Pet</a></li>
                    <li><a href="pet-list.php">Browse Pets</a></li>
                    <?php if (isset($_SESSION["userID"])): ?>
                        <?php if ($_SESSION["UserName"] === "Admin"): ?>
                            <li><a href="update-delete.php">Manage Pets</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li><a href="pending-pets.php">Pending Pets</a></li>
                    <?php if (isset($_SESSION["userID"])): ?>
                        <li><a href="profile.php" class="btn-outline loggedin">My Account</a></li>
                    <?php else: ?>
                        <li><a href="#" id="openLoginModal" class="btn-outline">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main pet details -->
    <main class="container">
        <section class="pet-details" data-animate>
            <div class="pet-details-content">

                <!-- Pet image -->
                <div class="pet-image-large" data-animate>
                    <img src="images/imageupload/<?= $pet['image']; ?>" alt="<?= $pet['petName']; ?>">
                </div>

                <!-- Pet info -->
                <div class="pet-info-details" data-animate>
                    <h2><?= $pet['petName']; ?></h2>

                    <div class="pet-meta" data-animate>
                        <span class="pet-breed"><?= $pet['petBreed']; ?></span>
                        <span class="pet-age"><?= $pet['age']; ?> years old</span>
                        <span class="pet-gender"><?= $pet['gender']; ?></span>
                    </div>

                    <div class="pet-description" data-animate>
                        <h3>About <?= $pet['petName']; ?></h3>
                        <p><?= nl2br($pet['description']); ?></p>
                    </div>

                    <!-- Extra details -->
                    <div class="pet-details-list" data-animate>
                        <h3>Details</h3>
                        <ul>
                            <li><strong>Breed:</strong> <?= $pet['petBreed']; ?></li>
                            <li><strong>Gender:</strong> <?= $pet['gender']; ?></li>
                            <li><strong>Age:</strong> <?= $pet['age']; ?> years</li>
                            <li><strong>Size:</strong> <?= $pet['size']; ?></li>
                            <li><strong>Color:</strong> <?= $pet['color']; ?></li>
                            <li><strong>Temperament:</strong> <?= $pet['temperament']; ?></li>
                            <li><strong>Good With:</strong> <?= $pet['goodwith']; ?></li>
                            <li><strong>Health:</strong> <?= $pet['health']; ?></li>
                        </ul>
                    </div>

                    <div class="adopt-button-container" data-animate>
                        <a href="#" class="btn btn-primary btn-large openAdoptModal" 
                            data-petid="<?= $pet['petID']; ?>">
                            Adopt <?= $pet['petName']; ?>
                        </a>
                        <p class="adoption-note">Please allow 2–3 business days for processing your application.</p>
                    </div>

                </div>
            </div>

            <!-- Related pets placeholder -->
            <div class="related-pets" data-animate>
                <h3>Other Pets You Might Like</h3>
                <div class="pet-grid">
                    <?php foreach ($relatedPets as $r): ?>
                        <article class="pet-card" data-animate>
                            <div class="pet-image">
                                <img src="images/imageupload/<?= $r['image'] ?>" 
                                    alt="<?= $r['petName'] ?>">
                            </div>
                            <div class="pet-info">
                                <h3><?= $r['petName'] ?></h3>
                                <p class="pet-breed"><?= $r['petBreed'] ?></p>
                                <p class="pet-age"><?= $r['age'] ?> years old</p>

                                <a href="pet-details.php?id=<?= $r['petID'] ?>" 
                                class="btn btn-outline">
                                    View Details
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

        </section>
    </main>

    <!-- Footer -->
    <footer class="footer" data-animate>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>FindYourFur</h3>
                    <p>Connecting loving homes with pets in need since 2025.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="add-pet.php">Add a Pet</a></li>
                        <li><a href="pet-list.php">Browse Pets</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Us</h4>
                    <p>email@findyourfur.com</p>
                    <p>(555) 123-4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 FindYourFur. All rights reserved.</p>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook">
                        <img src="images/facebook.png" width="24" height="24">
                    </a>
                    <a href="#" aria-label="Twitter">
                        <img src="images/twitter.png" width="24" height="24">
                    </a>
                    <a href="#" aria-label="Instagram">
                        <img src="images/instagram.png" width="24" height="24">
                    </a>
                </div>
            </div>
        </div>
    </footer>

<?php require __DIR__ . '/components/modals/adopt-modal.php'; ?>
<?php require __DIR__ . '/components/modals/login_signup.php'; ?>

<script src="https://unpkg.com/feather-icons"></script>
<script src="script.js"></script>

</body>
</html>
