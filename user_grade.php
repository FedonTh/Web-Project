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

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>UniBite - Βαθμολογία</title>

    <link rel="stylesheet"
          href="style.css">

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

        <a href="user_map.php">
            Χάρτης
        </a>

    </li>


    <li>

        <a class="active"
           href="user_grade.php">

            Βαθμολογία

        </a>

    </li>

</ul>


<!-- Welcome + Logout -->

<div class="user-header">

    <div>

        <h1>

            Καλώς Ήρθες,
            <span>
                <?= htmlspecialchars($_SESSION['username']); ?>
            </span>!

        </h1>

    </div>


    <button
        onclick="window.location.href='logout.php'">

        Logout

    </button>

</div>


<!-- Ratings -->

<div class="chef-content">

    <h2>
        Αξιολόγηση Αγγελιών
    </h2>


    <div id="ratings-container">

        <p>
            Φόρτωση...
        </p>

    </div>


    <p id="message"></p>

</div>


<script src="user_grade.js"></script>

</body>

</html>