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
 * Second page booking methods
 */
  /**
   * Check if the first page has been used
   */

  if (sessionStorage.getItem("firstpage") !== null) {
    //first page data exists, continue

    fetchAvailableTimeSlots();
  } else {
    //display message that you first need to visit the first page for data
  }

  /**
   * Fetch all available time slots from backend with the options
   */
  function fetchAvailableTimeSlots() {
    let firstPageData = JSON.parse(sessionStorage.getItem("firstpage"));
    let dateInput = document.querySelector("#date-picker");
    let dateValue = dateInput.value;

    let formData = new FormData();
    formData.append("date", dateValue);
    formData.append("duration", firstPageData.duration);
    formData.append("pickup_loc_id", firstPageData.dropoffId);
    formData.append("dropoff_loc_id", firstPageData.pickupId);

    axios
      .post("api/get-available-trip-time-slots.php", formData)
      .then(function(response) {
        if (response.data.statusCode == 200) {
          removeAllBookingTimesElem();
          setTimeout(() => {
            displayTimeSlots(response.data);
          }, 500);
        } else {
          // handle error
        }
      })
      .catch(function(error) {});
  }

  /**
   * Display all available time slots from fetched backend
   */
  const bookingTimesContainer = document.querySelector(".booking-times");
  function displayTimeSlots(data) {
    if (data.date_available == 1) {
      let timeSlots = data.data;

      timeSlots.forEach(slot => {
        if (slot != "unavailable" && slot != "on-request") {
          let divSlot = document.createElement("div");
          divSlot.textContent = slot;
          bookingTimesContainer.appendChild(divSlot);
        }
      });
      bookingTimesContainer.innerHTML +=
        "<p>For requests starting later than 22.00, please give us a call at <a href='tel:004560566609'>+45 60 56 66 09</a> or write an email to <a href='mailto:info@boatie.dk'>info@boatie.dk</a>.</p>";
      addEventListenerOnTimeSlots();
    } else {
      bookingTimesContainer.innerHTML += "<p>Sorry. We're closed that day.</p>";
    }

    // const element = document.querySelector(".booking-times-wrapper");
    // element.Scrolltop = 200;
    const element = document.querySelectorAll("#booking-time-slots div");
    element[element.length - 10].scrollIntoView({ block: "end" });
  }

  /**
   * Add event listener to all timeslots
   */

  function addEventListenerOnTimeSlots() {
    let timeSlots = document.querySelectorAll(".booking-times div");
    timeSlots.forEach(slot => {
      slot.addEventListener("click", handleTimeSlotSelection);
    });
  }

  /**
   * Select time slot and mark as active, also remove inactive class from next button
   */
  let selectedTimeSlot;
  function handleTimeSlotSelection(e) {
    selectedTimeSlot = e.target.textContent;
    let timeSlots = document.querySelectorAll(".booking-times div");
    timeSlots.forEach(slot => {
      slot.classList.remove("selected");
    });
    e.target.classList.add("selected");
    secondPageNextButton.classList.remove("inactive");
    allowSecondPageButton = true;
  }

  /**
   * Next button handler
   */
  let allowSecondPageButton = false;
  secondPageNextButton.addEventListener("click", handleSecondNextButton);
  function handleSecondNextButton(e) {
    let secondPage = JSON.parse(sessionStorage.getItem("firstpage"));
    let dateInput = document.querySelector("#date-picker");
    secondPage.date = dateInput.value;
    secondPage.start_time = selectedTimeSlot;

    //only if a time slot has been selected, allow next page
    if (allowSecondPageButton) {
      //Store date inside session
      sessionStorage.setItem("secondpage", JSON.stringify(secondPage));
    } else {
      e.preventDefault();
    }
  }

  /**
   * Remove all time slots from booking page 2
   */
  function removeAllBookingTimesElem() {
    while (bookingTimesContainer.firstChild) {
      bookingTimesContainer.removeChild(bookingTimesContainer.firstChild);
    }
  }

  /**
   * Flatpickr setup
   */
  let in3days = new Date();
  in3days.setDate(new Date().getDate() + 3);

  flatpickr("#date-picker", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "l J, M Y",
    weekNumbers: true,
    minDate: "today",
    defaultDate: in3days,
    maxDate: new Date().fp_incr(365),
    onChange: fetchAvailableTimeSlots
  });
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
