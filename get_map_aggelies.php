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
    Παίρνουμε ΟΛΕΣ τις
    ενεργές αγγελίες
*/

$result = $conn->query("

    SELECT

        id,

        title,

        description,

        photo,

        merides_total,

        merides_left,

        location,

        pickup_time,

        allergens,

        latitude,

        longitude

    FROM aggelia

    WHERE status = 'active'

    AND expires_at > NOW()

    ORDER BY created_at DESC

");


if (!$result) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Σφάλμα κατά την ανάκτηση των αγγελιών."

    ]);

    exit();

}


$aggelies = [];


while ($row = $result->fetch_assoc()) {

    $aggelies[] = $row;

}


/*
    Επιστρέφουμε JSON
*/

echo json_encode([

    "success" => true,

    "aggelies" => $aggelies

]);

?>