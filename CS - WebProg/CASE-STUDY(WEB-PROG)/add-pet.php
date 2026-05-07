<?php
session_start();

if (!isset($_SESSION["userID"])) {
    header("Location: index.php?login_required=true");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindYourFur | Add Pet</title>
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
                    <li><a href="add-pet.php" class="active">Add Pet</a></li>
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

    <!-- Main form content -->
    <main class="container">
        <section class="form-section" data-animate>
            <h2>Add a New Pet for Adoption</h2>
            <p class="form-description">Help us find a loving home for a pet in need. Fill out the form below to list a pet for adoption.</p>
            
            <form id="petForm" action="actions/save-pet.php" method="post" class="pet-form" enctype="multipart/form-data" data-animate>
                <div class="form-group">
                    <label for="pet-name">Pet Name</label>
                    <input type="text" id="pet-name" name="pet-name" required>
                </div>
                
                <div class="form-group">
                    <label for="pet-type">Animal Type</label>
                    <select id="pet-type" name="pet-type" required>
                        <option value="">Select an animal type</option>
                        <option value="dog">Dog</option>
                        <option value="cat">Cat</option>
                        <option value="rabbit">Rabbit</option>
                        <option value="bird">Bird</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="pet-breed">Breed</label>
                    <input type="text" id="pet-breed" name="pet-breed" required>
                </div>

                <div class="form-group">
                    <label for="pet-gender">Gender</label>
                    <select id="pet-gender" name="pet-gender" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pet-age">Age</label>
                    <input type="number" id="pet-age" name="pet-age" min="0" required>
                </div>

                <div class="form-group">
                    <label for="pet-birthdate">Birthday</label>
                    <input type="date" id="pet-birthdate" name="pet-birthdate" required>
                </div>

                <div class="form-group">
                    <label for="pet-size">Size</label>
                    <select id="pet-size" name="pet-size" required>
                        <option value="">Select size</option>
                        <option value="small">Small</option>
                        <option value="average">Average</option>
                        <option value="large">Large</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pet-weight">Weight</label>
                    <input type="text" id="pet-weight" name="pet-weight" placeholder="in lbs" required>
                </div>

                <div class="form-group">
                    <label for="pet-color">Color</label>
                    <input type="text" id="pet-color" name="pet-color" required>
                </div>

                <div class="form-group">
                    <label for="pet-temperament">Temperament</label>
                    <input type="text" id="pet-temperament" name="pet-temperament" required>
                </div>

                <div class="form-group">
                    <label for="pet-goodw">Good with</label>
                    <input type="text" id="pet-goodw" name="pet-goodw" required>
                </div>

                <div class="form-group">
                    <label for="pet-health">Health</label>
                    <input type="text" id="pet-health" name="pet-health" required>
                </div>
                
                <div class="form-group">
                    <label for="pet-description">Description</label>
                    <textarea id="pet-description" name="pet-description" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="pet-image" class="file-label">
                        <span>Upload Photo</span>
                        <input type="file" id="pet-image" name="pet-image" accept="image/*" required>
                    </label>
                    <div class="file-name" id="file-name">No file chosen</div>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Pet for Adoption</button>
            </form>
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

    <?php if (isset($_GET["login_required"])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("openLoginModal").click();
        });
    </script>
    <?php endif; ?>

</body>
</html>

