console.log("admin_page.js φορτώθηκε");


/*
    =========================
    ΦΟΡΤΩΣΗ ΣΤΑΤΙΣΤΙΚΩΝ
    =========================
*/

document.addEventListener(
    "DOMContentLoaded",
    function() {

        loadStatistics();

    }
);


/*
    =========================
    GET STATISTICS
    =========================
*/

function loadStatistics() {

    fetch("get_admin_stats.php")


        .then(function(response) {

            return response.json();

        })


        .then(function(data) {

            console.log(
                "Admin statistics:",
                data
            );


            if (data.success) {

                document.getElementById(
                    "total-meals"
                ).textContent =
                    data.total_meals;

            }

            else {

                document.getElementById(
                    "total-meals"
                ).textContent =
                    "0";


                document.getElementById(
                    "message"
                ).textContent =
                    data.message;

            }

        })


        .catch(function(error) {

            console.log(
                "ERROR:",
                error
            );


            document.getElementById(
                "total-meals"
            ).textContent =
                "0";


            document.getElementById(
                "message"
            ).textContent =
                "Παρουσιάστηκε σφάλμα.";

        });

}