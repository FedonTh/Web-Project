<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σελίδα Μάγειρα</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #fff;">
    <div class="box">
        <h1>Καλώς Ήρθες, <span><?= $_SESSION['username']; ?></span></h1>
        <p>Αυτή είναι η σελίδα <span>μάγειρα</span></p>
        <button onclick="window.location.href='logout.php'"> Logout </button>
    </div>
</body>
</html>