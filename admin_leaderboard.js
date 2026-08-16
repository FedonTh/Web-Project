console.log("admin_leaderboard.js φορτώθηκε");


document.addEventListener(
    "DOMContentLoaded",
    function() {

        loadLeaderboard();

    }
);


/*
    =========================
    ΦΟΡΤΩΣΗ LEADERBOARD
    =========================
*/

function loadLeaderboard() {

    fetch("get_leaderboard.php")

        .then(function(response) {

            return response.json();

        })

        .then(function(data) {

            console.log("Leaderboard:", data);


            if (!data.success) {

                document.getElementById(
                    "top-donor"
                ).textContent = data.message;

                document.getElementById(
                    "top-rated"
                ).textContent = data.message;

                return;

            }


            displayTopDonor(
                data.top_donor
            );


            displayTopRated(
                data.top_rated
            );

        })

        .catch(function(error) {

            console.log("ERROR:", error);


            document.getElementById(
                "top-donor"
            ).textContent =
                "Παρουσιάστηκε σφάλμα.";


            document.getElementById(
                "top-rated"
            ).textContent =
                "Παρουσιάστηκε σφάλμα.";

        });

}


/*
    =========================
    TOP DONOR
    =========================
*/

function displayTopDonor(donor) {

    const container =
        document.getElementById(
            "top-donor"
        );


    if (!donor) {

        container.innerHTML = `

            <p>
                Δεν υπάρχουν δεδομένα.
            </p>

        `;

        return;

    }


    container.innerHTML = `

        <p>

            <strong>
                Χρήστης:
            </strong>

            ${donor.username}

        </p>


        <p>

            <strong>
                Μερίδες που προσφέρθηκαν:
            </strong>

            ${donor.meals}

        </p>

    `;

}


/*
    =========================
    TOP RATED
    =========================
*/

function displayTopRated(meals) {

    const container =
        document.getElementById(
            "top-rated"
        );


    if (!meals || meals.length === 0) {

        container.innerHTML = `

            <p>
                Δεν υπάρχουν αξιολογημένα γεύματα.
            </p>

        `;

        return;

    }


    container.innerHTML = "";


    meals.forEach(function(meal) {

        const div =
            document.createElement("div");


        div.classList.add(
            "aggelia-card"
        );


        div.innerHTML = `

            <h3>

                ${meal.title}

            </h3>


            <p>

                <strong>
                    Μάγειρας:
                </strong>

                ${meal.username}

            </p>


            <p>

                <strong>
                    Μέση βαθμολογία:
                </strong>

                ⭐ ${parseFloat(
                    meal.average_rating
                ).toFixed(2)} / 5

            </p>


            <p>

                <strong>
                    Αριθμός αξιολογήσεων:
                </strong>

                ${meal.rating_count}

            </p>

        `;


        container.appendChild(div);

    });

}