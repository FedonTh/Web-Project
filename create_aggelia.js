console.log("create_aggelia.js φορτώθηκε");


document.getElementById("aggelia-form").addEventListener("submit", function(event) {

    event.preventDefault();

    console.log("Πατήθηκε Δημιουργία Αγγελίας");


    // Παίρνουμε όλα τα δεδομένα της φόρμας
    let formData = new FormData(this);


    // Παίρνουμε τα επιλεγμένα αλλεργιογόνα
    let allergens = formData.getAll("allergens");


    // Δημιουργούμε το αντικείμενο που θα στείλουμε ως JSON
    let data = {

        title: formData.get("title"),

        description: formData.get("description"),

        merides_total: formData.get("merides_total"),

        location: formData.get("location"),

        pickup_time: formData.get("pickup_time"),

        allergens: allergens

    };


    console.log("Δεδομένα:", data);


    fetch("create_aggelia_process.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify(data)

    })


    .then(function(response) {

        console.log("Απάντηση από PHP:", response);

        return response.json();

    })


    .then(function(result) {

        console.log("JSON:", result);


        let message = document.getElementById("message");


        if (result.success) {

            message.textContent = result.message;

            message.style.color = "green";


            setTimeout(function() {

                window.location.href = "chef_page.php";

            }, 1000);

        }

        else {

            message.textContent = result.message;

            message.style.color = "red";

        }

    })


    .catch(function(error) {

        console.log("ERROR:", error);

        document.getElementById("message").textContent =
            "Παρουσιάστηκε σφάλμα.";

    });

});
