console.log("user_grade.js φορτώθηκε");


document.addEventListener("DOMContentLoaded", function() {

    loadRatings();

});


/* =========================
   LOAD RATINGS
   ========================= */

function loadRatings() {

    fetch("get_ratings.php")

        .then(function(response) {

            console.log(
                "HTTP Status:",
                response.status
            );

            return response.json();

        })

        .then(function(data) {

            console.log(
                "Ratings:",
                data
            );


            if (data.success) {

                displayRatings(data.ratings);

            }
            else {

                document.getElementById(
                    "ratings-container"
                ).innerHTML = `
                    <p>${data.message}</p>
                `;

            }

        })

        .catch(function(error) {

            console.log(
                "ERROR:",
                error
            );


            document.getElementById(
                "ratings-container"
            ).innerHTML = `
                <p>
                    Παρουσιάστηκε σφάλμα.
                </p>
            `;

        });

}


/* =========================
   DISPLAY RATINGS
   ========================= */

function displayRatings(ratings) {

    const container =
        document.getElementById(
            "ratings-container"
        );


    container.innerHTML = "";


    if (ratings.length === 0) {

        container.innerHTML = `
            <div class="aggelia-card">

                <h3>
                    Δεν υπάρχουν αγγελίες για αξιολόγηση.
                </h3>

                <p>
                    Οι αγγελίες που έχεις παραλάβει
                    θα εμφανιστούν εδώ.
                </p>

            </div>
        `;

        return;

    }


    ratings.forEach(function(rating) {

        addRatingCard(rating);

    });

}


/* =========================
   CREATE RATING CARD
   ========================= */

function addRatingCard(rating) {

    const container =
        document.getElementById(
            "ratings-container"
        );


    const card =
        document.createElement("div");


    card.classList.add("aggelia-card");


    card.innerHTML = `

        <h3>
            ${rating.title}
        </h3>


        <p>

            <strong>Μάγειρας:</strong>
            ${rating.chef_username}

        </p>


        <p>

            <strong>Ημερομηνία παραλαβής:</strong>
            ${rating.pickup_date}

        </p>


        <p>

            <strong>Αξιολόγηση:</strong>

        </p>


        <select
            id="rating-${rating.aitima_id}"
            class="rating-select">

            <option value="">
                -- Επιλέξτε βαθμολογία --
            </option>

            <option value="1">
                ★ 1/5
            </option>

            <option value="2">
                ★★ 2/5
            </option>

            <option value="3">
                ★★★ 3/5
            </option>

            <option value="4">
                ★★★★ 4/5
            </option>

            <option value="5">
                ★★★★★ 5/5
            </option>

        </select>


        <br><br>


        <label>

            Σχόλιο:

        </label>


        <textarea
            id="comment-${rating.aitima_id}"
            rows="4"
            style="width:100%; margin-top:8px;"
            placeholder="Προαιρετικό σχόλιο..."
        ></textarea>


        <br><br>


        <button
            onclick="submitRating(${rating.aitima_id})">

            Υποβολή Αξιολόγησης

        </button>

    `;


    container.appendChild(card);

}


/* =========================
   SUBMIT RATING
   ========================= */

function submitRating(aitimaId) {

    const ratingSelect =
        document.getElementById(
            "rating-" + aitimaId
        );


    const comment =
        document.getElementById(
            "comment-" + aitimaId
        ).value;


    const rating =
        ratingSelect.value;


    if (rating === "") {

        alert(
            "Παρακαλώ επιλέξτε βαθμολογία."
        );

        return;

    }


    fetch("submit_rating.php", {

        method: "POST",

        headers: {

            "Content-Type":
                "application/json"

        },

        body: JSON.stringify({

            aitima_id: aitimaId,

            rating: rating,

            comment: comment

        })

    })


    .then(function(response) {

        return response.json();

    })


    .then(function(data) {

        console.log(
            "Submit rating:",
            data
        );


        const message =
            document.getElementById(
                "message"
            );


        if (data.success) {

            message.textContent =
                data.message;

            message.style.color =
                "green";


            loadRatings();

        }
        else {

            message.textContent =
                data.message;

            message.style.color =
                "red";

        }

    })


    .catch(function(error) {

        console.log(
            "ERROR:",
            error
        );


        document.getElementById(
            "message"
        ).textContent =
            "Παρουσιάστηκε σφάλμα.";

    });

}