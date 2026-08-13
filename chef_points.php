<?php

session_start();

require_once 'config.php';


if (!isset($_SESSION['username'])) {

    header("Location: index.php");
    exit();

}


$username = $_SESSION['username'];


/*
    Παίρνουμε τους πόντους
    του συνδεδεμένου χρήστη
*/

$result = $conn->query("

    SELECT credits

    FROM users

    WHERE username = '$username'

");


if (!$result || $result->num_rows == 0) {

    $credits = 0;

}
else {

    $user = $result->fetch_assoc();

    $credits = $user['credits'];

}

?>


<!DOCTYPE html>

<html lang="el">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>UniBite - Πόντοι</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

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

            Καλώς Ήρθες,
            <span><?= htmlspecialchars($_SESSION['username']); ?></span>!

        </h1>

    </div>


    <button
        onclick="window.location.href='logout.php'">

        Logout

    </button>

</div>


<!-- Points -->

<div class="points-container">

    <h2>
        Οι πόντοι σας
    </h2>


    <div class="points-box">

        <p>
            Οι πόντοι σας είναι:
        </p>


        <span class="points-number">
            <?= $credits ?>
        </span>

    </div>

</div>


</body>

</html>