<?php
session_start();
require "config.php";

//Redirect if not logged in
if (!isset($_SESSION["userID"])) {
    header("Location: index.php?login_required=true");
    exit();
}

$isAdmin = (isset($_SESSION["UserName"]) && $_SESSION["UserName"] === "Admin");
$userID = $_SESSION["userID"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindYourFur | Pending Pets</title>
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
                            <li><a href="update-delete.php">Manage Pets</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li><a href="pending-pets.php" class="active">Pending Pets</a></li> 
                    <?php if (isset($_SESSION["userID"])): ?>
                        <li><a href="profile.php" class="btn-outline loggedin">My Account</a></li>
                    <?php else: ?>
                        <li><a href="#" id="openLoginModal" class="btn-outline">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="container" data-animate style="min-height: 60dvh;">
        <br>
        <h2>Pending Pets</h2>
        <p class="form-description">Review pet submissions and adoption requests.</p>

        <!-- USER SIDE: Their pending uploaded pets -->
        <?php if (!$isAdmin): ?>
        <section>
            <h3>Your Pets Waiting for Approval</h3>

            <?php
            $sql = "SELECT * FROM pets WHERE userID = ? AND status IN ('Upload Pending', 'Rejected')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0):
                echo "<p>No pending uploads.</p>";
            else:
                while ($row = $result->fetch_assoc()):
            ?>
                <div class="pet-card-horizontal">
                    <img src="images/imageupload/<?= $row['image'] ?>" alt="Pet">

                    <div class="pet-card-horizontal-content">
                        <h4><?= $row['petName'] ?></h4>
                        <div class="pet-info-row">
                            <div><strong>Type:</strong> <?= $row['animalType'] ?></div>
                            <div><strong>Breed:</strong> <?= $row['petBreed'] ?></div>
                            <div><strong>Gender:</strong> <?= $row['gender'] ?></div>
                            <div><strong>Age:</strong> <?= $row['age'] ?></div>
                            <div><strong>Status:</strong> <?= $row['status'] ?></div>
                        </div>
                        <div class="pet-description">
                            <strong>Description:</strong>
                            <p><?= $row['description'] ?></p>
                        </div>
                    </div> 

                    <div class="pet-card-horizontal-actions">
                        <form class="cancel-form" method="POST" action="actions/delete-pet.php">
                            <input type="hidden" name="petID" value="<?= $row['petID'] ?>">
                            <button type="submit" class="btn btn-danger cancel-btn">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php
                endwhile;
            endif;
            ?>
        </section>
        <?php endif; ?>

        <!-- ADMIN SIDE: All pending pets -->
        <?php if ($isAdmin): ?>
        <section>
            <h3>All Pending Pets</h3>

            <?php
            $sql = "SELECT pets.*, users.DisplayName 
                    FROM pets 
                    JOIN users ON pets.userID = users.userID 
                    WHERE status = 'Upload Pending'";
            $result = $conn->query($sql);

            if ($result->num_rows === 0):
                echo "<p>No pending pets found.</p>";
            else:
                while ($row = $result->fetch_assoc()):
            ?>
                <div class="pet-card-horizontal">
                    <img src="images/imageupload/<?= $row['image'] ?>" alt="Pet">

                    <div class="pet-card-horizontal-content">
                        <h4><?= $row['petName'] ?></h4>
                        <div class="pet-info-row">
                            <div><strong>Type:</strong> <?= $row['animalType'] ?></div>
                            <div><strong>Breed:</strong> <?= $row['petBreed'] ?></div>
                            <div><strong>Gender:</strong> <?= $row['gender'] ?></div>
                            <div><strong>Age:</strong> <?= $row['age'] ?></div>
                            <div><strong>Status:</strong> <?= $row['status'] ?></div>
                        </div>
                        <div class="pet-description">
                            <strong>Description:</strong>
                            <p><?= $row['description'] ?></p>
                        </div>
                    </div> 
                    
                    <div class="pet-card-horizontal-actions">
                        <form class="approve-form" method="POST" action="actions/approve-pet.php">
                            <input type="hidden" name="petID" value="<?= $row['petID'] ?>">
                            <button type="submit" class="btn btn-primary approve-btn">Approve</button>
                        </form>
                        <form class="reject-form" method="POST" action="actions/reject-pet.php">
                            <input type="hidden" name="petID" value="<?= $row['petID'] ?>">
                            <button type="submit" class="btn btn-danger reject-btn">Reject</button>
                        </form>
                    </div>
                </div>
            <?php
                endwhile;
            endif;
            ?>
        </section>
        <?php endif; ?>


        <!-- USER SIDE: Pending adoption requests -->
        <?php if (!$isAdmin): ?>
        <section>
            <h3>Your Adoption Requests</h3>

            <?php
            $sql = "SELECT adoption.*, pets.petName, pets.image 
                    FROM adoption
                    INNER JOIN pets ON adoption.petID = pets.petID
                    WHERE adoption.userID = ?
                    ORDER BY adoption.status = 'Pending' DESC, adoption.adoptID DESC";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0):
                echo "<p>No adoption requests found.</p>";
            else:
                while ($row = $result->fetch_assoc()):

                    $status = $row['status'];
            ?>
                <div class="pet-card-horizontal">

                    <img src="images/imageupload/<?= $row['image'] ?>" alt="Pet">

                    <div class="pet-card-horizontal-content">
                        <h4><?= $row['petName'] ?></h4>

                        <div class="pet-info-row">
                            <div><strong>Full Name:</strong> <?= $row['fullName'] ?></div>
                            <div><strong>Contact Number:</strong> <?= $row['contactNumber'] ?></div>
                            <div><strong>Email:</strong> <?= $row['email'] ?></div>
                            <div><strong>Payment Confirmed:</strong> <?= $row['payConfirm'] ? "Yes" : "No" ?></div>

                            <div><strong>Status:</strong> 
                                <?php 
                                    if ($status === "Approved") echo "<span class='status-approved'>Approved</span>";
                                    elseif ($status === "Rejected") echo "<span class='status-rejected'>Rejected</span>";
                                    else echo "<span class='status-pending'>Pending</span>";
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="pet-card-horizontal-actions">

                        <?php if ($status === "Pending"): ?>
                            
                            <!-- Only pending requests can be canceled -->
                            <button class="btn btn-danger cancel-adoption-btn" 
                                data-adoptid="<?= $row['adoptID'] ?>">
                                Cancel Request
                            </button>

                        <?php else: ?>

                            <!-- Approved or Rejected: No actions -->
                            <button class="btn btn-secondary" disabled>
                                <?= $status ?>
                            </button>

                        <?php endif; ?>

                    </div>

                </div>
            <?php
                endwhile;
            endif;
            ?>
        </section>
        <?php endif; ?>

        <!-- ADMIN SIDE: All pending adoption -->
        <?php if ($isAdmin): ?>
        <section>
            <h3>All Adoption Requests</h3>

            <?php
            $sql = "SELECT adoption.*, pets.petName, pets.image
                    FROM adoption
                    INNER JOIN pets ON adoption.petID = pets.petID
                    WHERE adoption.status = 'Pending'
                    ORDER BY adoption.adoptID DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0):
                echo "<p>No adoption requests found.</p>";
            else:
                while ($row = $result->fetch_assoc()):
            ?>
                    <div class="pet-card-horizontal">

                        <!-- Pet Image -->
                        <img src="images/imageupload/<?= $row['image'] ?>" alt="Pet">

                        <!-- Adoption Details -->
                        <div class="pet-card-horizontal-content">
                            <h4><?= $row['petName'] ?></h4>

                            <div class="pet-info-row">
                                <div><strong>Applicant Name:</strong> <?= $row['fullName'] ?></div>
                                <div><strong>Contact Number:</strong> <?= $row['contactNumber'] ?></div>
                                <div><strong>Email:</strong> <?= $row['email'] ?></div>
                                <div><strong>Payment Confirmed:</strong> <?= $row['payConfirm'] ? "Yes" : "No" ?></div>
                                <div><strong>Status:</strong> <?= $row['status'] ?></div>
                            </div>
                        </div>

                        <!-- Admin Action Buttons -->
                        <div class="pet-card-horizontal-actions">
                            <?php if ($row['status'] === "Pending"): ?>
                                <button class="btn btn-success approve-btn" data-adoptid="<?= $row['adoptID'] ?>">Approve</button>
                                <button class="btn btn-danger reject-btn" data-adoptid="<?= $row['adoptID'] ?>">Reject</button>
                            <?php else: ?>
                                <span class="status-label"><?= $row['status'] ?></span>
                            <?php endif; ?>
                        </div>

                    </div>
            <?php
                endwhile;
            endif;
            ?>
        </section>
        <?php endif; ?>
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