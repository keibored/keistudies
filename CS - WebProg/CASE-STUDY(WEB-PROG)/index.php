<?php
session_start();
require "config.php";

// Fetch 3 random available pets
$featuredPets = [];

$featuredQuery = $conn->query("
    SELECT petID, petName, petBreed, age, image 
    FROM pets
    WHERE status = 'Available'
    ORDER BY RAND()
    LIMIT 3
");

while ($row = $featuredQuery->fetch_assoc()) {
    $featuredPets[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindYourFur | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>



    <!-- Hero section with floating pets -->
    <section class="hero" data-animate>
        <!-- Header with navigation -->
        <header class="header index" data-animate>
            <div class="container">
                <div class="nav-brand">
                    <div class="logo-bg">
                        <a href="index.php"><img src="./images/logo.png" alt=""></a>
                    </div>
                </div>
                <button id="navToggle" class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
                    ☰
                </button>
                <nav class="navbar">
                    <ul class="nav-links" id="navLinks">
                        <li><a href="index.php" class="active">Home</a></li>
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

        <!-- Video Background -->
        <div class="video-bg">
            <video autoplay loop muted playsinline>
                <source src="./images/video/MM005862____ABSTRACT_LIQUID_0149____1080p____A084_C080_0611J4_001.mp4" type="video/mp4">
            </video>
        </div>

        <!-- HERO CONTENT -->
        <div class="hero-content">
            <h2 class="title-shape-shift-hero">Find Your Perfect Furry Friend</h2>
            <p class="text-shape-shift">
                We connect loving homes with pets in need. Browse our selection of adorable animals waiting for their forever families.
            </p>
            <div class="hero-buttons">
                <a href="pet-list.php" class="btn btn-primary btn-shape-shift btn-browse">Browse Pets</a>
                <a href="add-pet.php" class="btn btn-secondary btn-shape-shift btn-list">List a Pet</a>
            </div>
        </div>

        <!-- FLOATING PET IMAGES -->
        <div class="floating-pets">
            <div class="floating-pet floating-pet-1" data-depth="0.2">
                <img src="./images/floating image/cat-float.jpg" alt="cat" class="shape-shift-img">
            </div>
            <div class="floating-pet floating-pet-2" data-depth="0.5">
                <img src="./images/floating image/bird-float.jpg" alt="bird" class="shape-shift-img">
            </div>
            <div class="floating-pet floating-pet-3" data-depth="0.3">
                <img src="./images/floating image/dog1-float.jpg" alt="dog" class="shape-shift-img">
            </div>
            <div class="floating-pet floating-pet-4" data-depth="0.4">
                <img src="./images/floating image/dog2-float img.avif" alt="dog" class="shape-shift-img">
            </div>
            <div class="floating-pet floating-pet-5" data-depth="0.6">
                <img src="./images/floating image/lizard-float.jpg" alt="lizard" class="shape-shift-img">
            </div>
            <div class="floating-pet floating-pet-6" data-depth="0.25">
                <img src="./images/floating image/rabbit-float.jpg" alt="rabbit" class="shape-shift-img">
            </div>
        </div>

    </section>

    <main class="container">
        <section class="featured-pets" data-animate>
            <h2 class="title-shape-shift">Featured Pets</h2>
            <p class="text-shape-shift">Meet our adorable friends looking for loving homes</p>
            
            <div class="pet-grid">
                <?php if (count($featuredPets) === 0): ?>
                    <p>No featured pets available right now.</p>

                <?php else: ?>
                    <?php foreach ($featuredPets as $fp): ?>
                        <article class="pet-card morph-card" data-animate>
                            <div class="pet-image">
                                <div class="image-morph-container">
                                    <img src="./images/imageupload/<?= $fp['image'] ?>" 
                                        alt="<?= $fp['petName'] ?>" 
                                        class="morph-image">
                                </div>
                                <div class="pet-shape-overlay"></div>
                            </div>

                            <div class="pet-info">
                                <h3 class="name-shape-shift"><?= $fp['petName'] ?></h3>
                                <p class="pet-breed breed-shape-shift"><?= $fp['petBreed'] ?></p>
                                <p class="pet-age age-shape-shift"><?= $fp['age'] ?> years old</p>

                                <a href="pet-details.php?id=<?= $fp['petID'] ?>" 
                                class="btn btn-outline btn-morph">
                                    View Details
                                </a>
                            </div>

                            <div class="pet-card-bg"></div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
        <section class="adoption-process" data-animate>
            <div class="process-container">
                <h2 class="title-shape-shift">How Adoption Works</h2>
                <div class="process-steps">
                    <div class="process-step morph-step">
                        <div class="step-icon shape-shift-icon">1</div>
                        <h3>Browse Pets</h3>
                        <p>Find your perfect match from our selection</p>
                    </div>
                    <div class="process-step morph-step">
                        <div class="step-icon shape-shift-icon">2</div>
                        <h3>Meet & Greet</h3>
                        <p>Arrange to meet your potential new family member</p>
                    </div>
                    <div class="process-step morph-step">
                        <div class="step-icon shape-shift-icon">3</div>
                        <h3>Adoption</h3>
                        <p>Complete the paperwork and bring them home</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Happy pet owners with their pets -->
        <section class="testimonials" data-animate>
            <div class="testimonial-container">
                <h2 class="title-shape-shift">Happy Tails</h2>
                <div class="testimonial-slider">
                    <div class="testimonial-slide morph-slide">
                        <div class="testimonial-image shape-shift-circle">
                            <img src="./images/person/person-1.webp" alt="Happy adopter">
                        </div>
                        <blockquote>
                            "Adopting Max was the best decision we ever made!"
                        </blockquote>
                        <div class="testimonial-author">- Jason</div>
                    </div>
                    <div class="testimonial-slide morph-slide">
                        <div class="testimonial-image shape-shift-circle">
                            <img src="./images/person/person-2.jpg" alt="Happy adopter">
                        </div>
                        <blockquote>
                            "Luna has brought so much joy to our home."
                        </blockquote>
                        <div class="testimonial-author">- Michael</div>
                    </div>
                </div>
            </div>
        </section>
    <!-- Animated CTA section with floating elements -->
    <section class="cta" data-animate>
        <div class="cta-content">
            <h2 class="title-shape-shift">Ready to Make a Difference?</h2>
            <p class="text-shape-shift">Join our community of pet lovers and help animals find their forever homes.</p>
            <div class="cta-buttons">
                <a href="pet-list.php" class="btn btn-primary btn-morph">Adopt a Pet</a>
                <a href="add-pet.php" class="btn btn-secondary btn-morph">List a Pet for Adoption</a>
            </div>
        </div>
        <div class="cta-floating-elements">
            <div class="floating-heart" data-depth="0.1">❤️</div>
            <div class="floating-paw" data-depth="0.3">🐾</div>
            <div class="floating-dog" data-depth="0.2">🐶</div>
            <div class="floating-cat" data-depth="0.4">🐱</div>
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
