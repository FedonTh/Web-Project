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


$username = $_SESSION['username'];


/* Βρίσκουμε chef id */

$result = $conn->query(
    "SELECT id FROM users WHERE username = '$username'"
);


$user = $result->fetch_assoc();

$chef_id = $user['id'];


/*
    Μαρκάρουμε expired αγγελίες
    ως deleted.
*/

$conn->query("
    UPDATE aggelia
    SET status = 'deleted'
    WHERE chef_id = '$chef_id'
    AND status = 'active'
    AND expires_at <= NOW()
");


/* Παίρνουμε τις ενεργές αγγελίες */

$result = $conn->query("
    SELECT *
    FROM aggelia
    WHERE chef_id = '$chef_id'
    AND status = 'active'
    AND expires_at > NOW()
    ORDER BY created_at DESC
");


$aggelies = [];


while ($row = $result->fetch_assoc()) {

    $aggelies[] = $row;

}


echo json_encode([

    "success" => true,

    "aggelies" => $aggelies

]);

?>