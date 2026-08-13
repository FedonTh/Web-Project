<?php

session_start();

require_once 'config.php';

header("Content-Type: application/json");


/*
    Έλεγχος σύνδεσης
*/

if (!isset($_SESSION['username'])) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Δεν είσαι συνδεδεμένος."

    ]);

    exit();

}


/*
    Βρίσκουμε τον χρήστη
*/

$username =
    $_SESSION['username'];


$result =
    $conn->query("

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


$user_id =
    $user['id'];


/*
    Παίρνουμε τις αγγελίες
    που ο χρήστης έχει παραλάβει
    και δεν έχει αξιολογήσει.
*/

$sql = "

    SELECT

        a.id AS aitima_id,

        ag.title,

        ag.location,

        ag.pickup_time,

        u.username AS chef_username,

        a.pickup_date

    FROM aitima a


    INNER JOIN aggelia ag

        ON a.aggelia_id = ag.id


    INNER JOIN users u

        ON ag.chef_id = u.id


    LEFT JOIN rating r

        ON r.aitima_id = a.id


    WHERE

        a.user_id = '$user_id'

        AND a.status = 'approved'

        AND a.picked_up = 1

        AND r.id IS NULL


        /*
            Μόνο μέσα στις 48 ώρες
            από την παραλαβή.
        */

        AND a.pickup_date >= DATE_SUB(
            NOW(),
            INTERVAL 48 HOUR
        )


    ORDER BY
        a.pickup_date DESC

";


$result =
    $conn->query($sql);


if (!$result) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Σφάλμα βάσης δεδομένων.",

        "error" =>
            $conn->error

    ]);

    exit();

}


$ratings = [];


while ($row =
       $result->fetch_assoc()) {

    $ratings[] = $row;

}


/*
    Επιστρέφουμε JSON
*/

echo json_encode([

    "success" => true,

    "ratings" => $ratings

]);

?>