const bookingEventPage = document.querySelector("#booking-event");

//Only run this script on booking event page
if (bookingEventPage) {
  /**
   * Check if id exists in url, else redirect to event page
   */
  urlParam = getURLParam();
  if (!urlParam["id"]) {
    window.location.href = "events.php";
  }

  /**
   * Check people amount and limit
   */
  let maxPeople = 12;
  let bookedOutDiv = document.querySelector(".booked-out");
  let bookingContent = document.querySelector(".booking-content");
  function fetchPeopleAmount() {
    let formData = new FormData();
    formData.append("id", urlParam["id"]);

    axios
      .post("api/get-people-amount-event.php", formData)
      .then(function(response) {
        if (response.data.statusCode == 200) {
          //manual redirect
          maxPeople = maxPeople - response.data.people_amount.people;
          if (maxPeople < 1) {
            bookedOutDiv.style.display = "block";
            bookingContent.style.opacity = ".3";
            bookingContent.style.pointerEvents = "none";
          }
        } else {
          // handle error
          // errorMessageElem.textContent = response.data.message;
        }
      })
      .catch(function(error) {});
  }
  // setTimeout(fetchPeopleAmount, 10000);
  fetchPeopleAmount();

  // Input fields check
  const bookingEventFirstName = document.querySelector(
    "#booking-event input[name='first-name']"
  );
  const bookingEventLastName = document.querySelector(
    "#booking-event input[name='last-name']"
  );
  const bookingEventEmail = document.querySelector(
    "#booking-event input[name='email']"
  );
  const bookingEventPhone = document.querySelector(
    "#booking-event input[name='phone']"
  );
  const bookingEventGroupSize = document.querySelector(
    "#booking-event input[name='group-size']"
  );
  const bookingErrorMessage = document.querySelector(
    "#booking-event .error-messages p"
  );
  const bookingEventButton = document.querySelector("#booking-event-button");
  // Check if fields have input values on input change
  const bookingEventInputs = document.querySelectorAll("#booking-event input");
  bookingEventInputs.forEach(input => {
    input.addEventListener("input", checkFields);
  });

  function checkFields() {
    if (
      bookingEventFirstName.value.length > 1 &&
      bookingEventLastName.value.length > 1 &&
      bookingEventEmail.value.length > 5 &&
      bookingEventPhone.value.length > 1 &&
      maxPeople > 1
    ) {
      bookingEventButton.classList.remove("inactive");
      bookingErrorMessage.textContent = "";
      return true;
    } else {
      bookingErrorMessage.textContent = "Some input fields are not complete.";
      bookingEventButton.classList.add("inactive");
      return false;
    }
  }

  /**
   * Book the event
   */
  bookingEventButton.addEventListener("click", bookEvent);

  function bookEvent(e) {
    e.preventDefault();
    sessionStorage.setItem(
      "confirmation",
      JSON.stringify({
        name: bookingEventFirstName.value,
        email: bookingEventEmail.value
      })
    );

    let formData = new FormData();
    formData.append("event_id", urlParam["id"]);
    formData.append("first_name", bookingEventFirstName.value);
    formData.append("last_name", bookingEventLastName.value);
    formData.append("email", bookingEventEmail.value);
    formData.append("phone", bookingEventPhone.value);
    formData.append("group_size", bookingEventGroupSize.value);

    axios
      .post("api/create-event-booking.php", formData)
      .then(function(response) {
        if (response.data.statusCode == 200) {
          //manual redirect
          window.location.href = "booking-confirmation.php";
        } else {
          // handle error
          bookingErrorMessage.textContent = response.data.message;
        }
      })
      .catch(function(error) {});
  }

  /**
   * Group size, handle minues/plus click with min=1 and max=12
   */
  const minusPlusButtons = document.querySelectorAll(
    ".event-group-size-container .group-size p"
  );
  minusPlusButtons.forEach(btn => {
    btn.addEventListener("click", handleMinusPlusGroupSize);
  });
  function handleMinusPlusGroupSize(e) {
    let size = Number(bookingEventGroupSize.value);
    if (e.currentTarget.classList.contains("minus")) {
      //minus
      bookingEventGroupSize.value = size > 1 ? size - 1 : 1;
    } else {
      //plus
      bookingEventGroupSize.value = size < maxPeople ? size + 1 : maxPeople;
    }
  }
}

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
