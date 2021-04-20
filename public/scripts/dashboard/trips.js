/* ==========================================================================
   Global variables
   ========================================================================== */
let page = 1; // the current page
let perPage = 20; // limit of items per page
let maxPage = 2; // max available pages for pagination. Will instantly be updated to get the correct value from the DB.
let pastTrips = false; // The flag that decides between fetching past trips or upcomming trips
const deleteTripButton = document.querySelector("button#delete-trip");
/*  ==========================================================================
    Initialize
    ========================================================================== */
document.addEventListener("DOMContentLoaded", init);

function init() {
  /**
   * Check if url parameters exist and update page & perPage value if true
   * tos check if key exists in object, use
   *    "key" in obj // -> returns true if key exists
   */
  urlParam = getURLParam();
  page = "page" in urlParam ? urlParam["page"] : page;
  perPage = "limit" in urlParam ? urlParam["limit"] : perPage;
  pastTrips = "past-trips" in urlParam ? urlParam["past-trips"] : pastTrips;
  pastTrips = pastTrips == "true"; // convert string "true" into boolean TRUE

  //do stuff after page has loaded
  fetchTrips();
  selectTripsButtons();
  shiftTripsPastUpcoming(pastTrips);
}
/*  ==========================================================================
    Functions 
    ========================================================================== */

/**
 * "Manage" buttons onclick handler.
 * Open the mdoal window when a Manage button is clicked
 * and fetch the requested trip's data by id
 */
let tripsManageButtons;

function selectTripsButtons() {
  tripsManageButtons = document.querySelectorAll(
    "#dashboard-trips .manage-button"
  );
  tripsManageButtons.forEach(btn => {
    btn.addEventListener("click", openTripsModalBox);
  });
}

function openTripsModalBox(evt) {
  const tripId = evt.target.dataset.trip_id;
  fetchSingleTrip(tripId);
  tripsModalBox.classList.add("active");
}

/**
 * Fetch a single trip data
 *
 * @param {int} tripId The id of the single trip to fetch and display in the modal
 */
function fetchSingleTrip(tripId) {
  let formData = new FormData();
  formData.append("id", tripId);

  axios
    .post("../api/get-single-trip.php", formData)
    .then(function(response) {
      if (response.data.statusCode == 200) {
        updateModalTrip(response.data);
      } else {
        // handle error
      }
    })
    .catch(function(error) {});
}

/**
 * Update the modal window with the new requested trip
 *
 * @param {json} data Object or array containing data of a single trip
 */
function updateModalTrip(data) {
  let trip = data.data.trip;
  console.log(trip);

  if (trip.customer_thoughts) {
    tripsModalBox.querySelector(
      ".client-details .trip-customer-thoughts"
    ).style.display = "block";
    tripsModalBox.querySelector(
      ".client-details .trip-customer-thoughts .item-value"
    ).textContent = trip.customer_thoughts;
  } else {
    tripsModalBox.querySelector(
      ".client-details .trip-customer-thoughts"
    ).style.display = "none";
  }

  tripsModalBox.querySelector(
    ".trip-details .trip-modal-start .item-value"
  ).textContent = trip.start_time;
  tripsModalBox.querySelector(
    ".trip-details .trip-modal-end .item-value"
  ).textContent = trip.end_time;
  tripsModalBox.querySelector(
    ".trip-details .trip-modal-date .item-value"
  ).textContent = trip.date;
  tripsModalBox.querySelector(
    ".trip-details .trip-modal-pickup .item-value"
  ).textContent = trip.pickup_loc_name;
  tripsModalBox.querySelector(
    ".trip-details .trip-modal-dropoff .item-value"
  ).textContent = trip.dropoff_loc_name;
  tripsModalBox.querySelector(
    ".client-details .trip-modal-name .item-value"
  ).textContent = trip.first_name + " " + trip.last_name;
  tripsModalBox.querySelector(
    ".client-details .trip-modal-email .item-value"
  ).textContent = trip.email;
  tripsModalBox.querySelector(
    ".client-details .trip-modal-phone .item-value"
  ).textContent = trip.phone;

  tripsModalBox.querySelector(
    ".client-details .trip-modal-paid .item-value"
  ).textContent = trip.paid ? "Yes" : "No";
  deleteTripButton.dataset.id = trip.trip_id;

  //Update captain selector and add the trip id to the selector
  tripsModalBox.querySelector(
    ".trip-details .trip-modal-captain #captain-select"
  ).dataset.tripid = trip.trip_id;
  tripsModalBox.querySelector(
    ".trip-details .trip-modal-captain #captain-select"
  ).value = trip.captain_id;
}

/**
 * Close Trips Modal Box on click
 */
const tripsModalBox = document.querySelector("#manage-trip-modal");

tripsModalBox
  .querySelector(".close-button")
  .addEventListener("click", closeTripsModalBox);

