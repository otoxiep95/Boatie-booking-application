const urlParams = new URLSearchParams(window.location.search);
const eventId = urlParams.get("event");
console.log(urlParams, eventId);

if (eventId) {
  setTimeout(function() {
    const eventElem = document.getElementById(eventId);
    eventElem.scrollIntoView();
  }, 300);
}

/**
 * Add event listener to the event boxes
 */
function eventBoxesAddEventListerClick() {
  let eventsBoxes = document.querySelectorAll(".event_box");

  eventsBoxes.forEach(box => {
    box.addEventListener("click", handleBoxClick);
  });
}

/**
 * Handle click on event box
 *
 */
function handleBoxClick(e) {
  let isBookButton = e.target.classList.contains("book-event-button");
  if (isBookButton) {
    // redirect to other page
  } else {
    e.currentTarget.classList.toggle("open");
  }
}

/**
 * Fetch all events
 */
function fetchAllEvents() {
  axios
    .get("api/get-all-upcoming-events.php")
    .then(function(response) {
      if (response.data.statusCode == 200) {
        //handle success
        displayEvents(response.data.data);
      } else {
        // handle error
      }
    })
    .catch(function(error) {});
}
fetchAllEvents();

/**
 * Display all events
 */
function displayEvents(events) {
  let events1 = [];
  const template = document.querySelector("#event-template").content;
  const container = document.querySelector(".right-container");
  const menuBar = document.querySelector("#events .menubar");
  let existingMonthsHeader = [];

  if (events.length > 0) {
    events.forEach(elem => {
      // Get month as text
      let date = convertStringToDate(elem.date);
      let monthName = dateToMonth(date);

      let clone = template.cloneNode(true);
      clone.firstElementChild.dataset.id = elem.event_id;
      clone.firstElementChild.id = "id-" + elem.event_id;
      clone.querySelector(".event_img").style.backgroundImage =
        "url(uploads/events/" + elem.img + ")";
      clone.querySelector(".event_title").textContent = elem.name;
      clone.querySelector(".event_date").textContent =
        date.getDate() + ". " + monthName + " " + date.getFullYear();
      clone.querySelector(".event_info_text").textContent = elem.description;
      clone.querySelector(".time_book").textContent =
        elem.start_time + " - " + elem.end_time;
      clone.querySelector(".price_book").textContent =
        elem.price_person + " DKK/person";
      clone
        .querySelector(".book-event-button")
        .setAttribute("href", "booking-event.php?id=" + elem.event_id);

      //Hide dropdown icon if description length is below 200
      if (elem.description.length < 200) {
        clone.querySelector(".expand_info").style.opacity = "0";
        clone.firstElementChild.style.cursor = "default";
      }

      // Handler to append into the correct month section
      //Check if month section already exists, if false, add it
      if (!existingMonthsHeader.includes(monthName)) {
        //month does not exist yet, create new section
        let header = document.createElement("h3");
        header.textContent = monthName;
        header.id = monthName.toLowerCase();
        header.classList.add("month-header");
        container.appendChild(header);
        existingMonthsHeader.push(monthName);
        menuBar.querySelector(
          "ul"
        ).innerHTML += `<li id="link-${monthName.toLowerCase()}"><a href="#${monthName.toLowerCase()}">${monthName}</a></li>`;
      }

      let monthLower = monthName.toLowerCase();
      let section = document.querySelector("#" + monthLower);
      section.parentNode.insertBefore(clone, section.nextSibling);
    });
  } else {
    let empty = document.createElement("p");
    empty.textContent = "There are currently no events";
    empty.classList.add("empty-events");
    container.appendChild(empty);
    menuBar.innerHTML = "";
  }
  eventBoxesAddEventListerClick();
  eventListenerMenuBar();
}

//After displaying the menu bar, add click events on all links
function eventListenerMenuBar() {
  const menuBarLinks = document.querySelectorAll("#events .menubar ul li");
  menuBarLinks.forEach(link => {
    link.addEventListener("click", handleMenuBar);
  });
  menuBarLinks[0].classList.add("active");
}

// Handle click on a link
function handleMenuBar(e) {
  const menuBarLinks = document.querySelectorAll("#events .menubar ul li");
  menuBarLinks.forEach(link => {
    link.classList.remove("active");
  });
  e.currentTarget.classList.add("active");
}

/**
 * Update active menu link on scroll
 */
window.addEventListener("scroll", e => {
  const monthHeaders = document.querySelectorAll(".month-header");
  const menuBarLinks = document.querySelectorAll("#events .menubar ul li");
  monthHeaders.forEach(header => {
    //check if one of the headers is within 0px - 50px range from the top of the window
    let offset = header.getBoundingClientRect();
    let offsetTop = offset.top;
    if (offsetTop < 50 && offsetTop > 0) {
      // one of the header is within range, update active header section
      menuBarLinks.forEach(link => {
        link.classList.remove("active");
      });
      let monthName = header.id;
      let selection = document.querySelector("#link-" + monthName);
      selection.classList.add("active");
      //console.log("Truuee");
    }
  });
});

/**
 * Convert date to month
 *
 * @param {string} date The string of the date, not a Date type!
 */
function dateToMonth(date) {
  let month = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  ][date.getMonth()];
  return month;
}

/**
 * Get weekday name based on date
 */
function dateToWeekdayName(date) {
  let weekday = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday"
  ][date.getDay()];
  return weekday;
}

/**
 * Convert string date to Date type
 */
function convertStringToDate(text) {
  return new Date(text.replace(/(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
}

/**
 * Calculate offset
 */
