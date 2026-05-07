<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindYourFur | Browse Pets</title>
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
                    <li><a href="pet-list.php" class="active">Browse Pets</a></li>
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
    <br>

    <!-- Main content with filters and pet grid -->
    <?php
    // Read filters from URL
    $selectedTypes = isset($_GET['type']) ? explode(",", $_GET['type']) : [];
    $selectedAges  = isset($_GET['age'])  ? explode(",", $_GET['age'])  : [];
    $selectedSizes = isset($_GET['size']) ? explode(",", $_GET['size']) : [];
    ?>

    <main class="container pet-list-container">
        <!-- Filter sidebar -->
        <aside class="filters" data-animate>
            <h2>Filters</h2>

            <!-- ANIMAL TYPE FILTERS -->
            <div class="filter-group" data-animate>
                <h3>Animal Type</h3>

                <?php
                $types = ["dog" => "Dogs", "cat" => "Cats", "rabbit" => "Rabbits", "bird" => "Birds", "other" => "Other"];
                foreach ($types as $value => $label):
                ?>
                <label class="filter-option">
                    <input type="checkbox" name="type" value="<?= $value ?>"
                        <?= in_array($value, $selectedTypes) ? "checked" : "" ?>>
                    <span class="checkmark"></span>
                    <?= $label ?>
                </label>
                <?php endforeach; ?>
            </div>

            <!-- AGE FILTERS -->
            <div class="filter-group" data-animate>
                <h3>Age</h3>

                <?php
                $ages = [
                    "puppy"  => "Puppy/Kitten (0-1 yr)",
                    "young"  => "Young (1-3 yrs)",
                    "adult"  => "Adult (3-8 yrs)",
                    "senior" => "Senior (8+ yrs)"
                ];
                foreach ($ages as $value => $label):
                ?>
                <label class="filter-option">
                    <input type="checkbox" name="age" value="<?= $value ?>"
                        <?= in_array($value, $selectedAges) ? "checked" : "" ?>>
                    <span class="checkmark"></span>
                    <?= $label ?>
                </label>
                <?php endforeach; ?>
            </div>

            <!-- SIZE FILTERS -->
            <div class="filter-group" data-animate>
                <h3>Size</h3>

                <?php
                $sizes = ["small" => "Small", "medium" => "Medium", "large" => "Large"];
                foreach ($sizes as $value => $label):
                ?>
                <label class="filter-option">
                    <input type="checkbox" name="size" value="<?= $value ?>"
                        <?= in_array($value, $selectedSizes) ? "checked" : "" ?>>
                    <span class="checkmark"></span>
                    <?= $label ?>
                </label>
                <?php endforeach; ?>
            </div>

            <button class="btn btn-primary apply-filters" data-animate>Apply Filters</button>
            <button class="btn btn-outline reset-filters" data-animate>Reset Filters</button>

        </aside>


        <!-- Pet grid section -->
        <section class="pet-listings" data-animate>
            <div class="listings-header" data-animate>
                <h2>Available Pets</h2>
                <div class="sort-options">
                    <label for="sort">Sort by:</label>

                    <?php $sort = $_GET['sort'] ?? 'newest'; ?>

                    <select id="sort" name="sort">
                        <option value="newest" <?= ($sort == "newest") ? "selected" : "" ?>>Newest First</option>
                        <option value="oldest" <?= ($sort == "oldest") ? "selected" : "" ?>>Oldest First</option>
                        <option value="age" <?= ($sort == "age") ? "selected" : "" ?>>Age</option>
                        <option value="name" <?= ($sort == "name") ? "selected" : "" ?>>Name</option>
                    </select>
                </div>
            </div>
            
            <div class="pet-grid">
                <?php
                require "config.php";
                
                
                //---------------PAGINATION--------------------//
                $limit = 6;

                // What page are we on?
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($page < 1) $page = 1;

                $offset = ($page - 1) * $limit;

                //---------FETCH ONLY PETS AVAILABLE WITH FILTER----------//
                $conditions = ["status = 'Available'"];

                // ANIMAL TYPE FILTER
                if (!empty($_GET['type'])) {
                    $types = explode(",", $_GET['type']);
                    $typeList = "'" . implode("','", $types) . "'";
                    $conditions[] = "animalType IN ($typeList)";
                }

                // AGE FILTER
                if (!empty($_GET['age'])) {
                    $ages = explode(",", $_GET['age']);
                    $ageConditions = [];

                    foreach ($ages as $ageRange) {
                        if ($ageRange == "puppy")   $ageConditions[] = "age BETWEEN 0 AND 1";
                        if ($ageRange == "young")   $ageConditions[] = "age BETWEEN 1 AND 3";
                        if ($ageRange == "adult")   $ageConditions[] = "age BETWEEN 3 AND 8";
                        if ($ageRange == "senior")  $ageConditions[] = "age > 8";
                    }

                    if (!empty($ageConditions)) {
                        $conditions[] = "(" . implode(" OR ", $ageConditions) . ")";
                    }
                }

                // SIZE FILTER
                if (!empty($_GET['size'])) {
                    $sizes = explode(",", $_GET['size']);
                    $sizeList = "'" . implode("','", $sizes) . "'";
                    $conditions[] = "size IN ($sizeList)";
                }

                $where = implode(" AND ", $conditions);

                // Apply SORT
                $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

                switch ($sort) {
                    case "oldest":
                        $orderBy = "petID ASC";
                        break;
                    case "age":
                        $orderBy = "age ASC";
                        break;
                    case "name":
                        $orderBy = "petName ASC";
                        break;
                    default:
                        $orderBy = "petID DESC"; // newest
                }

                $sql = "SELECT * FROM pets 
                        WHERE $where
                        ORDER BY $orderBy
                        LIMIT $limit OFFSET $offset";

                $countResult = $conn->query("SELECT COUNT(*) AS total FROM pets WHERE $where");
                $totalPets = $countResult->fetch_assoc()['total'];

                $totalPages = ceil($totalPets / $limit);

                $result = $conn->query($sql);

                if ($result->num_rows === 0):
                ?>
                    <p>No pets available at the moment.</p>

                <?php else: ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        
                        <article class="pet-card morph-card" data-animate>
                            <div class="pet-image">
                                <div class="image-morph-container">
                                    <img src="images/imageupload/<?= $row['image'] ?>" 
                                        alt="<?= $row['petName'] ?>" 
                                        class="morph-image">
                                </div>
                                <div class="pet-shape-overlay"></div>
                            </div>

                            <div class="pet-info">
                                <h3 class="name-shape-shift"><?= $row['petName'] ?></h3>
                                <p class="pet-breed breed-shape-shift"><?= $row['petBreed'] ?></p>
                                <p class="pet-age age-shape-shift"><?= $row['age'] ?> years old</p>

                                <?php if (isset($_SESSION["UserName"]) && $_SESSION["UserName"] === "Admin"): ?>
                                    <a href="update-delete.php?id=<?= $row['petID'] ?>" 
                                        class="btn btn-outline btn-morph">
                                        Manage Pet
                                    </a>
                                <?php else: ?>
                                    <a href="pet-details.php?id=<?= $row['petID'] ?>" 
                                        class="btn btn-outline btn-morph">
                                        View Details
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="pet-card-bg"></div>
                        </article>

                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <div class="pagination" data-animate>
                <!-- Previous button -->
                <a href="?page=<?= max(1, $page - 1) ?>">
                    <button class="btn btn-outline" <?= ($page <= 1) ? "disabled" : "" ?>>Previous</button>
                </a>

                <!-- Page numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>">
                        <button class="btn <?= ($i == $page) ? "btn-primary active" : "btn-outline" ?>">
                            <?= $i ?>
                        </button>
                    </a>
                <?php endfor; ?>

                <!-- Next button -->
                <a href="?page=<?= min($totalPages, $page + 1) ?>">
                    <button class="btn btn-outline" <?= ($page >= $totalPages) ? "disabled" : "" ?>>Next</button>
                </a>
            </div>

        </section>
    </main>
    <br>

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
