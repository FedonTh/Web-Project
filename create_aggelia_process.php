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


$title = $data['title'];
$description = $data['description'];
$merides_total = $data['merides_total'];
$location = $data['location'];
$pickup_time = $data['pickup_time'];


if (isset($data['allergens'])) {

    $allergens = implode(",", $data['allergens']);

} else {

    $allergens = "";

}


/* Βρίσκουμε τον μάγειρα */

$username = $_SESSION['username'];

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


/* Μερίδες */

$merides_left = $merides_total;


/* Ημερομηνίες */

$created_at = date("Y-m-d H:i:s");

$expires_at = date(
    "Y-m-d H:i:s",
    strtotime("+48 hours")
);


/* Δημιουργία αγγελίας */

$sql = "INSERT INTO aggelia
(
    chef_id,
    title,
    description,
    merides_total,
    merides_left,
    location,
    pickup_time,
    created_at,
    expires_at,
    status,
    allergens
)

VALUES
(
    '$chef_id',
    '$title',
    '$description',
    '$merides_total',
    '$merides_left',
    '$location',
    '$pickup_time',
    '$created_at',
    '$expires_at',
    'active',
    '$allergens'
)";


if ($conn->query($sql)) {

    echo json_encode([
        "success" => true,
        "message" => "Η αγγελία δημιουργήθηκε επιτυχώς!",
        "id" => $conn->insert_id
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Σφάλμα κατά τη δημιουργία της αγγελίας."
    ]);

}

?>



