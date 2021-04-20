/* ==========================================================================
   Global variables
   ========================================================================== */
const unavailabilitiesForm = document.querySelector(
  ".handle-unavailability-container"
);
const mailchimpForm = document.querySelector(".handle-mailchimp-container");
const calendarApiForm = document.querySelector(".handle-calendar-container");
const createUnavailabilityButton = unavailabilitiesForm.querySelector(
  ".create-button"
);
const saveMailchimpButton = mailchimpForm.querySelector("button");
const saveCalendarButton = calendarApiForm.querySelector("button");

//console.log(unavailabilitiesForm, createUnavailabilityButton);

/*  ==========================================================================
       Initialize
       ========================================================================== */
document.addEventListener("DOMContentLoaded", init);
function init() {
  //do stuff after page has loaded
  fetchUnavailabilities();
  fetchSettings();
}

/*  ==========================================================================
       Unavailabilities Part
       ========================================================================== */

createUnavailabilityButton.addEventListener("click", createUnavailability);

/**
 * Fetch Unavailabilities
 *
 *
 *
 */

function fetchUnavailabilities() {
  axios
    .post("../api/get-unavailabilities.php")
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        displayUnavailabilities(response.data);
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}
/**
 * Display Unavailabilities
 *
 *@param data = array of all future unavailabilities
 *
 */

function displayUnavailabilities(data) {
  //First clean the table
  cleanUnavailabilitiesTable();

  //Then display the new data
  const template = document.querySelector("#unavailabilities-template").content;
  const container = document.querySelector("#unavailabilities-table");

  let unavailabilities = data.data.unavailabilities;

  unavailabilities.forEach(elem => {
    let clone = template.cloneNode(true);

    clone.firstElementChild.id = "unavailability-" + elem.id;
    clone.querySelector(".unavailability-start-date").textContent =
      elem.start_date;
    clone.querySelector(".unavailability-end-date").textContent = elem.end_date;
    clone.querySelector(".delete-button").dataset.unavailability_id = elem.id;
    clone
      .querySelector(".delete-button")
      .addEventListener("click", deleteUnavailability);
    container.appendChild(clone);
  });
}

function cleanUnavailabilitiesTable() {
  // Remove all users. This is a fast method to do so: https://stackoverflow.com/a/3955238
  const container = document.querySelector("#unavailabilities-table");
  while (container.firstChild) {
    container.removeChild(container.firstChild);
  }

  // Add table headers
  container.innerHTML =
    "<tr><th>Start Date</th><th>End Date</th><th> </th></tr>";
}

/**
 * Creates Unavailabilitiy backend
 *
 *
 *
 */

function createUnavailability() {
  formData = new FormData();
  let startDate = unavailabilitiesForm.querySelector(".start-date").value;
  let endDate = unavailabilitiesForm.querySelector(".end-date").value;
  formData.append("start-date", startDate);
  formData.append("end-date", endDate);

  axios
    .post("../api/create-unavailability.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        // console.log("status 200");
        unavailabilitiesForm.reset();
        fetchUnavailabilities();
      } else {
      }
    })
    .catch(function(error) {
      // console.log(error);
    });
}

/**
 * Delete Unavailabilitiy
 *
 *
 *
 */

function deleteUnavailability(evt) {
  let id = evt.target.dataset.unavailability_id;
  deleteUnavailabilityFrontend(id);

  formData = new FormData();
  formData.append("id", id);

  axios
    .post("../api/delete-unavailability.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        // console.log("status 200");
      } else {
      }
    })
    .catch(function(error) {
      // console.log(error);
    });
}

function deleteUnavailabilityFrontend(id) {
  unavailabilityTr = document.querySelector("#unavailability-" + id);
  //console.log(unavailabilityTr);

  unavailabilityTr.remove();
}

/*  ==========================================================================
       Settings Part
       ========================================================================== */

saveMailchimpButton.addEventListener("click", createOrUpdateSetting);
saveCalendarButton.addEventListener("click", createOrUpdateSetting);

function fetchSettings() {
  axios
    .post("../api/get-settings.php")
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        //console.log("status 200");
        displaySettings(response.data.data);
      } else {
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}

function displaySettings(data) {
  //console.log("data", data);
  data.settings.forEach(element => {
    //console.log(element);
    const mailchimpInput = mailchimpForm.querySelector("input");
    const gCalendarInput = calendarApiForm.querySelector("input");
    if (element.s_key == mailchimpInput.name) {
      mailchimpInput.value = element.s_value;
    }
    if (element.s_key == gCalendarInput.name) {
      gCalendarInput.value = element.s_value;
    }
  });
}

function createOrUpdateSetting(evt) {
  let formData = new FormData();
  //console.log(evt.target.parentElement);
  form = evt.target.parentElement;
  let keyValue = form.querySelector("input").value;
  let keyName = form.querySelector("input").name;
  // console.log(keyName, keyValue);
  formData.append("key-name", keyName);
  formData.append("key-value", keyValue);
  const statusMessage = form.querySelector(".status");

  statusMessage.textContent = "Success!";
  statusMessage.style.opacity = 1;
  statusMessage.style.color = "green";
  setTimeout(() => {
    statusMessage.style.opacity = 0;
  }, 2000);
  axios
    .post("../api/create-or-update-settings.php", formData)
    .then(function(response) {
      // console.log(response.data);
      if (response.data.statusCode == 200) {
        // console.log("status 200");
      } else {
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}
