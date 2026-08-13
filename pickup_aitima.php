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
    Παίρνουμε τα δεδομένα
*/

$data =
    json_decode(
        file_get_contents("php://input"),
        true
    );


$id =
    $data['id'];


$picked_up =
    $data['picked_up'];


/*
    Βρίσκουμε τον chef
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


$chef_id =
    $user['id'];


/*
    Βρίσκουμε το αίτημα.

    Ελέγχουμε επίσης ότι
    η αγγελία ανήκει στον chef.
*/

$result =
    $conn->query("

        SELECT

            aitima.id,

            aitima.user_id,

            aitima.status,

            aitima.picked_up,

            aggelia.chef_id

        FROM aitima

        INNER JOIN aggelia
            ON aitima.aggelia_id = aggelia.id

        WHERE aitima.id = '$id'

        AND aggelia.chef_id = '$chef_id'

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
    Πρέπει να είναι approved
    για να δηλωθεί παραλαβή.
*/

if ($aitima['status'] !== 'approved') {

    echo json_encode([

        "success" => false,

        "message" =>
            "Η παραλαβή μπορεί να δηλωθεί μόνο για εγκεκριμένο αίτημα."

    ]);

    exit();

}


/*
    Ελέγχουμε αν έχει ήδη
    καταχωρηθεί παραλαβή.
*/

if ($aitima['picked_up'] == 1) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Η παραλαβή έχει ήδη καταχωρηθεί."

    ]);

    exit();

}


/*
    ΠΑΡΕΛΗΦΘΗ
*/

if ($picked_up === true) {


    /*
        Σημειώνουμε ότι
        η μερίδα παραλήφθηκε
        και αποθηκεύουμε
        την ημερομηνία παραλαβής.
    */

    $result =
        $conn->query("

            UPDATE aitima

            SET

                picked_up = TRUE,

                pickup_date = NOW()

            WHERE id = '$id'

        ");


    if ($result) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Η παραλαβή καταχωρήθηκε επιτυχώς."

        ]);

    }
    else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Σφάλμα κατά την καταχώρηση της παραλαβής."

        ]);

    }

}


/*
    ΔΕΝ ΠΑΡΕΛΗΦΘΗ
*/

else {


    /*
        Σημειώνουμε ότι
        ΔΕΝ παρελήφθη.
    */

    $result =
        $conn->query("

            UPDATE aitima

            SET

                picked_up = FALSE,

                pickup_date = NULL

            WHERE id = '$id'

        ");


    if (!$result) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Σφάλμα κατά την ενημέρωση του αιτήματος."

        ]);

        exit();

    }


    /*
        Αφαιρούμε 1 credit
        από τον καταναλωτή.
    */

    $result =
        $conn->query("

            UPDATE users

            SET credits = credits - 1

            WHERE id = '{$aitima['user_id']}'

            AND credits > 0

        ");


    if (!$result) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Σφάλμα κατά την αφαίρεση του πόντου."

        ]);

        exit();

    }


    echo json_encode([

        "success" => true,

        "message" =>
            "Η μη παραλαβή καταχωρήθηκε και αφαιρέθηκε 1 πόντος από τον χρήστη."

    ]);

}

?>