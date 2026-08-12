console.log("user_aggelies.js φορτώθηκε");


document.addEventListener(
    "DOMContentLoaded",
    function() {

        loadAggelies();

    }
);


/*
    =========================
    LOAD AGGELIES
    =========================
*/

function loadAggelies() {

    fetch("get_active_aggelies.php")

        .then(function(response) {

            return response.json();

        })

        .then(function(data) {

            console.log("Αγγελίες:", data);


            if (data.success) {

                displayAggelies(data.aggelies);

            }
            else {

                document.getElementById(
                    "feed-message"
                ).textContent = data.message;

            }

        })

        .catch(function(error) {

            console.log("ERROR:", error);

            document.getElementById(
                "feed-message"
            ).textContent =
                "Παρουσιάστηκε σφάλμα κατά τη φόρτωση των αγγελιών.";

        });

}


/*
    =========================
    DISPLAY AGGELIES
    =========================
*/

function displayAggelies(aggelies) {

    const container =
        document.getElementById(
            "user-aggelies-container"
        );


    const message =
        document.getElementById(
            "feed-message"
        );


    container.innerHTML = "";


    if (aggelies.length === 0) {

        message.textContent =
            "Δεν υπάρχουν διαθέσιμες αγγελίες.";

        return;

    }


    message.textContent = "";


    aggelies.forEach(function(aggelia) {

        addAggeliaCard(
            aggelia
        );

    });

}


/*
    =========================
    CREATE CARD
    =========================
*/

function addAggeliaCard(aggelia) {

    const container =
        document.getElementById(
            "user-aggelies-container"
        );


    const col =
        document.createElement("div");


    col.className =
        "col-12 col-md-6 col-lg-4";


    const card =
        document.createElement("div");


    card.classList.add(
        "card",
        "h-100",
        "shadow-sm",
        "user-aggelia-card"
    );


    /*
        Έλεγχος αν
        έχουν τελειώσει
        οι μερίδες
    */

    const soldOut =
        parseInt(aggelia.merides_left) === 0;


    if (soldOut) {

        card.classList.add(
            "sold-out"
        );

    }


    /*
        Φωτογραφία
    */

    let photoHTML = "";


    if (aggelia.photo) {

        photoHTML = `

            <img
                src="${aggelia.photo}"
                class="card-img-top aggelia-photo"
                alt="${aggelia.title}"
            >

        `;

    }
    else {

        photoHTML = `

            <div class="no-photo">

                🍽️

            </div>

        `;

    }


    /*
        Ένδειξη εξάντλησης
    */

    let soldOutHTML = "";


    if (soldOut) {

        soldOutHTML = `

            <div class="sold-out-badge">

                Εξαντλήθηκε

            </div>

        `;

    }


    /*
        Δημιουργία HTML
    */

    card.innerHTML = `

        ${photoHTML}

        <div class="card-body">

            ${soldOutHTML}


            <h5 class="card-title">

                ${aggelia.title}

            </h5>


            <p class="card-text">

                ${aggelia.description || ""}

            </p>


            <p class="card-text">

                <strong>Μερίδες:</strong>

                ${aggelia.merides_left}
                /
                ${aggelia.merides_total}

            </p>


            <p class="card-text">

                <strong>📍 Τοποθεσία:</strong>

                ${aggelia.location}

            </p>


            <p class="card-text">

                <strong>🕐 Παραλαβή:</strong>

                ${aggelia.pickup_time}

            </p>


            <p class="card-text">

                <strong>⚠️ Αλλεργιογόνα:</strong>

                ${aggelia.allergens || "Κανένα"}

            </p>


            ${
                soldOut
                ?
                `
                    <button
                        class="btn btn-secondary w-100"
                        disabled>

                        Εξαντλήθηκε

                    </button>
                `
                :
                `
                    <button
                        class="btn btn-success w-100"
                        onclick="sendAitima(${aggelia.id}, this)">

                        Αίτημα Μερίδας

                    </button>
                `
            }

        </div>

    `;


    col.appendChild(card);

    container.appendChild(col);

}

/*
    =========================
    SEND AITIMA
    =========================
*/

function sendAitima(aggeliaId, button) {

    if (
        !confirm(
            "Θέλεις να δεσμεύσεις μία μερίδα;"
        )
    ) {

        return;

    }


    /*
        Απενεργοποιούμε προσωρινά
        το κουμπί ώστε να μην
        σταλούν δύο αιτήματα.
    */

    button.disabled = true;

    button.textContent =
        "Αποστολή...";


    fetch(
        "send_aitima.php",
        {

            method: "POST",

            headers: {

                "Content-Type":
                    "application/json"

            },

            body: JSON.stringify({

                aggelia_id: aggeliaId

            })

        }
    )


    .then(function(response) {

        return response.json();

    })


    .then(function(data) {

        console.log(
            "Απάντηση αιτήματος:",
            data
        );


        if (data.success) {

            button.textContent =
                "Αίτημα στάλθηκε";

            button.classList.remove(
                "btn-success"
            );

            button.classList.add(
                "btn-secondary"
            );


            /*
                Ξαναφορτώνουμε το feed
                ώστε να ενημερωθούν
                οι διαθέσιμες μερίδες.
            */

            loadAggelies();

        }

        else {

            alert(data.message);

            button.disabled = false;

            button.textContent =
                "Αίτημα Μερίδας";

        }

    })


    .catch(function(error) {

        console.log(
            "ERROR:",
            error
        );

        alert(
            "Παρουσιάστηκε σφάλμα."
        );

        button.disabled = false;

        button.textContent =
            "Αίτημα Μερίδας";

    });

}