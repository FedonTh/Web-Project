console.log("chef_aitimata.js φορτώθηκε");


document.addEventListener("DOMContentLoaded", function() {

    loadAitimata();

});


/* =========================
   LOAD AITIMATA
   ========================= */

function loadAitimata() {

    fetch("get_aitimata.php")

        .then(function(response) {

            return response.json();

        })

        .then(function(data) {

            console.log("Αιτήματα:", data);


            if (data.success) {

                displayAitimata(data.aitimata);

            }
            else {

                document.getElementById(
                    "aitimata-container"
                ).innerHTML = `
                    <p>${data.message}</p>
                `;

            }

        })

        .catch(function(error) {

            console.log("ERROR:", error);

            document.getElementById(
                "aitimata-container"
            ).innerHTML = `
                <p>Παρουσιάστηκε σφάλμα.</p>
            `;

        });

}


/* =========================
   DISPLAY AITIMATA
   ========================= */

function displayAitimata(aitimata) {

    const container =
        document.getElementById(
            "aitimata-container"
        );


    container.innerHTML = "";


    if (aitimata.length === 0) {

        container.innerHTML = `
            <p>
                Δεν υπάρχουν αιτήματα.
            </p>
        `;

        return;

    }


    aitimata.forEach(function(aitima) {

        addAitimaCard(aitima);

    });

}


/* =========================
   CREATE CARD
   ========================= */

function addAitimaCard(aitima) {

    const container =
        document.getElementById(
            "aitimata-container"
        );


    const card =
        document.createElement("div");


    card.classList.add("aggelia-card");


    card.id =
        "aitima-" + aitima.id;


    let buttonsHTML = "";


    /*
        PENDING
    */

    if (aitima.status === "pending") {

        buttonsHTML = `

            <div class="aggelia-buttons">

                <button
                    onclick="updateAitima(
                        ${aitima.id},
                        'approved'
                    )">

                    Έγκριση

                </button>


                <button
                    class="delete-button"
                    onclick="updateAitima(
                        ${aitima.id},
                        'rejected'
                    )">

                    Απόρριψη

                </button>

            </div>

        `;

    }


    /*
        APPROVED
    */

    else if (
        aitima.status === "approved"
        &&
        aitima.picked_up == 0
    ) {

        buttonsHTML = `

            <div class="aggelia-buttons">

                <button
                    onclick="pickupAitima(
                        ${aitima.id},
                        true
                    )">

                    ✓ Παρελήφθη

                </button>


                <button
                    class="delete-button"
                    onclick="pickupAitima(
                        ${aitima.id},
                        false
                    )">

                    ✗ Δεν παραλήφθηκε

                </button>

            </div>

        `;

    }


    /*
        APPROVED + PICKED UP
    */

    else if (
        aitima.status === "approved"
        &&
        aitima.picked_up == 1
    ) {

        buttonsHTML = `

            <p style="color: green; font-weight: bold;">

                ✓ Η μερίδα παρελήφθη

            </p>

        `;

    }


    card.innerHTML = `

        <h3>
            ${aitima.title}
        </h3>


        <p>

            <strong>Χρήστης:</strong>
            ${aitima.username}

        </p>


        <p>

            <strong>Ημερομηνία αιτήματος:</strong>
            ${aitima.aitima_date}

        </p>


        <p>

            <strong>Διαθέσιμες μερίδες:</strong>
            ${aitima.merides_left}

        </p>


        <p>

            <strong>Τοποθεσία:</strong>
            ${aitima.location}

        </p>


        <p>

            <strong>Ώρα παραλαβής:</strong>
            ${aitima.pickup_time}

        </p>


        <p>

            <strong>Κατάσταση:</strong>
            ${getStatusText(aitima.status)}

        </p>


        ${buttonsHTML}

    `;


    container.appendChild(card);

}


/* =========================
   STATUS TEXT
   ========================= */

function getStatusText(status) {

    if (status === "pending") {
        return "Εκκρεμεί";
    }


    if (status === "approved") {
        return "Εγκρίθηκε";
    }


    if (status === "rejected") {
        return "Απορρίφθηκε";
    }


    return status;

}


/* =========================
   APPROVE / REJECT
   ========================= */

function updateAitima(id, status) {

    let question;


    if (status === "approved") {

        question =
            "Θέλεις να εγκρίνεις αυτό το αίτημα;";

    }
    else {

        question =
            "Θέλεις να απορρίψεις αυτό το αίτημα;";

    }


    if (!confirm(question)) {

        return;

    }


    fetch("update_aitima.php", {

        method: "POST",

        headers: {

            "Content-Type":
                "application/json"

        },

        body: JSON.stringify({

            id: id,

            status: status

        })

    })


    .then(function(response) {

        return response.json();

    })


    .then(function(data) {

        console.log("Απάντηση:", data);


        const message =
            document.getElementById("message");


        if (data.success) {

            message.textContent =
                data.message;

            message.style.color =
                "green";


            loadAitimata();

        }
        else {

            message.textContent =
                data.message;

            message.style.color =
                "red";

        }

    })


    .catch(function(error) {

        console.log("ERROR:", error);

        document.getElementById(
            "message"
        ).textContent =
            "Παρουσιάστηκε σφάλμα.";

    });

}


/* =========================
   PICKUP
   ========================= */

function pickupAitima(id, pickedUp) {

    let question;


    if (pickedUp) {

        question =
            "Επιβεβαιώνεις ότι η μερίδα παρελήφθη;";

    }
    else {

        question =
            "Επιβεβαιώνεις ότι ο χρήστης δεν παρέλαβε τη μερίδα; Θα αφαιρεθεί 1 πόντος.";

    }


    if (!confirm(question)) {

        return;

    }


    fetch("pickup_aitima.php", {

        method: "POST",

        headers: {

            "Content-Type":
                "application/json"

        },

        body: JSON.stringify({

            id: id,

            picked_up: pickedUp

        })

    })


    .then(function(response) {

        return response.json();

    })


    .then(function(data) {

        console.log("Απάντηση παραλαβής:", data);


        const message =
            document.getElementById("message");


        if (data.success) {

            message.textContent =
                data.message;

            message.style.color =
                "green";


            loadAitimata();

        }
        else {

            message.textContent =
                data.message;

            message.style.color =
                "red";

        }

    })


    .catch(function(error) {

        console.log("ERROR:", error);

        document.getElementById(
            "message"
        ).textContent =
            "Παρουσιάστηκε σφάλμα.";

    });

}