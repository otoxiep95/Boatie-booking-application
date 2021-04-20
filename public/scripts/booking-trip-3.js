/* ==========================================================================
   Global variables
   ========================================================================== */
const progressStepsTopCircles = document.querySelectorAll(".step");
const firstPageNextButton = document.querySelector("#first-next-step-button");
const secondPageNextButton = document.querySelector("#second-next-step-button");
const thirdPageNextButton = document.querySelector("#third-next-step-button");
const confirmationPage = document.querySelector("#booking-confirmation");
const pricePerHour = 1200;
/*  ==========================================================================
    Initialize
    ========================================================================== */
document.addEventListener("DOMContentLoaded", init);
function init() {
  //do stuff after page has loaded
}

/*  ==========================================================================
    Functions
    ========================================================================== */

progressStepsTopCircles.forEach(stp => {
  stp.addEventListener("click", e => {
    if (!this.classList.contains("step-3")) {
      let activeSteps = document.querySelectorAll(".active");
      activeSteps[0].className = activeSteps[0].className.replace(
        " active",
        ""
      );
      this.className += " active";
    }
  });
});

/**
 * On next button click, only POST data if all data has been filled out
 */
thirdPageNextButton.addEventListener("click", handleThirdNextButton);
const firstName = document.querySelector(
  ".booking-content-left input[name='first-name']"
);
const lastName = document.querySelector(
  ".booking-content-left input[name='last-name']"
);
const email = document.querySelector(
  ".booking-content-left input[name='email']"
);
const phone = document.querySelector(
  ".booking-content-left input[name='phone']"
);
const thoughts = document.querySelector(".booking-content-left textarea");
const thirdPageInputFields = document.querySelectorAll(
  ".booking-3 .group input"
);
const errorMessageElem = document.querySelector(
  ".booking-button .error-messages p"
);

//Update the booking card with the trip details
function updateBookingCard() {
  const cDuration = document.querySelector(".booking-card .c-duration");
  const cLocation = document.querySelector(".booking-card .c-location");
  const cDate = document.querySelector(".booking-card .c-date");
  const cTime = document.querySelector(".booking-card .c-time");
  const cPrice = document.querySelector(".booking-card .c-price");
  let secondPage = JSON.parse(sessionStorage.getItem("secondpage"));

  let hours = Math.floor(secondPage.duration / 60);
  let minutes = secondPage.duration % 60;
  durationText = minutes ? `${hours}hr ${minutes}min trip` : `${hours}hr trip`;
  cDuration.textContent = durationText;

  cLocation.innerHTML = `${secondPage.pickupName} &rarr; ${secondPage.dropoffName}`;

  let dateArr = secondPage.date.split("-");
  cDate.textContent = dateArr[2] + "-" + dateArr[1] + "-" + dateArr[0];

  let endTime = addTimes(secondPage.start_time, `${hours}:${minutes}`);
  cTime.textContent = `${secondPage.start_time} - ${endTime}`;

  let price = (pricePerHour / 60) * secondPage.duration;
  cPrice.textContent = `DKK ${price}`;
}
updateBookingCard();

// Check if fields have input values on input change
thirdPageInputFields.forEach(field => {
  field.addEventListener("input", checkFields);
});

function checkFields() {
  if (
    firstName.value.length > 1 &&
    lastName.value.length > 1 &&
    email.value.length > 5 &&
    phone.value.length > 1
  ) {
    thirdPageNextButton.classList.remove("inactive");
    errorMessageElem.textContent = "";
    return true;
  } else {
    errorMessageElem.textContent = "Some input fields are not complete.";
    thirdPageNextButton.classList.add("inactive");
    return false;
  }
}

function handleThirdNextButton(e) {
  e.preventDefault();
  if (checkFields()) {
    // Allow POST request
    postNewTrip();
    errorMessageElem.textContent = "";
  } else {
    // Some fields are not complete
  }
}

/**
 * Try to POST the data to the API and if failed, display error message to the frontend user
 */
function postNewTrip() {
  let secondPage = JSON.parse(sessionStorage.getItem("secondpage"));
  secondPage.name = firstName.value;
  secondPage.email = email.value;

  //Store data to display on final page
  //Save them inside a session
  sessionStorage.setItem("confirmation", JSON.stringify(secondPage));
  // send data to API

  let formData = new FormData();
  formData.append("date", secondPage.date);
  formData.append("start_time", secondPage.start_time);
  formData.append("duration", secondPage.duration);
  formData.append("pickup_loc_id", secondPage.pickupId);
  formData.append("dropoff_loc_id", secondPage.dropoffId);
  formData.append("pickup_name", secondPage.pickupName);
  formData.append("dropoff_name", secondPage.dropoffName);
  formData.append("first_name", firstName.value);
  formData.append("last_name", lastName.value);
  formData.append("email", email.value);
  formData.append("phone", phone.value);
  formData.append("thoughts", thoughts.value);

  axios
    .post("api/create-trip.php", formData)
    .then(function(response) {
      if (response.data.statusCode == 200) {
        //manual redirect
        window.location.href = "booking-confirmation.php";
      } else {
        // handle error
        errorMessageElem.textContent = response.data.message;
      }
    })
    .catch(function(error) {});
}
/**
 * Helper function to addition two time together HH:MM + HH:MM = HH:MM
 */
// Convert a time in hh:mm format to minutes
function timeToMins(time) {
  var b = time.split(":");
  return b[0] * 60 + +b[1];
}

// Convert minutes to a time in format hh:mm
// Returned value is in range 00  to 24 hrs
function timeFromMins(mins) {
  function z(n) {
    return (n < 10 ? "0" : "") + n;
  }
  var h = ((mins / 60) | 0) % 24;
  var m = mins % 60;
  return z(h) + ":" + z(m);
}

// Add two times in hh:mm format
function addTimes(t0, t1) {
  return timeFromMins(timeToMins(t0) + timeToMins(t1));
}
