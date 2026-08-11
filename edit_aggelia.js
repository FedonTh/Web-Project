console.log("edit_aggelia.js φορτώθηκε");


document
    .getElementById("edit-aggelia-form")
    .addEventListener("submit", function(event) {

        event.preventDefault();


        console.log(
            "Πατήθηκε Αποθήκευση Αλλαγών"
        );


        /*
            Παίρνουμε ολόκληρη τη φόρμα.

            Το FormData περιλαμβάνει:
            - text
            - αριθμούς
            - allergens
            - φωτογραφία
        */

        let formData =
            new FormData(this);


        /*
            Debug
        */

        console.log("FormData:");

        for (
            let pair of formData.entries()
        ) {

            console.log(
                pair[0],
                pair[1]
            );

        }


        /*
            Στέλνουμε το FormData
            στο PHP.
        */

        fetch(
            "edit_aggelia_process.php",
            {

                method: "POST",

                body: formData

            }
        )


        .then(function(response) {

            console.log(
                "HTTP Status:",
                response.status
            );


            return response.json();

        })


        .then(function(result) {

            console.log(
                "JSON:",
                result
            );


            let message =
                document.getElementById(
                    "message"
                );


            if (result.success) {

                message.textContent =
                    result.message;

                message.style.color =
                    "green";


                setTimeout(
                    function() {

                        window.location.href =
                            "chef_page.php";

                    },
                    1000
                );

            }

            else {

                message.textContent =
                    result.message;

                message.style.color =
                    "red";

            }

        })


        .catch(function(error) {

            console.log(
                "ERROR:",
                error
            );


            document
                .getElementById("message")
                .textContent =
                "Παρουσιάστηκε σφάλμα.";

        });

    });
