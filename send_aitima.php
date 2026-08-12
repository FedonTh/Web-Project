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
    Παίρνουμε τα δεδομένα
    από το fetch()
*/

$data = json_decode(
    file_get_contents("php://input"),
    true
);


if (!isset($data['aggelia_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Δεν δόθηκε αγγελία."
    ]);

    exit();

}


$aggelia_id = $data['aggelia_id'];


/*
    Βρίσκουμε τον χρήστη
*/

$username = $_SESSION['username'];


$result = $conn->query("

    SELECT id, credits

    FROM users

    WHERE username = '$username'

");


if (!$result || $result->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Ο χρήστης δεν βρέθηκε."
    ]);

    exit();

}


$user = $result->fetch_assoc();


$user_id = $user['id'];

$credits = $user['credits'];


/*
    Έλεγχος πόντων
*/

if ($credits < 1) {

    echo json_encode([
        "success" => false,
        "message" => "Δεν έχεις αρκετούς πόντους για να δεσμεύσεις μερίδα."
    ]);

    exit();

}


/*
    Βρίσκουμε την αγγελία
*/

$result = $conn->query("

    SELECT
        id,
        merides_left,
        status,
        expires_at

    FROM aggelia

    WHERE id = '$aggelia_id'

");


if (!$result || $result->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Η αγγελία δεν βρέθηκε."
    ]);

    exit();

}


$aggelia = $result->fetch_assoc();


/*
    Έλεγχος ενεργής αγγελίας
*/

if (
    $aggelia['status'] !== 'active'
    ||
    strtotime($aggelia['expires_at']) <= time()
) {

    echo json_encode([
        "success" => false,
        "message" => "Η αγγελία δεν είναι πλέον ενεργή."
    ]);

    exit();

}


/*
    Έλεγχος διαθέσιμων μερίδων

    ΔΕΝ μειώνουμε εδώ τη μερίδα.
    Η μερίδα θα μειωθεί μόνο
    όταν ο chef εγκρίνει το αίτημα.
*/

if ($aggelia['merides_left'] <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Δεν υπάρχουν διαθέσιμες μερίδες."
    ]);

    exit();

}


/*
    Έλεγχος αν ο χρήστης
    έχει ήδη κάνει αίτημα
*/

$result = $conn->query("

    SELECT id

    FROM aitima

    WHERE aggelia_id = '$aggelia_id'

    AND user_id = '$user_id'

    AND status IN ('pending', 'approved')

");


if ($result && $result->num_rows > 0) {

    echo json_encode([
        "success" => false,
        "message" => "Έχεις ήδη κάνει αίτημα για αυτή την αγγελία."
    ]);

    exit();

}


/*
    Δημιουργία αιτήματος
*/

$aitima_date = date("Y-m-d H:i:s");


$sql = "

    INSERT INTO aitima
    (
        aggelia_id,
        user_id,
        aitima_date,
        status,
        picked_up
    )

    VALUES
    (
        '$aggelia_id',
        '$user_id',
        '$aitima_date',
        'pending',
        FALSE
    )

";


if ($conn->query($sql)) {

    echo json_encode([

        "success" => true,

        "message" =>
            "Το αίτημα στάλθηκε επιτυχώς!"

    ]);

}
else {

    echo json_encode([

        "success" => false,

        "message" =>
            "Σφάλμα κατά την αποστολή του αιτήματος."

    ]);

}

?>