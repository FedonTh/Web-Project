<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UniBite - Καταναλωτής</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Logo -->
    <div class="logo">
        UniBite
    </div>


    <!-- Navbar -->
    <ul class="navbar">

        <li>
            <a href="chef_page.php">
                Διαχείριση Αγγελιών
            </a>
        </li>

        <li>
            <a href="chef_aggelies.php">
                Διαχείριση Αιτημάτων
            </a>
        </li>

        <li>
            <a class="active" href="chef_points.php">
               Προβολή Πόντων
            </a>
        </li>

    </ul>

    <!-- Welcome message + Logout -->
    <div class="user-header">

        <div>
            <h1>
                Καλώς Ήρθες, <span><?= $_SESSION['username']; ?></span>!
            </h1>

        </div>

        <button onclick="window.location.href='logout.php'">
            Logout
        </button>

    </div>


</body>

</html>
