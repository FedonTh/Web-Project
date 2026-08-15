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
*/

$title =
    $_POST['title'];

$description =
    $_POST['description'];

$merides_total =
    $_POST['merides_total'];

$location =
    $_POST['location'];

$pickup_time =
    $_POST['pickup_time'];


/*
    Αλλεργιογόνα
*/

if (isset($_POST['allergens'])) {

    if (is_array($_POST['allergens'])) {

        $allergens =
            implode(",", $_POST['allergens']);

    }
    else {

        $allergens =
            $_POST['allergens'];

    }

}
else {

    $allergens = "";

}


/*
    Upload φωτογραφίας
*/

$photo = "";


if (
    isset($_FILES['photo']) &&
    $_FILES['photo']['error'] === 0
) {

    $photo_name =
        $_FILES['photo']['name'];

    $photo_tmp =
        $_FILES['photo']['tmp_name'];


    $extension =
        pathinfo(
            $photo_name,
            PATHINFO_EXTENSION
        );


    $new_name =
        uniqid("food_") . "." . $extension;


    $upload_path =
        "uploads/" . $new_name;


    if (!is_dir("uploads")) {

        echo json_encode([

            "success" => false,

            "message" =>
                "Ο φάκελος uploads δεν υπάρχει."

        ]);

        exit();

    }


    if (
        move_uploaded_file(
            $photo_tmp,
            $upload_path
        )
    ) {

        $photo =
            $upload_path;

    }
    else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Η φωτογραφία δεν μπόρεσε να αποθηκευτεί."

        ]);

        exit();

    }

}


/*
    Βρίσκουμε τον μάγειρα
*/

$username =
    $_SESSION['username'];


$result =
    $conn->query(

        "SELECT id
         FROM users
         WHERE username = '$username'"

    );


if (!$result) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Σφάλμα κατά την αναζήτηση του χρήστη.",

        "error" =>
            $conn->error

    ]);

    exit();

}


if ($result->num_rows == 0) {

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
    Μερίδες
*/

$merides_left =
    $merides_total;


/*
    Ημερομηνίες
*/

$created_at =
    date("Y-m-d H:i:s");


$expires_at =
    date(
        "Y-m-d H:i:s",
        strtotime("+48 hours")
    );


/*
    ========================================
    ΤΥΧΑΙΑ ΘΕΣΗ ΣΤΟΝ ΧΑΡΤΗ
    ========================================

    Ενδεικτικό εύρος περιοχής
    Πανεπιστημίου Πατρών.
 
*/


$minLat = 38.2850;
$maxLat = 38.2900;

$minLng = 21.7750;
$maxLng = 21.7850;


/*
    Δημιουργούμε τυχαίες
    συντεταγμένες
*/

$latitude =
    $minLat +
    (mt_rand() / mt_getrandmax())
    * ($maxLat - $minLat);


$longitude =
    $minLng +
    (mt_rand() / mt_getrandmax())
    * ($maxLng - $minLng);


/*
    Δημιουργία αγγελίας
*/

$sql = "

    INSERT INTO aggelia

    (
        chef_id,
        title,
        description,
        photo,
        merides_total,
        merides_left,
        location,
        latitude,
        longitude,
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
        '$photo',
        '$merides_total',
        '$merides_left',
        '$location',
        '$latitude',
        '$longitude',
        '$pickup_time',
        '$created_at',
        '$expires_at',
        'active',
        '$allergens'
    )

";


/*
    Εκτέλεση INSERT
*/

if ($conn->query($sql)) {

    echo json_encode([

        "success" =>
            true,

        "message" =>
            "Η αγγελία δημιουργήθηκε επιτυχώς!",

        "id" =>
            $conn->insert_id

    ]);

}
else {

    echo json_encode([

        "success" =>
            false,

        "message" =>
            "Σφάλμα κατά τη δημιουργία της αγγελίας.",

        "error" =>
            $conn->error

    ]);

}

?>