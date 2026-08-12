<?php

session_start();

require_once 'config.php';

header("Content-Type: application/json");


if (!isset($_SESSION['username'])) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Δεν είσαι συνδεδεμένος."

    ]);

    exit();

}


$username =
    $_SESSION['username'];


/* Βρίσκουμε τον chef */

$result = $conn->query("

    SELECT id

    FROM users

    WHERE username = '$username'

");


if (!$result || $result->num_rows == 0) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Ο χρήστης δεν βρέθηκε."

    ]);

    exit();

}


$user =
    $result->fetch_assoc();


$chef_id =
    $user['id'];


/*
    Παίρνουμε τα αιτήματα
    για τις αγγελίες του chef.
*/

$result = $conn->query("

    SELECT

        aitima.id,

        aitima.aggelia_id,

        aitima.user_id,

        aitima.aitima_date,

        aitima.status,

        aitima.picked_up,

        aggelia.title,

        aggelia.merides_left,

        aggelia.location,

        aggelia.pickup_time,

        users.username

    FROM aitima

    INNER JOIN aggelia
        ON aitima.aggelia_id = aggelia.id

    INNER JOIN users
        ON aitima.user_id = users.id

    WHERE aggelia.chef_id = '$chef_id'

    ORDER BY aitima.aitima_date DESC

");


if (!$result) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Σφάλμα κατά την ανάκτηση των αιτημάτων."

    ]);

    exit();

}


$aitimata = [];


while ($row = $result->fetch_assoc()) {

    $aitimata[] = $row;

}


echo json_encode([

    "success" => true,

    "aitimata" => $aitimata

]);

?>