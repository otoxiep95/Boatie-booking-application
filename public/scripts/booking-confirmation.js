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
 * Confirmation booking page
 */

let confirmationData = JSON.parse(sessionStorage.getItem("confirmation"));
if (confirmationData !== null) {
  confirmationPage.querySelector(".conf-name").textContent =
    confirmationData.name;
  confirmationPage.querySelector(".conf-email").textContent =
    confirmationData.email;
  sessionStorage.clear();
} else {
  window.location.href = "booking.php";
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
