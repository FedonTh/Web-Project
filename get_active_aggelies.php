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

        "message" => "Δεν είσαι συνδεδεμένος."

    ]);

    exit();

}


/*
    Μαρκάρουμε τις
    ληγμένες αγγελίες
*/

$conn->query("

    UPDATE aggelia

    SET status = 'deleted'

    WHERE status = 'active'

    AND expires_at <= NOW()

");


/*
    Παίρνουμε όλες τις
    ενεργές αγγελίες
*/

$result = $conn->query("

    SELECT

        id,
        chef_id,
        title,
        description,
        photo,
        merides_total,
        merides_left,
        location,
        pickup_time,
        created_at,
        expires_at,
        status,
        allergens

    FROM aggelia

    WHERE status = 'active'

    AND expires_at > NOW()

    ORDER BY created_at DESC

");


if (!$result) {

    echo json_encode([

        "success" => false,

        "message" => "Σφάλμα κατά την ανάκτηση των αγγελιών."

    ]);

    exit();

}


$aggelies = [];


while ($row = $result->fetch_assoc()) {

    $aggelies[] = $row;

}


echo json_encode([

    "success" => true,

    "aggelies" => $aggelies

]);

?>