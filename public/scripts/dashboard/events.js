/* ==========================================================================
   Global variables
   ========================================================================== */
let page = 1; // the current page
let perPage = 20; // limit of items per page
let maxPage = 1; // max available pages for pagination. Will instantly be updated to get the correct value from the DB.
let pastEvents = false;
/*  ==========================================================================
    Initialize
    ========================================================================== */
document.addEventListener("DOMContentLoaded", init);
function init() {
  //do stuff after page has loaded

  urlParam = getURLParam();
  page = "page" in urlParam ? urlParam["page"] : page;
  perPage = "limit" in urlParam ? urlParam["limit"] : perPage;
  pastEvents = "past-events" in urlParam ? urlParam["past-events"] : pastEvents;
  pastEvents = pastEvents == "true";

  fetchEvents();
  selectAttendeeButtons();
  selectEventsButtons();
  shiftEventsPastUpcoming(pastEvents);
}

/*  ==========================================================================
    Functions 
    ========================================================================== */
/**
 * Select attendee delete buttons
 */

let removeAttendeeBtns;

function selectAttendeeButtons() {
  const removeAttendeeBtns = document.querySelectorAll(
    ".client-details .remove-button"
  );

  removeAttendeeBtns.forEach(btn => {
    btn.addEventListener("click", deleteSingleAttendee);
  });
}

/**
 * [Dashboard Events Modal] -  open/close toggle and input update/creation
 */

let eventsManageButtons;

//Open modal when you click manage btn or create btn

function selectEventsButtons() {
  const eventsManageButtons = document.querySelectorAll(
    "#dashboard-events .manage-button"
  );
  const createEventButton = document.querySelector(".create-event-btn");

  eventsManageButtons.forEach(btn => {
    btn.addEventListener("click", openEventsModalBox);
  });

  createEventButton.addEventListener("click", openEventsModalBox);
}

// Open events modal box

function openEventsModalBox(evt) {
  //check if event exists (data-exists) to see if its a create event or manage event
  let eventExists = "true" == evt.target.dataset.exists;
  document.querySelector(".error-message").textContent = " ";

  //if event does not exist (create)
  if (eventExists === false) {
    //reset all form values
    eventsModalBox.querySelector("form").reset();

    //set default date
    let in1day = new Date();
    in1day.setDate(new Date().getDate() + 1);

    const eventDate = document.querySelector(".event-date-value");
    const fp = flatpickr(eventDate, {
      maxDate: new Date().fp_incr(365)
    });

    fp.setDate(in1day, true, "d/m/Y");

    //set default time
    document.querySelector("#start-time-picker").value = "16:00";
    document.querySelector("#end-time-picker").value = "17:00";

    //give modal class of active and change header text
    eventsModalBox.classList.add("active");
    eventsModalBox.querySelector(".create-event-title").textContent =
      "Create event";

    //default placeholder image
    eventsModalBox.querySelector(
      ".event-image .image-preview"
    ).style.backgroundImage = "url(../assets/images/boatie-placeholder.jpg)";
    eventsModalBox.querySelector(
      ".event-image .image-preview"
    ).style.backgroundSize = "cover";
    eventsModalBox.querySelector(
      ".event-image .image-preview"
    ).style.backgroundPosition = "center center";

    //no grid while one button
    eventsModalBox.querySelector(".button-wrapper").style.display = "grid";
    eventsModalBox.querySelector(".button-wrapper").style.gridTemplateColumns =
      "1fr";

    //hide attendees
    eventsModalBox.querySelector(".client-details").style.display = "none";

    //show/hide buttons
    eventsModalBox.querySelector(".update-event").style.display = "none";
    eventsModalBox.querySelector(".delete-event").style.display = "none";
    eventsModalBox.querySelector(".submit-event").style.display = "block";

    document.querySelector("#event-image").value = "";

    //if event exists (manage)
  } else if (eventExists === true) {
    //get event Id from manage button dataset
    const eventId = evt.target.dataset.event_id;

    //fetch single event
    fetchSingleEvent(eventId);

    //fetch attendees
    fetchAttendees(eventId);

    //give modal class of active and change header text
    eventsModalBox.classList.add("active");
    eventsModalBox.querySelector(".create-event-title").textContent =
      "Manage event";

    //button gris when two buttons
    eventsModalBox.querySelector(".button-wrapper").style.display = "grid";
    eventsModalBox.querySelector(".button-wrapper").style.gridTemplateColumns =
      "1fr 1fr";
    eventsModalBox.querySelector(".button-wrapper").style.gridGap = "10px";

    //show attendees
    eventsModalBox.querySelector(".client-details").style.display = "block";

    //show/hide buttons
    eventsModalBox.querySelector(".submit-event").style.display = "none";
    eventsModalBox.querySelector(".update-event").style.display = "block";
    eventsModalBox.querySelector(".delete-event").style.display = "block";

    document.querySelector("#event-image").value = "";

    eventsModalBox.querySelector(
      ".event-image .image-preview"
    ).style.backgroundSize = "cover";
    eventsModalBox.querySelector(
      ".event-image .image-preview"
    ).style.backgroundPosition = "center center";
  }
}

