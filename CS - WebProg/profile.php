<?php
session_start();

$userID = $_SESSION["userID"] ?? null;
$displayName = $_SESSION["DisplayName"] ?? null;
$userName = $_SESSION["UserName"] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindYourFur | My Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header class="header">
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
                    <?php if ($_SESSION["UserName"] === "Admin"): ?>
                        <li><a href="update-delete.php">Manage Pets</a></li>
                    <?php endif; ?>
                    <li><a href="pending-pets.php">Pending Pets</a></li> 
                    <li><a href="profile.php" class="btn-outline loggedin">My Account</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <br>

    <!-- Main -->
    <main class="container profile-main">
            <section class="modal-box fade-in profile-card">
                <h2 class="text-center">My Account</h2>

                <div class="profile-info">
                    <p><strong>User ID:</strong> <?php echo htmlspecialchars($userID); ?></p>
                    <p><strong>Display Name:</strong> <?php echo htmlspecialchars($displayName); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($userName); ?></p>
                </div>

                <div class="hero-buttons profile-actions">
                    <a class="btn btn-primary" id="openEditProfile">Edit Profile</a>
                    <a class="btn btn-outline" href="actions/logout.php">Logout</a>

                    <form action="actions/delete-account.php" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');"
                          class="profile-delete-form">
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </form>
                </div>
            </section>
    </main>

    <?php
        require __DIR__ . '/components/modals/edit-profile.php';
    ?>
    
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="script.js"></script>
    <br>
</body>
</html>
