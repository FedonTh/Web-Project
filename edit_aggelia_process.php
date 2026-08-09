<?php

session_start();
require_once 'config.php';

header("Content-Type: application/json");


if (!isset($_SESSION['username'])) {

    echo json_encode([
        "success" => false,
        "message" => "Δεν είσαι συνδεδεμένος."
    ]);

    exit();
}


$data = json_decode(
    file_get_contents("php://input"),
    true
);


$id = $data['id'];

$title = $data['title'];

$description = $data['description'];

$merides_total = $data['merides_total'];

$location = $data['location'];

$pickup_time = $data['pickup_time'];


if (isset($data['allergens'])) {

    $allergens =
        implode(",", $data['allergens']);

} else {

    $allergens = "";

}


$username = $_SESSION['username'];


/* Βρίσκουμε τον chef */

$result = $conn->query(
    "SELECT id FROM users WHERE username = '$username'"
);


if ($result->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Ο χρήστης δεν βρέθηκε."
    ]);

    exit();

}


$user = $result->fetch_assoc();

$chef_id = $user['id'];


/*
    Υπολογίζουμε πόσες μερίδες
    είχαν ήδη δοθεί.
*/

$result = $conn->query("
    SELECT merides_total, merides_left
    FROM aggelia
    WHERE id = '$id'
    AND chef_id = '$chef_id'
");


if ($result->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Η αγγελία δεν βρέθηκε."
    ]);

    exit();

}


$old = $result->fetch_assoc();


$used_merides =
    $old['merides_total'] - $old['merides_left'];


/*
    Δεν επιτρέπουμε να βάλει
    συνολικές μερίδες λιγότερες
    από όσες έχουν ήδη δοθεί.
*/

if ($merides_total < $used_merides) {

    echo json_encode([
        "success" => false,
        "message" =>
        "Οι συνολικές μερίδες δεν μπορούν να είναι λιγότερες από τις μερίδες που έχουν ήδη δοθεί."
    ]);

    exit();

}


$merides_left =
    $merides_total - $used_merides;


/* UPDATE */

$sql = "

    UPDATE aggelia

    SET

        title = '$title',

        description = '$description',

        merides_total = '$merides_total',

        merides_left = '$merides_left',

        location = '$location',

        pickup_time = '$pickup_time',

        allergens = '$allergens'

    WHERE id = '$id'

    AND chef_id = '$chef_id'

";


if ($conn->query($sql)) {

    echo json_encode([

        "success" => true,

        "message" =>
        "Η αγγελία ενημερώθηκε επιτυχώς!"

    ]);

}

else {

    echo json_encode([

        "success" => false,

        "message" =>
        "Σφάλμα κατά την ενημέρωση της αγγελίας."

    ]);

}

?>