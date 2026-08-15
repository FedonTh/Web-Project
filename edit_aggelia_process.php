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

$id =
    $_POST['id'];

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
            implode(
                ",",
                $_POST['allergens']
            );

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
    Username
*/

$username =
    $_SESSION['username'];


/*
    Βρίσκουμε τον chef
*/

$result =
    $conn->query(

        "SELECT id
         FROM users
         WHERE username = '$username'"

    );


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
    Παίρνουμε την υπάρχουσα αγγελία
*/

$result =
    $conn->query("

        SELECT

            merides_total,
            merides_left,
            photo,
            location,
            latitude,
            longitude

        FROM aggelia

        WHERE id = '$id'

        AND chef_id = '$chef_id'

    ");


if (!$result || $result->num_rows == 0) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Η αγγελία δεν βρέθηκε."

    ]);

    exit();

}


$old =
    $result->fetch_assoc();


/*
    Υπολογίζουμε πόσες μερίδες
    έχουν ήδη δοθεί.
*/

$used_merides =
    $old['merides_total']
    -
    $old['merides_left'];


/*
    Έλεγχος μερίδων
*/

if ($merides_total < $used_merides) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Οι συνολικές μερίδες δεν μπορούν να είναι λιγότερες από τις μερίδες που έχουν ήδη δοθεί."

    ]);

    exit();

}


$merides_left =
    $merides_total
    -
    $used_merides;


/*
    ========================================
    ΣΥΝΤΕΤΑΓΜΕΝΕΣ
    ========================================
*/


/*
    Από προεπιλογή κρατάμε
    τις παλιές συντεταγμένες.
*/

$latitude =
    $old['latitude'];

$longitude =
    $old['longitude'];


/*
    Αν άλλαξε η τοποθεσία,
    δημιουργούμε νέα τυχαία θέση.
*/

if ($location !== $old['location']) {


    /*
        Ενδεικτικό εύρος
        Πανεπιστημίου Πατρών
    */

    $minLat = 38.2850;
    $maxLat = 38.2900;

    $minLng = 21.7750;
    $maxLng = 21.7850;


    $latitude =
        $minLat +
        (mt_rand() / mt_getrandmax())
        * ($maxLat - $minLat);


    $longitude =
        $minLng +
        (mt_rand() / mt_getrandmax())
        * ($maxLng - $minLng);

}


/*
    Κρατάμε την παλιά φωτογραφία
    αν δεν ανέβει καινούρια.
*/

$photo =
    $old['photo'];


/*
    Ελέγχουμε νέα φωτογραφία
*/

if (
    isset($_FILES['photo'])
    &&
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
        uniqid("food_")
        . "."
        . $extension;


    $upload_path =
        "uploads/"
        .
        $new_name;


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


        /*
            Διαγράφουμε την παλιά
            φωτογραφία.
        */

        if (
            !empty($old['photo'])
            &&
            file_exists($old['photo'])
        ) {

            unlink(
                $old['photo']
            );

        }

    }
    else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Η νέα φωτογραφία δεν μπόρεσε να αποθηκευτεί."

        ]);

        exit();

    }

}


/*
    UPDATE
*/

$sql = "

    UPDATE aggelia

    SET

        title = '$title',

        description = '$description',

        photo = '$photo',

        merides_total = '$merides_total',

        merides_left = '$merides_left',

        location = '$location',

        latitude = '$latitude',

        longitude = '$longitude',

        pickup_time = '$pickup_time',

        allergens = '$allergens'

    WHERE id = '$id'

    AND chef_id = '$chef_id'

";


/*
    Εκτέλεση UPDATE
*/

if ($conn->query($sql)) {

    echo json_encode([

        "success" => true,

        "message" =>
            "Η αγγελία ενημερώθηκε επιτυχώς!"

    ]);

}
else {

    echo json_encode([

        "success" => false,

        "message" =>
            "Σφάλμα κατά την ενημέρωση της αγγελίας.",

        "error" =>
            $conn->error

    ]);

}

?>