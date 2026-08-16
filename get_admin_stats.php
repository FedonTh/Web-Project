<?php

session_start();

require_once 'config.php';

header("Content-Type: application/json");


/*
    =========================
    ΕΛΕΓΧΟΣ ΣΥΝΔΕΣΗΣ
    =========================
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
    =========================
    ΕΛΕΓΧΟΣ ADMIN
    =========================
*/

$username = $_SESSION['username'];


$result = $conn->query("

    SELECT role

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


$user = $result->fetch_assoc();


if ($user['role'] !== 'admin') {

    echo json_encode([

        "success" => false,

        "message" =>
            "Δεν έχεις δικαιώματα Admin."

    ]);

    exit();

}


/*
    =========================
    ΣΥΝΟΛΙΚΕΣ ΜΕΡΙΔΕΣ
    =========================

    Μετράμε τις μερίδες
    που:

    1. Το αίτημα εγκρίθηκε
    2. Η μερίδα παραλήφθηκε
    3. Η παραλαβή έγινε
       τον τελευταίο μήνα
*/

$result = $conn->query("

    SELECT COUNT(*) AS total_meals

    FROM aitima

    WHERE status = 'approved'

    AND picked_up = TRUE

    AND pickup_date >=
        DATE_SUB(
            NOW(),
            INTERVAL 1 MONTH
        )

");


if (!$result) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Σφάλμα κατά την ανάκτηση των στατιστικών."

    ]);

    exit();

}


$row =
    $result->fetch_assoc();


$total_meals =
    $row['total_meals'];


/*
    =========================
    ΑΠΟΤΕΛΕΣΜΑ
    =========================
*/

echo json_encode([

    "success" => true,

    "total_meals" =>
        (int)$total_meals

]);

?>