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

$username = $_SESSION['username'];


/* Βρίσκουμε chef */

$result = $conn->query(
    "SELECT id FROM users WHERE username = '$username'"
);


$user = $result->fetch_assoc();

$chef_id = $user['id'];


/*
    Διαγράφουμε μόνο αν
    η αγγελία ανήκει στον συγκεκριμένο chef.
*/

$sql = "
    UPDATE aggelia

    SET status = 'deleted'

    WHERE id = '$id'

    AND chef_id = '$chef_id'
";


if ($conn->query($sql)) {

    echo json_encode([
        "success" => true,
        "message" => "Η αγγελία διαγράφηκε."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Σφάλμα κατά τη διαγραφή."
    ]);

}

?>