/**
 * Fetch a single event data
 *
 * @param {int} eventId The id of the single event to fetch and display in the modal
 */

function fetchSingleEvent(eventId) {
  let formData = new FormData();
  formData.append("id", eventId);

  axios
    .post("../api/get-single-event.php", formData)
    .then(function(response) {
      if (response.data.statusCode == 200) {
        updateModalEvent(response.data);
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}

/**
 * Fetch attendees based on eventId
 *
 * @param {int} eventId The id of the single event to fetch and display attendees in the modal
 */

function fetchAttendees(eventId) {
  let formData = new FormData();
  formData.append("id", eventId);

  axios
    .post("../api/get-attendees.php", formData)
    .then(function(response) {
      if (response.data.statusCode == 200) {
        displayAttendees(response.data);
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}

/**
 * Update the modal window with the new requested event
 *
 * @param {json} data Object or array containing data of a single event
 */
function updateModalEvent(data) {
  let event = data.data.event;

  eventsModalBox.querySelector(
    ".event-details .title-group .event-title-value"
  ).value = event.name;

  eventsModalBox.querySelector(
    ".event-details .price-group .event-price-value"
  ).value = event.price_person;

  eventsModalBox.querySelector(
    ".event-details .start-group #start-time-picker"
  ).value = event.start_time;

  eventsModalBox.querySelector(
    ".event-details .end-group #end-time-picker"
  ).value = event.end_time;

  eventsModalBox.querySelector(
    ".event-description .textarea-group .event-description-value"
  ).value = event.description;

  eventsModalBox.querySelector(".event-image .image-preview").style.background =
    "url(../uploads/events/" + event.img + ")";

  eventsModalBox.querySelector(".button-wrapper .delete-event").dataset.id =
    event.event_id;

  eventsModalBox.querySelector(".button-wrapper .update-event").dataset.id =
    event.event_id;

  // set date picker value to event date
  const eventDate = document.querySelector(".event-date-value");
  const fp = flatpickr(eventDate, {
    maxDate: new Date().fp_incr(365)
  });

  fp.setDate(event.date, true, "d/m/Y");
}

// Close Events Modal Box
const eventsModalBox = document.querySelector("#events-modal");

// Close when clicking close button
eventsModalBox
  .querySelector(".close-button")
  .addEventListener("click", closeEventsModalBox);

// Close when clicking shadow background
eventsModalBox
  .querySelector(".shadow-background")
  .addEventListener("click", closeEventsModalBox);

function closeEventsModalBox() {
  eventsModalBox.classList.remove("active");

  //remove validation error class
  const eventInputs = document.querySelectorAll(
    ".handle-event-container input"
  );

  document
    .querySelector(".handle-event-container textarea")
    .classList.remove("error");

  eventInputs.forEach(input => {
    input.classList.remove("error");
  });
  cleanAttendeesTable();
}

// Validate input fields

const eventInputs = document.querySelectorAll(".handle-event-container input");
const eventTextarea = document.querySelector(
  ".handle-event-container textarea"
);

eventTextarea.addEventListener("input", checkFields);

eventInputs.forEach(input => {
  input.addEventListener("input", checkFields);
});

function checkFields(evt) {
  if (evt.target.value.length == 0 || !evt.target.value) {
    document.querySelector(".error-message").textContent =
      "Add a " + evt.target.name;
    document.querySelector(".error-message-mobile").textContent =
      "Add a " + evt.target.name;
    evt.target.classList.add("error");
    return false;
  }

  if (evt.target.value.length < 2) {
    document.querySelector(".error-message").textContent =
      evt.target.name + " is too short";
    document.querySelector(".error-message-mobile").textContent =
      evt.target.name + " is too short";

    evt.target.classList.add("error");
    return false;
  }

  if (evt.target.type == "text") {
    if (evt.target.value.length > 80) {
      document.querySelector(".error-message").textContent =
        evt.target.name + " is too long";
      document.querySelector(".error-message-mobile").textContent =
        evt.target.name + " is too long";
      evt.target.classList.add("error");
      return false;
    }
  }

  if (evt.target.type == "textarea") {
    if (evt.target.value.length > 1200) {
      document.querySelector(".error-message").textContent =
        evt.target.name + " is too long";
      document.querySelector(".error-message-mobile").textContent =
        evt.target.name + " is too long";
      evt.target.classList.add("error");
      return false;
    }
  }

  if (evt.target.type == "file") {
    let image = document.querySelector("#event-image").files[0];
    let imageType = image.type
      .split("/")
      .pop()
      .toLowerCase();

    if (imageType != "jpeg" && imageType != "jpg" && imageType != "png") {
      document.querySelector(".error-message").textContent =
        "Image must be jpeg, jpg or png";
      document.querySelector(".error-message-mobile").textContent =
        "Image must be jpeg, jpg or png";
      document.querySelector("#event-image").value = "";
      evt.target.classList.add("error");
      return false;
    }
  }

  document.querySelector(".error-message").textContent = "";
  document.querySelector(".error-message-mobile").textContent = "";
  evt.target.classList.remove("error");
  return true;
}

/**
 * Fetch Events pagination
 *
 * @param {integer} pageArg Optional number of the page, starts at page 0, default 0
 * @param {integer} perPageArg Optional number of items per page, defualt 30
 */

function fetchEvents(pageArg, perPageArg, pastEventsArg) {
  page = pageArg && pageArg == 0 ? pageArg : page;
  perPage = perPageArg ? perPageArg : perPage;
  pastEvents =
    typeof pastEventsArg !== "undefined" ? pastEventsArg : pastEvents;

  let formData = new FormData();
  formData.append("page", Number(page));
  formData.append("perPage", Number(perPage));
  formData.append("pastEvents", pastEvents);

  axios
    .post("../api/get-events.php", formData)
    .then(function(response) {
      if (response.data.statusCode == 200) {
        cleanEventsTable(); //clean table
        paginationHandler(response.data); //handle pagination
        displayEvents(response.data); //display events
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}

/**
 *
 * Display the fetched events using a <template>
 *
 * @param {json} data Object or array containing the data
 */

function displayEvents(data) {
  //Target template and table
  const template = document.querySelector("#events-template").content;
  const container = document.querySelector("#events-table");
  let events = data.data.events;

  if (events.length < 1) {
    container.innerHTML += "<p class='no-events'>There are not events yet </p>";
    return true;
  }

  //For each event, make a clone
  events.forEach(elem => {
    let clone = template.cloneNode(true);
    clone.firstElementChild.id = "event-" + elem.event_id;
    clone.querySelector(".event-start-time").textContent = elem.start_time;
    clone.querySelector(".event-date").textContent = elem.date;
    clone.querySelector(".event-pickup").textContent = elem.pickup_loc_name;
    clone.querySelector(".event-name").textContent = elem.name;
    clone.querySelector(".event-captain").textContent = elem.captain_name;
    clone.querySelector(".manage-button").dataset.event_id = elem.event_id;
    container.appendChild(clone);
  });
  selectEventsButtons(); //call event buttons
  paginationPrevNext(); //hide/show prev next buttons
}

/**
 *
 * Display the fetched attendees using a <template>
 *
 * @param {json} data Object or array containing the data
 */

function displayAttendees(data) {
  const template = document.querySelector("#attendees-template").content;
  const container = document.querySelector("#attendees-table");
  let attendees = data.data.attendees;

  if (attendees.length < 1) {
    //If there are no attendees, show "there are no attendees"
    container.innerHTML +=
      "<p class='no-attendees'>There are not attendees yet </p>";
    document.querySelector(".client-details span").textContent = "";
  }

  //array to push the group sizes
  let groupSizeArray = [];

  attendees.forEach(elem => {
    //push group size numbers into groupSizeArray
    let groupSize = elem.group_size;
    groupSizeArray.push(groupSize);

    //display the data
    let clone = template.cloneNode(true);
    clone.firstElementChild.id = "customer-" + elem.customer_id;
    clone.querySelector(".event-attendee-name").textContent =
      elem.first_name + " " + elem.last_name;
    clone.querySelector(".event-attendee-email").textContent = elem.email;
    clone.querySelector(".event-attendee-phone").textContent = elem.phone;
    clone.querySelector(".event-attendee-groupsize").textContent =
      elem.group_size;
    clone.querySelector(".event-attendee-paid").textContent = elem.paid
      ? "Yes"
      : "No";

    clone.querySelector(".remove-button").dataset.customer_id =
      elem.customer_id;

    container.appendChild(clone);
  });

  //add the group size numbers
  let attendeesSum = groupSizeArray.reduce(
    (partial_sum, a) => partial_sum + a,
    0
  );

  //show the amount of attendees
  document.querySelector(".client-details span").textContent =
    "(" + attendeesSum + "/12)";

  selectAttendeeButtons();
}

/**
 * Remove all events in the table and add the table headers
 */

function cleanEventsTable() {
  // Remove all events. This is a fast method to do so: https://stackoverflow.com/a/3955238
  const container = document.querySelector("#events-table");
  while (container.firstChild) {
    container.removeChild(container.firstChild);
  }

  // Add table headers
  container.innerHTML =
    "<tr><th>Time</th><th>Date</th><th>Pickup Loc.</th><th>Name</th><th>Assigned captain</th><th></th></tr>";
}

/**
 * Remove all attendees in the modal table and add the table headers
 */

function cleanAttendeesTable() {
  const container = document.querySelector("#attendees-table");
  while (container.firstChild) {
    container.removeChild(container.firstChild);
  }

  // Add table headers
  container.innerHTML =
    "<tr><th>Name</th><th>Email</th><th>Phone</th><th>Group size</th><th>Paid</th><th></th></tr>";
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
    fetchEvents();
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
    `?page=${page}&limit=${perPage}&past-events=${pastEvents}`
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
  fetchEvents();
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
  fetchEvents();
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

/**
 * Switch between displaying upcoming events and past events
 */

let shiftEventsButtons = document.querySelectorAll(
  "#dashboard-events .left .shift-events"
);

//add click event on the Upcoming and Past button
shiftEventsButtons.forEach(btn => {
  btn.addEventListener("click", evt => {
    let decision = evt.target.dataset.past == "true";
    shiftEventsPastUpcoming(decision);
    fetchEvents(page, perPage, decision);
  });
});

function shiftEventsPastUpcoming(pastEventsArg) {
  // Switch active class between the two buttons
  shiftEventsButtons.forEach(btn => {
    btn.classList.remove("active");
  });

  // Add active class to active button
  if (pastEventsArg) {
    // Past events selected
    shiftEventsButtons[1].classList.add("active");
  } else {
    //Upcoming events selected
    shiftEventsButtons[0].classList.add("active");
  }
}

/**
 *  show notification
 *
 */

let notification = document.querySelector(".notification");

function showNotification(message) {
  //give class of show
  notification.classList.add("show");

  //display specified message
  notification.textContent = message;

  //remove class after 4 seconds
  setTimeout(function() {
    notification.classList.remove("show");
  }, 4000);
}

/**
 * Create new event
 */

eventsModalBox
  .querySelector(".button-wrapper .submit-event")
  .addEventListener("click", createNewEvent);

function createNewEvent() {
  let formData = new FormData();
  form = document.querySelector(".handle-event-container");

  form.elements.forEach(element => {
    //get element names
    const elementName = element.dataset.name;

    //if element is not a button or file input
    if (elementName != "button" && elementName != "img") {
      formData.append(element.name, element.value);
    }
    //if element is a file input (for img), append this way
    if (elementName == "img") {
      formData.append("image", element.files[0]);
    }
  });

  axios
    .post("../api/create-event.php", formData)
    .then(function(response) {
      let eventData = response.data;
      if (eventData.statusCode == 200) {
        closeEventsModalBox();
        showNotification("event created");
        fetchEvents();
      } else {
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}

/**
 * Update event
 */

eventsModalBox
  .querySelector(".button-wrapper .update-event")
  .addEventListener("click", updateEvent);

function updateEvent(evt) {
  //get id from update button dataset
  let id = evt.target.dataset.id;
  let formData = new FormData();
  formData.append("id", id);
  form = document.querySelector(".handle-event-container");

  form.elements.forEach(element => {
    const elementName = element.dataset.name;
    if (elementName != "button" && elementName != "img") {
      formData.append(element.name, element.value);
    }
    if (elementName == "img") {
      formData.append("image", element.files[0]);
    }
  });

  axios
    .post("../api/update-event.php", formData)
    .then(function(response) {
      let eventData = response.data;
      if (eventData.statusCode == 200) {
        closeEventsModalBox();
        showNotification("event updated");
      } else {
        //handle error
      }
    })
    .catch(function(error) {
      // console.log(error);
    });
}

/**
 * Delete event by id
 */

eventsModalBox
  .querySelector(".button-wrapper .delete-event")
  .addEventListener("click", deleteSingleEvent);

function deleteSingleEvent(evt) {
  let id = evt.target.dataset.id;
  let formData = new FormData();
  formData.append("id", id);

  //Optimistic deletion front-end
  document.querySelector("#event-" + id).remove();
  closeEventsModalBox();
  showNotification("event deleted");

  axios
    .post("../api/delete-event.php", formData)
    .then(function(response) {
      let eventData = response.data;
      //console.log(eventData);
      if (eventData.statusCode == 200) {
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      // console.log(error);
    });
}

/**
 * Delete attendee by id
 */

function deleteSingleAttendee(evt) {
  let id = evt.target.dataset.customer_id;

  let formData = new FormData();
  formData.append("id", id);

  //Optimistic deletion front-end
  document.querySelector("#customer-" + id).remove();

  axios
    .post("../api/delete-attendee.php", formData)
    .then(function(response) {
      let eventData = response.data;
      if (eventData.statusCode == 200) {
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}

let in1day = new Date();
in1day.setDate(new Date().getDate() + 1);

/* Date picker for events modal */
flatpickr("#events-modal #datepicker", {
  dateFormat: "Y-m-d",
  altInput: true,
  altFormat: "l J, M Y",
  weekNumbers: true,
  minDate: "today",
  maxDate: new Date().fp_incr(365),
  defaultDate: in1day
});

/* start time picker for events modal */
flatpickr("#events-modal #start-time-picker", {
  enableTime: true,
  noCalendar: true,
  dateFormat: "H:i",
  time_24hr: true,
  minuteIncrement: "15",
  defaultHour: 16,
  defaultMinute: 00
});

/* start time picker for events modal */
flatpickr("#events-modal #end-time-picker", {
  enableTime: true,
  noCalendar: true,
  dateFormat: "H:i",
  time_24hr: true,
  minuteIncrement: "15",
  defaultHour: 17,
  defaultMinute: 00
});

// Register the simplebar to the modal, so the scrollbar never disappears
const dashboardListSimpleBar = new SimpleBar(
  document.querySelector(".modal-box-sub-wrapper"),
  {
    autoHide: false
  }
);
