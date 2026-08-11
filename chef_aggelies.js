document.addEventListener("DOMContentLoaded", function() {

    loadAggelies();

});


/* =========================
   LOAD AGGELIES
   ========================= */

function loadAggelies() {

    fetch("get_aggelies.php")

        .then(response => response.json())

        .then(data => {

            if (data.success) {

                displayAggelies(data.aggelies);

            }

        })

        .catch(error => {

            console.log(error);

        });

}


/* =========================
   DISPLAY CARDS
   ========================= */

function displayAggelies(aggelies) {

    const container =
        document.getElementById("aggelies-container");

    container.innerHTML = "";


    aggelies.forEach(function(aggelia) {

        addAggeliaCard(aggelia);

    });

}


/* =========================
   CREATE CARD
   ========================= */

function addAggeliaCard(aggelia) {

    const container =
        document.getElementById("aggelies-container");


    const card =
        document.createElement("div");

    card.classList.add("aggelia-card");

    card.id =
        "aggelia-" + aggelia.id;


    /*
        Φωτογραφία

        Αν υπάρχει φωτογραφία,
        την εμφανίζουμε.

        Αν δεν υπάρχει,
        εμφανίζουμε ένα απλό placeholder.
    */

    let photoHTML = "";

    if (aggelia.photo) {

        photoHTML = `
            <img
                src="${aggelia.photo}"
                alt="${aggelia.title}"
                class="aggelia-photo"
            >
        `;

    }
    else {

        photoHTML = `
            <div class="no-photo">
                Δεν υπάρχει φωτογραφία
            </div>
        `;

    }


    card.innerHTML = `

        ${photoHTML}


        <div class="aggelia-card-content">

            <h3>
                ${aggelia.title}
            </h3>


            <p>
                ${aggelia.description || ""}
            </p>


            <p>
                <strong>Μερίδες:</strong>
                ${aggelia.merides_left}/${aggelia.merides_total}
            </p>


            <p>
                <strong>Τοποθεσία:</strong>
                ${aggelia.location}
            </p>


            <p>
                <strong>Παραλαβή:</strong>
                ${aggelia.pickup_time}
            </p>


            <p>
                <strong>Αλλεργιογόνα:</strong>
                ${aggelia.allergens || "Κανένα"}
            </p>


            <div class="aggelia-buttons">

                <button
                    onclick="editAggelia(${aggelia.id})">
                    Επεξεργασία
                </button>


                <button
                    class="delete-button"
                    onclick="deleteAggelia(${aggelia.id})">
                    Διαγραφή
                </button>

            </div>

        </div>

    `;


    container.appendChild(card);

}


function deleteAggelia(id) {

    if (!confirm("Θέλεις σίγουρα να διαγράψεις την αγγελία;")) {
        return;
    }


    fetch("delete_aggelia.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            id: id
        })

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            /*
                Βρίσκουμε την card
                και την αφαιρούμε από το DOM.
            */

            const card =
                document.getElementById("aggelia-" + id);

            card.remove();

        } else {

            alert(data.message);

        }

    })

    .catch(error => {

        console.log(error);

    });

}

function editAggelia(id) {

    window.location.href = "edit_aggelia.php?id=" + id;

}