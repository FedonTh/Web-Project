<?php

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Επιλογή Ρόλου</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="role-container">

        <h1>Καλώς Ήρθες, <span><?= $_SESSION['username']; ?></span></h1>

        <p>Πώς θέλεις να χρησιμοποιήσεις την πλατφόρμα;</p>

        <div class="role-buttons">

            <button onclick="window.location.href='chef_page.php'">
                👨‍🍳 Μάγειρας
            </button>

            <button onclick="window.location.href='user_page.php'">
                🍽️ Καταναλωτής
            </button>

        </div>

        <button class="logout-button" onclick="window.location.href='logout.php'">
            Logout
        </button>

    </div>

</body>

</html>

