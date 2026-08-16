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

    <title>UniBite - Κατάταξη Χρηστών</title>

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

        <a href="admin_page.php">

            Συνολικά Στατιστικά

        </a>

    </li>


    <li>

        <a class="active" href="admin_leaderboard.php">

            Κατάταξη Χρηστών

        </a>

    </li>

</ul>


<!-- Welcome message + Logout -->

<div class="user-header">

    <div>

        <h1>

            Καλώς Ήρθες,

            <span>

                <?= htmlspecialchars($_SESSION['username']); ?>

            </span>!

        </h1>

    </div>


    <button onclick="window.location.href='logout.php'">

        Logout

    </button>

</div>


<h2 class="admin-page-title">

    Κατάταξη Χρηστών

</h2>


<!-- Top Donor -->

<div class="points-box">

    <h2>

        🏆 Top Donor

    </h2>


    <div id="top-donor">

        Φόρτωση...

    </div>

</div>


<!-- Highest Rated Meals -->

<div class="points-box">

    <h2>

        ⭐ Γεύματα με Υψηλότερη Αξιολόγηση

    </h2>


    <div id="top-rated">

        Φόρτωση...

    </div>

</div>


<script src="admin_leaderboard.js"></script>


</body>

</html>