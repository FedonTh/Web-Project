<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="el">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UniBite - Δημιουργία Αγγελίας</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="logo">
        UniBite
    </div>


    <div class="create-aggelia-container">

        <h1>Δημιουργία Αγγελίας</h1>

        <form id="aggelia-form">

            <label for="title">Τίτλος Αγγελίας</label>

            <input
                type="text"
                id="title"
                name="title"
                placeholder="π.χ. Μακαρόνια με κιμά"
                maxlength="100"
                required
            >


            <label for="description">Περιγραφή</label>

            <textarea
                id="description"
                name="description"
                placeholder="Περιέγραψε το φαγητό..."
                rows="5"
            ></textarea>


            <label for="merides_total">Συνολικές Μερίδες</label>

            <input
                type="number"
                id="merides_total"
                name="merides_total"
                min="1"
                required
            >


            <label for="location">Τοποθεσία Παραλαβής</label>

            <input
                type="text"
                id="location"
                name="location"
                placeholder="π.χ. Φοιτητική Εστία"
                maxlength="255"
                required
            >


            <label for="pickup_time">Ώρα Παραλαβής</label>

            <input
                type="datetime-local"
                id="pickup_time"
                name="pickup_time"
                required
            >

            <label>Αλλεργιογόνα</label>

            <div class="allergens">

                <label>
                    <input type="checkbox" name="allergens" value="Cereals containing gluten">
                    Cereals containing gluten
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Crustaceans">
                    Crustaceans
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Eggs">
                    Eggs
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Fish">
                    Fish
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Peanuts">
                    Peanuts
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Soybeans">
                    Soybeans
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Milk">
                    Milk
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Nuts">
                    Nuts
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Celery">
                    Celery
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Mustard">
                    Mustard
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Sesame seeds">
                    Sesame seeds
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Sulphur dioxide and sulphites">
                    Sulphur dioxide and sulphites
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Lupin">
                    Lupin
                </label>

                <label>
                    <input type="checkbox" name="allergens" value="Molluscs">
                    Molluscs
                </label>

            </div>


            <button
                type="submit"
                class="create-aggelia-button">
                Δημιουργία Αγγελίας
            </button>

        </form>


        <button
            class="cancel-button"
            onclick="window.location.href='chef_page.php'">
            Ακύρωση
        </button>


        <p id="message"></p>

    </div>


    <script src="create_aggelia.js"></script>

</body>

</html>

