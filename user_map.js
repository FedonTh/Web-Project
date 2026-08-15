console.log("user_map.js φορτώθηκε");


/*
    =========================
    ΔΗΜΙΟΥΡΓΙΑ ΧΑΡΤΗ
    =========================
*/

const map = L.map("map");


/*
    OpenStreetMap
*/

L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {

        maxZoom: 19,

        attribution:
            '&copy; OpenStreetMap contributors'

    }
).addTo(map);


/*
    Αρχική θέση χάρτη
    Ελλάδα
*/

map.setView(
    [38.621, 21.407],
    13
);


/*
    Σημείο που επιλέγει
    ο χρήστης.
*/

let selectedLocation = null;


/*
    Marker του σημείου
    αναζήτησης.
*/

let selectedMarker = null;


/*
    Όλοι οι markers
    των αγγελιών.
*/

let currentMarkers = [];


/*
    Όλες οι αγγελίες
    από τη βάση.
*/

let allAggelies = [];


/*
    =========================
    ΦΟΡΤΩΣΗ ΑΓΓΕΛΙΩΝ
    =========================
*/

fetch("get_map_aggelies.php")

    .then(function(response) {

        return response.json();

    })

    .then(function(data) {

        console.log(
            "Αγγελίες:",
            data
        );


        if (data.success) {

            allAggelies =
                data.aggelies;


            /*
                Αρχικά δείχνουμε
                όλες τις αγγελίες.
            */

            displayAggelies(
                allAggelies
            );

        }

        else {

            console.log(
                data.message
            );

        }

    })

    .catch(function(error) {

        console.log(
            "ERROR:",
            error
        );

    });



/*
    =========================
    ΚΛΙΚ ΣΤΟΝ ΧΑΡΤΗ
    =========================
*/

map.on(
    "click",
    function(event) {


        /*
            Αποθηκεύουμε
            latitude / longitude
        */

        selectedLocation = {

            latitude:
                event.latlng.lat,

            longitude:
                event.latlng.lng

        };


        /*
            Αν υπάρχει ήδη
            marker επιλογής,
            τον αφαιρούμε.
        */

        if (selectedMarker) {

            map.removeLayer(
                selectedMarker
            );

        }


        /*
            Δημιουργούμε νέο
            marker.
        */

        selectedMarker =
            L.marker([

                event.latlng.lat,

                event.latlng.lng

            ]).addTo(map);


        selectedMarker.bindPopup(
            "Σημείο αναζήτησης"
        ).openPopup();


        /*
            Ενημερώνουμε μήνυμα.
        */

        document.getElementById(
            "filter-message"
        ).textContent =

            "Επιλέχθηκε σημείο. " +
            "Πάτησε «Εφαρμογή Φίλτρων».";

    }
);



/*
    =========================
    ΕΦΑΡΜΟΓΗ ΦΙΛΤΡΩΝ
    =========================
*/

document.getElementById(
    "apply-filters"
).addEventListener(
    "click",
    function() {


        /*
            Πρέπει πρώτα
            να έχει επιλεγεί σημείο.
        */

        if (!selectedLocation) {

            document.getElementById(
                "filter-message"
            ).textContent =

                "Πρέπει πρώτα να επιλέξεις σημείο στον χάρτη.";

            return;

        }


        /*
            Παίρνουμε απόσταση
        */

        const maxDistance =
            parseFloat(
                document.getElementById(
                    "distance"
                ).value
            );


        /*
            Υπολογίζουμε
            αποστάσεις.
        */

        let filteredAggelies =
            [];


        allAggelies.forEach(
            function(aggelia) {


                /*
                    Ελέγχουμε
                    συντεταγμένες.
                */

                if (
                    aggelia.latitude === null ||
                    aggelia.longitude === null
                ) {

                    return;

                }


                const latitude =
                    parseFloat(
                        aggelia.latitude
                    );


                const longitude =
                    parseFloat(
                        aggelia.longitude
                    );


                /*
                    Υπολογισμός απόστασης.
                */

                const distance =
                    calculateDistance(
                        selectedLocation.latitude,
                        selectedLocation.longitude,
                        latitude,
                        longitude
                    );


                /*
                    Κρατάμε την
                    απόσταση μέσα
                    στο αντικείμενο.
                */

                aggelia.distance =
                    distance;


                /*
                    Ελέγχουμε το
                    όριο απόστασης.
                */

                if (
                    distance <= maxDistance
                ) {

                    filteredAggelies.push(
                        aggelia
                    );

                }

            }
        );


        /*
            Ταξινόμηση από
            κοντινότερη προς
            μακρύτερη.
        */

        filteredAggelies.sort(
            function(a, b) {

                return (
                    a.distance -
                    b.distance
                );

            }
        );


        /*
            Εμφάνιση όλων των
            αποτελεσμάτων που
            βρίσκονται μέσα
            στην απόσταση.
        */

        displayAggelies(
            filteredAggelies
        );


        /*
            Ενημέρωση μηνύματος.
        */

        document.getElementById(
            "filter-message"
        ).textContent =

            "Βρέθηκαν " +
            filteredAggelies.length +
            " αγγελίες.";

    }
);



