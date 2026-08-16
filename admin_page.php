<?php

session_start();

require_once 'config.php';


/*
    Έλεγχος σύνδεσης
*/

if (!isset($_SESSION['username'])) {

    header("Location: index.php");
    exit();

}


/*
    Έλεγχος ότι ο χρήστης
    είναι Admin
*/

$username = $_SESSION['username'];


$result = $conn->query("

    SELECT role

    FROM users

    WHERE username = '$username'

");


if (!$result || $result->num_rows == 0) {

    header("Location: index.php");
    exit();

}


$user = $result->fetch_assoc();


if ($user['role'] !== 'admin') {

    header("Location: index.php");
    exit();

}

?>

<!DOCTYPE html>

<html lang="el">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UniBite - Συνολικά Στατιστικά</title>

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

        <a class="active" href="admin_page.php">

            Συνολικά Στατιστικά

        </a>

    </li>


    <li>

        <a href="admin_leaderboard.php">

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


<!-- =========================
     STATISTICS
     ========================= -->

<div class="admin-statistics">

    <h2>

        Συνολικά Στατιστικά

    </h2>


    <div class="stat-card">

        <h3>

            Επιτυχώς διαμοιρασμένες μερίδες

        </h3>


        <div
            id="total-meals"
            class="stat-number">

            ...

        </div>


        <p>

            Τον τελευταίο μήνα

        </p>

    </div>


    <p
        id="message"
        class="message">
    </p>

</div>


<script src="admin_page.js"></script>


</body>

</html>