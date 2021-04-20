/* ==========================================================================
   Global variables
   ========================================================================== */

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

/**
 * This function returns an object of URL parameters of an URL.
 * E.g. if url -> "/index.html?test1=t1&test2=t2&test3=t3"
 *    console.log(getURLParam()); //-> {test1: "t1"}
 */
function getURLParam() {
  var s1 = location.search.substring(1, location.search.length).split("&"),
    r = {},
    s2,
    i;
  for (i = 0; i < s1.length; i += 1) {
    s2 = s1[i].split("=");
    r[decodeURIComponent(s2[0]).toLowerCase()] = decodeURIComponent(s2[1]);
  }
  return r;
}
/**
 * [Dashboard Clients Modal] -  open/close toggle and captain assigning dropdown
 */

// Open modal box and get additional info by id
if (document.querySelector("#dashboard-customers")) {
  const customersManageButtons = document.querySelectorAll(
    "#dashboard-customers .manage-button"
  );
  customersManageButtons.forEach(btn => {
    btn.addEventListener("click", openCustomersModalBox);
  });

  function openCustomersModalBox(evt) {
    //console.log(evt.target.dataset.trip_id);
    customersModalBox.classList.add("active");
  }

  // Close customers Modal Box
  const customersModalBox = document.querySelector("#manage-customers-modal");

  customersModalBox
    .querySelector(".close-button")
    .addEventListener("click", closeCustomersModalBox);

  function closeCustomersModalBox() {
    customersModalBox.classList.remove("active");
  }
}

/**
 * Dashboard Burger Menu sidebar toggle
 */
const burgerMenuButton = document.querySelector("#burger-menu");
const dashboardDIV = document.querySelector("#dashboard");

if (document.querySelector("#burger-menu")) {
  burgerMenuButton.addEventListener("click", toggleMobileMenuSidebar);
}

function toggleMobileMenuSidebar() {
  dashboardDIV.classList.toggle("mobile-menu-active");
}

/**
 * [Dashboard Settings Page] -  open/close toggle and input update/creation
 */
if (document.querySelector("#main-dashboard-settings2")) {
  /* Date picker for events modal */
  flatpickr("#main-dashboard-settings2 #datepicker", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "l J, M Y",
    weekNumbers: true,
    minDate: "today"
  });

  /* start time picker for events modal */
  flatpickr("#start-time-picker", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    minuteIncrement: "15"
  });

  /* start time picker for events modal */
  flatpickr("#end-time-picker", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    minuteIncrement: "15"
  });
}