/*
    =========================
    ΚΑΘΑΡΙΣΜΟΣ ΦΙΛΤΡΩΝ
    =========================
*/

document.getElementById(
    "clear-filters"
).addEventListener(
    "click",
    function() {


        /*
            Αφαιρούμε το
            σημείο αναζήτησης.
        */

        if (selectedMarker) {

            map.removeLayer(
                selectedMarker
            );

        }


        selectedMarker = null;

        selectedLocation = null;


        /*
            Ξαναδείχνουμε
            όλες τις αγγελίες.
        */

        displayAggelies(
            allAggelies
        );


        /*
            Μήνυμα.
        */

        document.getElementById(
            "filter-message"
        ).textContent =

            "Δεν έχει επιλεγεί σημείο.";

    }
);



/*
    =========================
    ΕΜΦΑΝΙΣΗ ΑΓΓΕΛΙΩΝ
    =========================
*/

function displayAggelies(
    aggelies
) {


    /*
        Αφαιρούμε τους
        προηγούμενους markers.
    */

    currentMarkers.forEach(
        function(marker) {

            map.removeLayer(
                marker
            );

        }
    );


    currentMarkers = [];


    /*
        Αν δεν υπάρχουν
        αγγελίες.
    */

    if (
        aggelies.length === 0
    ) {

        return;

    }


    /*
        Νέα markers.
    */

    aggelies.forEach(
        function(aggelia) {


            if (
                aggelia.latitude === null ||
                aggelia.longitude === null
            ) {

                return;

            }


            const latitude =
                parseFloat(
                    aggelia.latitude
                );


            const longitude =
                parseFloat(
                    aggelia.longitude
                );


            /*
                Marker.
            */

            const marker =
                L.marker([

                    latitude,

                    longitude

                ]).addTo(map);


            /*
                Απόσταση
                μόνο όταν έχει
                γίνει επιλογή σημείου.
            */

            let distanceHTML = "";


            if (
                aggelia.distance !== undefined
            ) {

                distanceHTML = `

                    <p>

                        <strong>
                            Απόσταση:
                        </strong>

                        ${aggelia.distance.toFixed(2)}
                        km

                    </p>

                `;

            }


            /*
                Διαθέσιμες μερίδες.
            */

            let availabilityText;


            if (
                parseInt(
                    aggelia.merides_left
                ) > 0
            ) {

                availabilityText = `

                    <strong>
                        Μερίδες:
                    </strong>

                    ${aggelia.merides_left}
                    /
                    ${aggelia.merides_total}

                `;

            }

            else {

                availabilityText = `

                    <strong>
                        Μερίδες:
                    </strong>

                    0 /
                    ${aggelia.merides_total}

                    <br>

                    <strong>
                        ⚠ Εξαντλήθηκε
                    </strong>

                `;

            }


            /*
                Popup.
            */

            const popupHTML = `

                <div style="min-width:220px;">

                    <h3>
                        ${aggelia.title}
                    </h3>


                    <p>

                        ${aggelia.description || ""}

                    </p>


                    <p>

                        ${availabilityText}

                    </p>


                    ${distanceHTML}


                    <p>

                        <strong>
                            Τοποθεσία:
                        </strong>

                        ${aggelia.location}

                    </p>


                    <p>

                        <strong>
                            Παραλαβή:
                        </strong>

                        ${aggelia.pickup_time}

                    </p>


                    <p>

                        <strong>
                            Αλλεργιογόνα:
                        </strong>

                        ${aggelia.allergens || "Κανένα"}

                    </p>

                </div>

            `;


            marker.bindPopup(
                popupHTML
            );


            currentMarkers.push(
                marker
            );

        }
    );


    /*
        Προσαρμόζουμε τον χάρτη
        στα markers.
    */

    if (
        currentMarkers.length > 0
    ) {

        const group =
            L.featureGroup(
                currentMarkers
            );


        map.fitBounds(
            group.getBounds(),
            {

                padding: [50, 50]

            }
        );

    }

}



/*
    =========================
    ΥΠΟΛΟΓΙΣΜΟΣ ΑΠΟΣΤΑΣΗΣ
    HAVERSINE
    =========================
*/

function calculateDistance(
    lat1,
    lon1,
    lat2,
    lon2
) {


    /*
        Ακτίνα Γης σε km
    */

    const earthRadius =
        6371;


    /*
        Μετατροπή μοιρών
        σε radians.
    */

    const lat1Rad =
        lat1 * Math.PI / 180;


    const lat2Rad =
        lat2 * Math.PI / 180;


    const deltaLat =
        (lat2 - lat1) *
        Math.PI / 180;


    const deltaLon =
        (lon2 - lon1) *
        Math.PI / 180;


    /*
        Haversine formula
    */

    const a =

        Math.sin(
            deltaLat / 2
        ) *
        Math.sin(
            deltaLat / 2
        )

        +

        Math.cos(lat1Rad) *
        Math.cos(lat2Rad) *

        Math.sin(
            deltaLon / 2
        ) *
        Math.sin(
            deltaLon / 2
        );


    const c =

        2 *
        Math.atan2(
            Math.sqrt(a),
            Math.sqrt(1 - a)
        );


    return earthRadius * c;

}