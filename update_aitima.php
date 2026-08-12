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


/*
    Παίρνουμε JSON
*/

$data =
    json_decode(
        file_get_contents("php://input"),
        true
    );


$id =
    $data['id'];

$status =
    $data['status'];


/*
    Ελέγχουμε status
*/

if (
    $status !== "approved"
    &&
    $status !== "rejected"
) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Μη έγκυρη κατάσταση."

    ]);

    exit();

}


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
    Βρίσκουμε το αίτημα
    και ελέγχουμε ότι η αγγελία
    ανήκει στον συγκεκριμένο chef.
*/

$result =
    $conn->query("

        SELECT

            aitima.id,

            aitima.status,

            aitima.aggelia_id,

            aggelia.merides_left

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
    Μπορούμε να αλλάξουμε
    μόνο pending αίτημα.
*/

if ($aitima['status'] !== 'pending') {

    echo json_encode([

        "success" => false,

        "message" =>
            "Το αίτημα έχει ήδη επεξεργαστεί."

    ]);

    exit();

}


/*
    ΕΓΚΡΙΣΗ
*/

if ($status === "approved") {


    /*
        Ελέγχουμε αν υπάρχει
        διαθέσιμη μερίδα.
    */

    if ($aitima['merides_left'] <= 0) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Δεν υπάρχουν διαθέσιμες μερίδες."

        ]);

        exit();

    }


    /*
        Μειώνουμε τη διαθέσιμη
        μερίδα κατά 1.
    */

    $result =
        $conn->query("

            UPDATE aggelia

            SET merides_left =
                merides_left - 1

            WHERE id =
                '{$aitima['aggelia_id']}'

            AND merides_left > 0

        ");


    if (!$result || $conn->affected_rows == 0) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Δεν ήταν δυνατή η μείωση της μερίδας."

        ]);

        exit();

    }


    /*
        Αλλάζουμε το αίτημα
        σε approved.
    */

    $result =
        $conn->query("

            UPDATE aitima

            SET status = 'approved'

            WHERE id = '$id'

        ");


    if ($result) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Το αίτημα εγκρίθηκε και η διαθέσιμη μερίδα μειώθηκε κατά 1."

        ]);

    }
    else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Σφάλμα κατά την έγκριση του αιτήματος."

        ]);

    }

}


/*
    ΑΠΟΡΡΙΨΗ
*/

else if ($status === "rejected") {


    /*
        Δεν αλλάζουμε τις μερίδες,
        επειδή δεν είχαν μειωθεί
        κατά την αποστολή του αιτήματος.
    */

    $result =
        $conn->query("

            UPDATE aitima

            SET status = 'rejected'

            WHERE id = '$id'

        ");


    if ($result) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Το αίτημα απορρίφθηκε."

        ]);

    }
    else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Σφάλμα κατά την απόρριψη του αιτήματος."

        ]);

    }

}

?>