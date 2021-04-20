/* ==========================================================================
   Global variables
   ========================================================================== */
const progressStepsTopCircles = document.querySelectorAll(".step");
const firstPageNextButton = document.querySelector("#first-next-step-button");
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


firstPageNextButton.addEventListener("click", handleFirstPage);
const durationSelection = document.querySelector("#duration-select");
const pickupSelection = document.querySelector("#pickup-select");
const dropoffSelection = document.querySelector("#dropoff-select");
function handleFirstPage(e) {
  //Prevent redirect
  e.preventDefault();

  //Get all values
  let duration =
    durationSelection.options[durationSelection.selectedIndex].value;

  let pickupId = pickupSelection.options[pickupSelection.selectedIndex].value;
  let pickupName =
    pickupSelection.options[pickupSelection.selectedIndex].text;
  let dropoffId =
    dropoffSelection.options[dropoffSelection.selectedIndex].value;
  let dropoffName =
    dropoffSelection.options[dropoffSelection.selectedIndex].text;

  //Save them inside a session
  sessionStorage.setItem(
    "firstpage",
    JSON.stringify({
      duration,
      pickupId,
      pickupName,
      dropoffId,
      dropoffName
    })
  );

  //manual redirect
  window.location.href = "booking-2.php";
}

/**
 * Mapbox map
 */
const allLocations = document.querySelectorAll("#pickup-select option");
const mapBoxToken =
  "pk.eyJ1IjoiYm9hdGllIiwiYSI6ImNrM3gwOXRrOTBseGkza3A2cmE3Z29lNzMifQ.Z457ye5P6w-Z-pcAkzp5eA";
mapboxgl.accessToken = mapBoxToken;
let mapSettings = {
  container: "locations-map",
  style: "mapbox://styles/mapbox/streets-v11",
  center: [
    allLocations[0].dataset.latitude,
    allLocations[0].dataset.longitude
  ],
  zoom: 18
};
let map = new mapboxgl.Map(mapSettings);

/**
 * Get all locations
 */

allLocations.forEach(loc => {
  let el = document.createElement("div");
  el.className = "marker";
  el.id = "marker-" + loc.value;
  el.setAttribute("data-id", "marker-" + loc.value);

  let popup = new mapboxgl.Popup({ offset: 25 }).setText(loc.text);

  new mapboxgl.Marker(el)
    .setLngLat([loc.dataset.latitude, loc.dataset.longitude])
    .setPopup(popup) // sets a popup on this marker
    .addTo(map);
});

// Update active marker on dropdown selection change
pickupSelection.addEventListener("change", () => {
  updateActiveMarker("pickup");
});
dropoffSelection.addEventListener("change", () => {
  updateActiveMarker("dropoff");
});
function updateActiveMarker(pd) {
  let latitude, longitude;
  if (pd == "pickup") {
    latitude =
      pickupSelection.options[pickupSelection.selectedIndex].dataset.latitude;
    longitude =
      pickupSelection.options[pickupSelection.selectedIndex].dataset
        .longitude;
  } else {
    latitude =
      dropoffSelection.options[dropoffSelection.selectedIndex].dataset
        .latitude;
    longitude =
      dropoffSelection.options[dropoffSelection.selectedIndex].dataset
        .longitude;
  }
  map.flyTo({
    center: [latitude, longitude],
    zoom: 18
  });
}

// Update active marker on show pickup/dropoff button
const showPickupDropoffButtons = document.querySelectorAll(".show-on-map");
showPickupDropoffButtons.forEach(btn => {
  btn.addEventListener("click", e => {
    if (e.currentTarget.classList.contains("show-pickup")) {
      updateActiveMarker("pickup");
    } else {
      updateActiveMarker("dropoff");
    }
  });
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
