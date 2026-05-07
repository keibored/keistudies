<?php
session_start();
require "config.php";

// Allow only Admin
if (!isset($_SESSION["UserName"]) || $_SESSION["UserName"] !== "Admin") {
    die("Access denied.");
}

$pet = null;   // default
$petID = null; // default

if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $petID = intval($_GET['id']);

    // Fetch the pet from DB
    $stmt = $conn->prepare("SELECT * FROM pets WHERE petID = ?");
    $stmt->bind_param("i", $petID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindYourFur | Manage Pets</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Header with navigation -->
    <header class="header" data-animate>
        <div class="container">
            <div class="nav-brand">
                    <a href="index.php"><img src="./images/logo.png" alt=""></a>
            </div>
            <button id="navToggle" class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
                ☰
            </button>
            <nav class="navbar">
                <ul class="nav-links" id="navLinks">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="add-pet.php">Add Pet</a></li>
                    <li><a href="pet-list.php">Browse Pets</a></li>
                    <?php if (isset($_SESSION["userID"])): ?>
                        <?php if ($_SESSION["UserName"] === "Admin"): ?>
                            <li><a href="update-delete.php" class="active">Manage Pets</a></li>
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

    <!-- Main content with editable pet list -->
    <main class="container">
        <section class="management-section">

            <?php if ($pet === null): ?>
                <h2 style="text-align:center; margin-top:40px;">
                    Choose a pet to manage in <a href="pet-list.php">Browse Pets</a>.
                </h2>
                <h1>NO RECORDS</h1>
            <?php else: ?>

                <h2>Manage: <?= $pet['petName']; ?></h2>
                <article class="manage-pet-card">

                    <!-- Image -->
                    <div class="pet-image">
                        <img src="images/imageupload/<?= $pet['image']; ?>" alt="<?= $pet['petName']; ?>">
                    </div>

                    <!-- Update Form -->
                    <form action="update-pet.php" method="POST" class="pet-form" enctype="multipart/form-data">

                        <input type="hidden" name="petID" value="<?= $pet['petID']; ?>">

                        <div class="form-group">
                            <label for="pet-name">Pet Name</label>
                            <input type="text" id="pet-name" name="pet-name" value="<?= $pet['petName']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-type">Animal Type</label>
                            <select id="pet-type" name="pet-type" required>
                                <option value="">Select an animal type</option>
                                <option value="dog"    <?= $pet['animalType']=="dog"?"selected":""; ?>>Dog</option>
                                <option value="cat"    <?= $pet['animalType']=="cat"?"selected":""; ?>>Cat</option>
                                <option value="rabbit" <?= $pet['animalType']=="rabbit"?"selected":""; ?>>Rabbit</option>
                                <option value="bird"   <?= $pet['animalType']=="bird"?"selected":""; ?>>Bird</option>
                                <option value="other"  <?= $pet['animalType']=="other"?"selected":""; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pet-breed">Breed</label>
                            <input type="text" id="pet-breed" name="pet-breed" value="<?= $pet['petBreed']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-gender">Gender</label>
                            <select id="pet-gender" name="pet-gender" required>
                                <option value="">Select gender</option>
                                <option value="male"   <?= $pet['gender']=="male"?"selected":""; ?>>Male</option>
                                <option value="female" <?= $pet['gender']=="female"?"selected":""; ?>>Female</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pet-age">Age</label>
                            <input type="number" id="pet-age" name="pet-age" min="0" value="<?= $pet['age']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-birthdate">Birthday</label>
                            <input type="date" id="pet-birthdate" name="pet-birthdate" value="<?= $pet['birthday']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-size">Size</label>
                            <select id="pet-size" name="pet-size" required>
                                <option value="">Select size</option>
                                <option value="small"   <?= $pet['size']=="small"?"selected":""; ?>>Small</option>
                                <option value="average" <?= $pet['size']=="average"?"selected":""; ?>>Average</option>
                                <option value="large"   <?= $pet['size']=="large"?"selected":""; ?>>Large</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pet-weight">Weight</label>
                            <input type="text" id="pet-weight" name="pet-weight" value="<?= $pet['weight']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-color">Color</label>
                            <input type="text" id="pet-color" name="pet-color" value="<?= $pet['color']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-temperament">Temperament</label>
                            <input type="text" id="pet-temperament" name="pet-temperament" value="<?= $pet['temperament']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-goodw">Good with</label>
                            <input type="text" id="pet-goodw" name="pet-goodw" value="<?= $pet['goodwith']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-health">Health</label>
                            <input type="text" id="pet-health" name="pet-health" value="<?= $pet['health']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="pet-description">Description</label>
                            <textarea id="pet-description" name="pet-description" rows="5" required><?= $pet['description']; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="pet-image" class="file-label">
                                <span>Upload New Photo (optional)</span>
                                <input type="file" id="pet-image" name="pet-image" accept="image/*">
                            </label>
                            <div class="file-name">Current: <?= $pet['image']; ?></div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="form-actions">
                            <button type="submit" name="update" class="btn btn-primary">Update Pet</button>

                            <a href="actions/delete-pet.php?id=<?= $pet['petID']; ?>" 
                            onclick="return confirm('Are you sure you want to delete this pet?')"
                            class="btn btn-danger">
                            Delete Pet
                            </a>
                        </div>

                    </form>

                </article>
            <?php endif; ?>
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

    <?php
        require __DIR__ . '/components/modals/login_signup.php';
    ?>

    <script src="https://unpkg.com/feather-icons"></script>
    <script src="script.js"></script>
</body>
</html>