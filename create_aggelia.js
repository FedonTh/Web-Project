console.log("create_aggelia.js φορτώθηκε");


document.getElementById("aggelia-form").addEventListener("submit", function(event) {

    event.preventDefault();

    console.log("Πατήθηκε Δημιουργία Αγγελίας");


    // Παίρνουμε όλα τα δεδομένα της φόρμας
    let formData = new FormData(this);


    // Εμφανίζουμε τι στέλνουμε
    console.log("FormData:");

    for (let pair of formData.entries()) {

        console.log(pair[0], pair[1]);

    }


    fetch("create_aggelia_process.php", {

        method: "POST",

        body: formData

    })


    .then(function(response) {

        console.log("HTTP Status:", response.status);

        return response.text();

    })


    .then(function(text) {

        console.log("Απάντηση PHP:");

        console.log(text);


        /*
            Προσπαθούμε να μετατρέψουμε
            την απάντηση σε JSON
        */

        let result;

        try {

            result = JSON.parse(text);

        }

        catch (error) {

            console.log(
                "Η PHP δεν επέστρεψε έγκυρο JSON."
            );

            console.log(
                "Πραγματική απάντηση:",
                text
            );


            document.getElementById("message").textContent =
                "Η PHP επέστρεψε σφάλμα. Δες την Console.";

            document.getElementById("message").style.color =
                "red";

            return;

        }


        console.log("JSON:", result);


        let message =
            document.getElementById("message");


        if (result.success) {

            message.textContent =
                result.message;

            message.style.color =
                "green";


            setTimeout(function() {

                window.location.href =
                    "chef_page.php";

            }, 1000);

        }

        else {

            message.textContent =
                result.message;

            message.style.color =
                "red";

        }

    })


    .catch(function(error) {

        console.log("FETCH ERROR:", error);

        document.getElementById("message").textContent =
            "Παρουσιάστηκε σφάλμα.";

    });

});


