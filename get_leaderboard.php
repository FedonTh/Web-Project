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
    =========================
    TOP DONOR
    =========================

    Μετράμε τις μερίδες
    που παραλήφθηκαν επιτυχώς.

    picked_up = TRUE
*/


$sql = "

    SELECT

        users.username,

        COUNT(*) AS meals

    FROM aitima

    INNER JOIN aggelia

        ON aitima.aggelia_id =
           aggelia.id

    INNER JOIN users

        ON aggelia.chef_id =
           users.id

    WHERE aitima.status = 'approved'

    AND aitima.picked_up = TRUE

    GROUP BY aggelia.chef_id

    ORDER BY meals DESC

    LIMIT 1

";


$result =
    $conn->query($sql);


$top_donor = null;


if ($result && $result->num_rows > 0) {

    $top_donor =
        $result->fetch_assoc();

}


/*
    =========================
    ΥΨΗΛΟΤΕΡΕΣ ΑΞΙΟΛΟΓΗΣΕΙΣ
    =========================

    Υπολογίζουμε τον μέσο
    όρο κάθε αγγελίας.
*/


$sql = "

    SELECT

        aggelia.title,

        users.username,

        AVG(rating.rating)
            AS average_rating,

        COUNT(rating.id)
            AS rating_count

    FROM rating

    INNER JOIN aitima

        ON rating.aitima_id =
           aitima.id

    INNER JOIN aggelia

        ON aitima.aggelia_id =
           aggelia.id

    INNER JOIN users

        ON aggelia.chef_id =
           users.id

    GROUP BY

        aggelia.id

    ORDER BY

        average_rating DESC

";


$result =
    $conn->query($sql);


$top_rated = [];


if ($result) {

    while ($row =
        $result->fetch_assoc()
    ) {

        $top_rated[] =
            $row;

    }

}


/*
    Επιστροφή JSON
*/

echo json_encode([

    "success" => true,

    "top_donor" =>
        $top_donor,

    "top_rated" =>
        $top_rated

]);

?>