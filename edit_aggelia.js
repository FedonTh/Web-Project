console.log("edit_aggelia.js φορτώθηκε");


document.getElementById("edit-aggelia-form")
    .addEventListener("submit", function(event) {

        event.preventDefault();


        console.log("Πατήθηκε Αποθήκευση Αλλαγών");


        let formData = new FormData(this);


        let data = {

            id: formData.get("id"),

            title: formData.get("title"),

            description: formData.get("description"),

            merides_total: formData.get("merides_total"),

            location: formData.get("location"),

            pickup_time: formData.get("pickup_time"),

            allergens: formData.getAll("allergens")

        };


        console.log("Δεδομένα:", data);


        fetch("edit_aggelia_process.php", {

            method: "POST",

            headers: {

                "Content-Type": "application/json"

            },

            body: JSON.stringify(data)

        })


        .then(function(response) {

            return response.json();

        })


        .then(function(result) {

            console.log("JSON:", result);


            let message =
                document.getElementById("message");


            if (result.success) {

                message.textContent =
                    result.message;

                message.style.color = "green";


                setTimeout(function() {

                    window.location.href =
                        "chef_page.php";

                }, 1000);

            }

            else {

                message.textContent =
                    result.message;

                message.style.color = "red";

            }

        })


        .catch(function(error) {

            console.log("ERROR:", error);

            document.getElementById("message")
                .textContent =
                "Παρουσιάστηκε σφάλμα.";

        });

    });