tripsModalBox
  .querySelector(".shadow-background")
  .addEventListener("click", closeTripsModalBox);

function closeTripsModalBox() {
  tripsModalBox.classList.remove("active");
}

/**
 * Fetch Trips pagination
 *
 * @param {integer} pageArg Optional number of the page, starts at page 0, default 0
 * @param {integer} perPageArg Optional number of items per page, defualt 30
 */

function fetchTrips(pageArg, perPageArg, pastTripsArg) {
  page = pageArg && pageArg != 0 ? pageArg : page;

  perPage = perPageArg ? perPageArg : perPage;
  pastTrips = typeof pastTripsArg !== "undefined" ? pastTripsArg : pastTrips; // Since the pastTrips value can be FALSE, we have to check on undefined, else it will break

  console.log("This is what i send: ", page, perPage, pastTrips, pageArg);

  let formData = new FormData();
  formData.append("page", Number(page));
  formData.append("perPage", Number(perPage));
  formData.append("pastTrips", pastTrips);

  axios
    .post("../api/get-trips.php", formData)
    .then(function(response) {
      console.log(response.data);
      if (response.data.statusCode == 200) {
        console.log(response.data.data.trips);
        cleanTripsTable(); //First clean the table
        paginationHandler(response.data); //handle pagination setup based on current page and max pages
        displayTrips(response.data); // display the trips
      } else {
        // handle error
      }
    })
    .catch(function(error) {});
}

/**
 *
 * Display the fetched trips using a <template>
 *
 * @param {json} data Object or array containing the data
 */
function displayTrips(data) {
  // console.log(data);
  //Then display the new data
  const template = document.querySelector("#trips-template").content;
  const container = document.querySelector("#trips-table");
  let trips = data.data.trips;

  // No trips yet, display message
  if (trips.length < 1) {
    container.innerHTML += "<p class='no-trips'>There are not trips yet </p>";
    return true;
  }

  //Found trips, display them
  trips.forEach(elem => {
    let clone = template.cloneNode(true);
    clone.firstElementChild.id = "trip-" + elem.trip_id;
    clone.querySelector(".trip-start-time").textContent = elem.start_time;
    clone.querySelector(".trip-date").textContent = elem.date;
    clone.querySelector(".trip-pickup").textContent = elem.pickup_loc_name;
    clone.querySelector(".trip-name").textContent =
      elem.first_name + " " + elem.last_name;
    clone.querySelector(".trip-paid").textContent = elem.paid ? "Yes" : "No";
    clone.querySelector(".trip-captain").textContent = elem.captain_name;
    clone.querySelector(".manage-button").dataset.trip_id = elem.trip_id;
    container.appendChild(clone);
  });
  selectTripsButtons();
  // Hide/display prev/next button
  paginationPrevNext();
}

/**
 * Remove all trips in the table and add the table headers
 */
function cleanTripsTable() {
  // Remove all trips. This is a fast method to do so: https://stackoverflow.com/a/3955238
  const container = document.querySelector("#trips-table");
  while (container.firstChild) {
    container.removeChild(container.firstChild);
  }

  // Add table headers
  container.innerHTML =
    "<tr><th>Time</th><th>Date</th><th>Pickup Loc.</th><th>Name</th><th>Paid</th><th>Assigned captain</th><th> </th></tr>";
}

/**
 * Handle pagination
 *
 * @param {json} data Object or Array containing the data. Used to display the current page out of XX pages
 */
let pagination = document.querySelector("#pagination");

function paginationHandler(data) {
  //Get the returned pagination values
  page = data.data.page;
  maxPage = data.data.out_of_pages;
  if (page > maxPage && maxPage != 0) {
    page = maxPage;
    fetchTrips();
  }

  // Update pagination text
  pagination.querySelector(".pagination-status").textContent =
    page + "/" + maxPage;

  updateUrlParam();
}

/**
 * Update the URL parameters using the global values
 */
function updateUrlParam() {
  // Update url parameter
  window.history.replaceState(
    null,
    null,
    `?page=${page}&limit=${perPage}&past-trips=${pastTrips}`
  ); // -> set url param
}

/**
 * Pagination next button handler
 */
let paginationNextButton = document.querySelector("#pagination .next");
paginationNextButton.addEventListener("click", paginationNext);

function paginationNext(e) {
  e.preventDefault(); //prevent hyperlink click
  if (page < maxPage) {
    ++page;
  } else {
    page = maxPage;
  }
  fetchTrips();
  simpleBarScrollBackToTop(dashboardListSimpleBar); // scroll back to top
}

/**
 * Pagination next previous handler
 */
let paginationPreviousButton = document.querySelector("#pagination .previous");
paginationPreviousButton.addEventListener("click", paginationPrevious);

function paginationPrevious(e) {
  e.preventDefault(); //prevent hyperlink click
  if (page > 1 || maxPage > page) {
    --page;
  } else {
    page = 1;
  }
  fetchTrips();
  simpleBarScrollBackToTop(dashboardListSimpleBar); // scroll back to top
}

