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

    <title>UniBite - Διαχείριση Αιτημάτων</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


<div class="logo">
    UniBite
</div>


<ul class="navbar">

    <li>
        <a href="chef_page.php">
            Διαχείριση Αγγελιών
        </a>
    </li>

    <li>
        <a class="active" href="chef_aggelies.php">
            Διαχείριση Αιτημάτων
        </a>
    </li>

    <li>
        <a href="chef_points.php">
            Προβολή Πόντων
        </a>
    </li>

</ul>


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


<div class="chef-content">

    <h2>
        Αιτήματα για τις Αγγελίες μου
    </h2>


    <div id="aitimata-container"></div>


    <p id="message"></p>

</div>


<script src="chef_aitimata.js"></script>

</body>

</html>