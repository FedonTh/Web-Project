<?php

session_start();
require_once 'config.php';


if (!isset($_SESSION['username'])) {

    header("Location: index.php");

    exit();

}


if (!isset($_GET['id'])) {

    header("Location: chef_page.php");

    exit();

}


$id =
    $_GET['id'];

$username =
    $_SESSION['username'];


/* Βρίσκουμε τον chef */

$result =
    $conn->query(
        "SELECT id
         FROM users
         WHERE username = '$username'"
    );


$user =
    $result->fetch_assoc();

$chef_id =
    $user['id'];


/* Παίρνουμε την αγγελία */

$result =
    $conn->query("
        SELECT *
        FROM aggelia
        WHERE id = '$id'
        AND chef_id = '$chef_id'
    ");


if ($result->num_rows == 0) {

    header("Location: chef_page.php");

    exit();

}


$aggelia =
    $result->fetch_assoc();


/* Αλλεργιογόνα */

$selected_allergens = [];


if (!empty($aggelia['allergens'])) {

    $selected_allergens =
        explode(",", $aggelia['allergens']);

}

?>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    UniBite - Επεξεργασία Αγγελίας
</title>

<link rel="stylesheet" href="style.css">


<div class="logo">
    UniBite
</div>


<div class="create-aggelia-container">

    <h1>
        Επεξεργασία Αγγελίας
    </h1>


    <form id="edit-aggelia-form">


        <input
            type="hidden"
            id="id"
            name="id"
            value="<?= $aggelia['id'] ?>"
        >


        <label for="title">
            Τίτλος Αγγελίας
        </label>

        <input
            type="text"
            id="title"
            name="title"
            value="<?= htmlspecialchars($aggelia['title']) ?>"
            maxlength="100"
            required
        >


        <label for="description">
            Περιγραφή
        </label>

        <textarea
            id="description"
            name="description"
            rows="5"
        ><?= htmlspecialchars($aggelia['description']) ?></textarea>


        <label for="merides_total">
            Συνολικές Μερίδες
        </label>

        <input
            type="number"
            id="merides_total"
            name="merides_total"
            value="<?= $aggelia['merides_total'] ?>"
            min="1"
            required
        >


        <label for="location">
            Τοποθεσία Παραλαβής
        </label>

        <input
            type="text"
            id="location"
            name="location"
            value="<?= htmlspecialchars($aggelia['location']) ?>"
            maxlength="255"
            required
        >


        <label for="pickup_time">
            Ώρα Παραλαβής
        </label>

        <input
            type="datetime-local"
            id="pickup_time"
            name="pickup_time"
            value="<?= date(
                'Y-m-d\TH:i',
                strtotime($aggelia['pickup_time'])
            ) ?>"
            required
        >


        <!-- =========================
             ΦΩΤΟΓΡΑΦΙΑ
             ========================= -->

        <label>
            Φωτογραφία
        </label>


        <?php if (!empty($aggelia['photo'])): ?>

            <img
                src="<?= htmlspecialchars($aggelia['photo']) ?>"
                alt="Τρέχουσα φωτογραφία"
                class="edit-aggelia-photo"
            >

        <?php else: ?>

            <div class="edit-no-photo">
                Δεν υπάρχει φωτογραφία
            </div>

        <?php endif; ?>


        <label for="photo">
            Επιλογή νέας φωτογραφίας
        </label>

        <input
            type="file"
            id="photo"
            name="photo"
            accept="image/*"
        >


        <p class="photo-info">
            Αν δεν επιλέξεις νέα φωτογραφία,
            θα παραμείνει η υπάρχουσα.
        </p>


        <!-- =========================
             ΑΛΛΕΡΓΙΟΓΟΝΑ
             ========================= -->

        <label>
            Αλλεργιογόνα
        </label>


        <div class="allergens">

            <?php

            $allergens = [

                "Cereals containing gluten",

                "Crustaceans",

                "Eggs",

                "Fish",

                "Peanuts",

                "Soybeans",

                "Milk",

                "Nuts",

                "Celery",

                "Mustard",

                "Sesame seeds",

                "Sulphur dioxide and sulphites",

                "Lupin",

                "Molluscs"

            ];


            foreach ($allergens as $allergen):

            ?>


                <label>

                    <input
                        type="checkbox"
                        name="allergens[]"
                        value="<?= $allergen ?>"

                        <?php

                        if (
                            in_array(
                                $allergen,
                                $selected_allergens
                            )
                        ) {

                            echo "checked";

                        }

                        ?>
                    >

                    <?= $allergen ?>

                </label>


            <?php endforeach; ?>

        </div>


        <button
            type="submit"
            class="create-aggelia-button"
        >

            Αποθήκευση Αλλαγών

        </button>


    </form>


    <button
        class="cancel-button"
        onclick="window.location.href='chef_page.php'"
    >

        Ακύρωση

    </button>


    <p id="message"></p>

</div>


<script src="edit_aggelia.js"></script>
