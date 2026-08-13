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
    Παίρνουμε JSON
*/

$data =
    json_decode(
        file_get_contents("php://input"),
        true
    );


$aitima_id =
    $data['aitima_id'] ?? null;


$rating =
    $data['rating'] ?? null;


$comment =
    $data['comment'] ?? "";


/*
    Βασικός έλεγχος
*/

if (!$aitima_id || !$rating) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Λείπουν δεδομένα."

    ]);

    exit();

}


/*
    Έλεγχος βαθμολογίας
*/

$rating =
    intval($rating);


if ($rating < 1 || $rating > 5) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Η βαθμολογία πρέπει να είναι από 1 έως 5."

    ]);

    exit();

}


/*
    Βρίσκουμε τον καταναλωτή
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
    Βρίσκουμε το αίτημα
*/

$result =
    $conn->query("

        SELECT

            a.id,

            a.user_id,

            a.aggelia_id,

            a.status,

            a.picked_up,

            a.pickup_date,

            ag.chef_id

        FROM aitima a


        INNER JOIN aggelia ag

            ON a.aggelia_id = ag.id


        WHERE a.id = '$aitima_id'

        AND a.user_id = '$user_id'

    ");


if (!$result || $result->num_rows == 0) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Το αίτημα δεν βρέθηκε."

    ]);

    exit();

}


$aitima =
    $result->fetch_assoc();


/*
    Πρέπει να έχει παραληφθεί
*/

if (
    $aitima['status'] !== 'approved'
    ||
    $aitima['picked_up'] != 1
) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Μπορείς να αξιολογήσεις μόνο φαγητό που έχεις παραλάβει."

    ]);

    exit();

}


/*
    Έλεγχος 48 ωρών
*/

if (
    empty($aitima['pickup_date'])
    ||
    strtotime($aitima['pickup_date'])
        < strtotime("-48 hours")
) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Έχουν περάσει περισσότερες από 48 ώρες από την παραλαβή."

    ]);

    exit();

}


/*
    Έλεγχος αν έχει ήδη
    αξιολογηθεί
*/

$result =
    $conn->query("

        SELECT id

        FROM rating

        WHERE aitima_id = '$aitima_id'

    ");


if (
    $result
    &&
    $result->num_rows > 0
) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Αυτό το αίτημα έχει ήδη αξιολογηθεί."

    ]);

    exit();

}


/*
    Υπολογίζουμε τους πόντους
    του μάγειρα.

    1-3 αστέρια = +1
    4-5 αστέρια = +2
*/

if ($rating >= 4) {

    $points = 2;

}
else {

    $points = 1;

}


/*
    Ημερομηνία αξιολόγησης
*/

$rating_date =
    date("Y-m-d H:i:s");


/*
    Προσθέτουμε την αξιολόγηση
*/

$sql = "

    INSERT INTO rating

    (
        aitima_id,
        rating,
        comment,
        rating_date
    )

    VALUES

    (
        '$aitima_id',
        '$rating',
        '$comment',
        '$rating_date'
    )

";


if (!$conn->query($sql)) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Σφάλμα κατά την αποθήκευση της αξιολόγησης.",

        "error" =>
            $conn->error

    ]);

    exit();

}


/*
    Προσθέτουμε τους πόντους
    στον ΜΑΓΕΙΡΑ.

    Όχι στον καταναλωτή.
*/

$chef_id =
    $aitima['chef_id'];


$sql = "

    UPDATE users

    SET credits = credits + $points

    WHERE id = '$chef_id'

";


if (!$conn->query($sql)) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Η αξιολόγηση αποθηκεύτηκε,
             αλλά υπήρξε πρόβλημα με
             την προσθήκη πόντων."

    ]);

    exit();

}


/*
    Επιτυχία
*/

echo json_encode([

    "success" => true,

    "message" =>
        "Η αξιολόγηση καταχωρήθηκε επιτυχώς! Ο μάγειρας κέρδισε "
        . $points
        . " πόντο"
        . ($points == 1 ? "" : "υς")
        . "."

]);

?>