/**
 * Hide or display prev / next button based on the current page
 */
function paginationPrevNext() {
  paginationPreviousButton.style.opacity = "1";
  paginationPreviousButton.style.pointerEvents = "auto";
  paginationNextButton.style.opacity = "1";
  paginationNextButton.style.pointerEvents = "auto";
  if (page == 1) {
    paginationPreviousButton.style.opacity = "0";
    paginationPreviousButton.style.pointerEvents = "none";
  }
  if (page == maxPage) {
    paginationNextButton.style.opacity = "0";
    paginationNextButton.style.pointerEvents = "none";
  }
}

// Register the simplebar div (optional, but needed if we want to scroll back to top)
const dashboardListSimpleBar = new SimpleBar(
  document.querySelector(".dashboard-list .list"),
  {
    autoHide: false
  }
);

/**
 * Tell simplebar to scroll the elem back to top
 *
 * @param {DOMElement} elem Element of the simplebar div
 * */
function simpleBarScrollBackToTop(elem) {
  dashboardListSimpleBar.getScrollElement().scrollTop = 0;
}

/**
 * Switch between displaying upcoming trips and past trips
 */
let shiftTripsButtons = document.querySelectorAll(
  "#dashboard-trips .left .shift-trips"
);
//add click event on the Upcoming and Past button
shiftTripsButtons.forEach(btn => {
  btn.addEventListener("click", evt => {
    let decision = evt.target.dataset.past == "true";
    shiftTripsPastUpcoming(decision);
    fetchTrips(page, perPage, decision);
  });
});

function shiftTripsPastUpcoming(pastTripsArg) {
  // Switch active class between the two buttons
  shiftTripsButtons.forEach(btn => {
    btn.classList.remove("active");
  });

  // Add active class to active button
  if (pastTripsArg) {
    // Past trips selected
    shiftTripsButtons[1].classList.add("active");
  } else {
    //Upcoming trips selected
    shiftTripsButtons[0].classList.add("active");
  }
}

/**
 * Make modal box scrollbar always visible
 */
const modalBoxSimpleBar = new SimpleBar(
  document.querySelector(".modal-box-sub-wrapper"),
  {
    autoHide: false
  }
);

/**
 * Delete the trip
 */

deleteTripButton.addEventListener("click", deleteTripById);

function deleteTripById(e) {
  let id = e.target.dataset.id;
  let formData = new FormData();
  formData.append("id", id);

  //Optimistic deletion front-end
  document.querySelector("#trip-" + id).remove();
  closeTripsModalBox();
  //Delete from database
  axios
    .post("../api/delete-trip-by-id.php", formData)
    .then(function(response) {
      if (response.data.statusCode == 200) {
        // success
      } else {
        // handle error
      }
    })
    .catch(function(error) {});
}

/**
 * Update captain on dropdown selection change
 */
const captainSelector = document.querySelector(
  ".trip-details .trip-modal-captain #captain-select"
);
const captainSelectorStatus = document.querySelector(
  ".trip-details .trip-modal-captain p .status"
);
captainSelector.addEventListener("change", updateCaptain);

function updateCaptain() {
  let captainId = captainSelector.options[captainSelector.selectedIndex].value;
  let tripId = captainSelector.dataset.tripid;

  let formData = new FormData();
  formData.append("trip_id", Number(tripId));
  formData.append("capt_id", Number(captainId));

  axios
    .post("../api/update-trip-captain.php", formData)
    .then(function(response) {
      if (response.data.statusCode == 200) {
        // success
        // Show success indication beside title
        captainSelectorStatus.textContent = "Success!";
        captainSelectorStatus.style.opacity = 1;
        captainSelectorStatus.style.color = "green";
        updateCaptainInListElem();
        setTimeout(() => {
          captainSelectorStatus.style.opacity = 0;
        }, 2000);
      } else {
        // handle error
        // Show failed indication beside title
        captainSelectorStatus.textContent = "Failed!";
        captainSelectorStatus.style.opacity = 1;
        captainSelectorStatus.style.color = "red";
        setTimeout(() => {
          captainSelectorStatus.style.opacity = 0;
        }, 2000);
      }
    })
    .catch(function(error) {});
}

function updateCaptainInListElem() {
  let captainName =
    captainSelector.options[captainSelector.selectedIndex].textContent;
  let tripId = captainSelector.dataset.tripid;

  let captainElem = document.querySelector(
    "#trip-" + tripId + " .trip-captain"
  );
  captainElem.textContent = captainName;
}
/**
 *
 *
 * FYI
 * URL params inside JavaScript
 *
 * paginationwindow.history.replaceState(null, null, "?page=1&limit=20"); // -> set url param
 * getURLParam() //-> get url param as an object
 */
