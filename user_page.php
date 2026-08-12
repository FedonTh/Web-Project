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

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

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
            <a class="active" href="user_page.php">
                Λίστα Αγγελιών
            </a>
        </li>

        <li>
            <a href="user_map.php">
                Χάρτης
            </a>
        </li>

        <li>
            <a href="user_grade.php">
                Βαθμολογία
            </a>
        </li>

    </ul>


    <!-- Welcome message + Logout -->

    <div class="user-header">

        <div>

            <h1>
                Καλώς Ήρθες,
                <span><?= htmlspecialchars($_SESSION['username']) ?></span>!
            </h1>

        </div>


        <button
            onclick="window.location.href='logout.php'">

            Logout

        </button>

    </div>


    <!-- FEED -->

    <div class="user-feed">

        <h2>
            Διαθέσιμες Αγγελίες
        </h2>


        <div
            id="user-aggelies-container"
            class="row g-4">

            <!--
                Οι αγγελίες
                θα μπουν εδώ
                μέσω JavaScript
            -->

        </div>


        <p
            id="feed-message"
            class="feed-message">
        </p>

    </div>


    <script src="user_aggelies.js"></script>

</body>

</html>

