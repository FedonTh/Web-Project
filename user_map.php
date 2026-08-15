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

    <title>UniBite - Χάρτης</title>

    <link rel="stylesheet" href="style.css">


    <!-- Leaflet CSS -->

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
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

            <a href="user_page.php">

                Λίστα Αγγελιών

            </a>

        </li>


        <li>

            <a class="active" href="user_map.php">

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

                <span>

                    <?= htmlspecialchars($_SESSION['username']); ?>

                </span>!

            </h1>

        </div>


        <button onclick="window.location.href='logout.php'">

            Logout

        </button>

    </div>


    <h2 style="text-align:center; margin-top:30px;">

        Χάρτης Αγγελιών

    </h2>


    <!-- =========================
         ΦΙΛΤΡΑ
         ========================= -->

    <div class="map-filters">


        <span>

            Κάνε κλικ στον χάρτη
            για να επιλέξεις σημείο.

        </span>


        <label for="distance">

            Μέγιστη απόσταση:

        </label>


        <select id="distance">

            <option value="1">1 km</option>

            <option value="5" selected>5 km</option>

            <option value="10">10 km</option>

            <option value="20">20 km</option>

            <option value="50">50 km</option>

        </select>

        <button
            id="apply-filters"
            class="map-button">

            Εφαρμογή Φίλτρων

        </button>


        <button
            id="clear-filters"
            class="map-button">

            Καθαρισμός

        </button>

    </div>


    <div
        id="filter-message"
        class="filter-message">

        Δεν έχει επιλεγεί σημείο.

    </div>


    <!-- Χάρτης -->

    <div id="map"></div>


    <!-- Leaflet JS -->

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>


    <!-- Δικό μας JS -->

    <script src="user_map.js"></script>


</body>

</html>