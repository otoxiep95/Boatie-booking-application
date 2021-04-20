/* ==========================================================================
   Global variables
   ========================================================================== */
let page = 1;
let perPage = 20;
let maxPage = 1;

/*  ==========================================================================
    Initialize
    ========================================================================== */
document.addEventListener("DOMContentLoaded", init);
function init() {
  //do stuff after page has loaded
  fetchUsers();
  selectUsersButtons();
  paginationPreviousButton.style.opacity = "0";
  paginationPreviousButton.style.pointerEvents = "none";
}

/**
 * [Dashboard employees Modal] -  open/close toggle and captain assigning dropdown
 */

// Open modal box and get additional info by id
function selectUsersButtons() {
  const employeesManageButtons = document.querySelectorAll(
    "#dashboard-employees .manage-button"
  );
  const createEmployeeButton = document.querySelector(".create-employee-btn");

  employeesManageButtons.forEach(btn => {
    btn.addEventListener("click", openEmployeesModalBox);
  });
  createEmployeeButton.addEventListener("click", openEmployeesModalBox);
}
function selectModalButtons() {
  const updateButton = employeesModalBox.querySelector(".update-btn");
  const createButton = employeesModalBox.querySelector(".create-btn");
  const deleteButton = employeesModalBox.querySelector(".delete-btn");

  createButton.addEventListener("click", createNewUser);
  updateButton.addEventListener("click", updateUser);
  deleteButton.addEventListener("click", deleteUser);
}

function openEmployeesModalBox(evt) {
  //console.log(evt.target.dataset.exists);
  let userExists = "true" == evt.target.dataset.exists;
  selectModalButtons();
  if (userExists === false) {
    employeesModalBox.querySelector("form").reset();
    employeesModalBox.classList.add("active");
    employeesModalBox.querySelector(".modal-title").textContent =
      "Create Employee";
    let randomNumber = Math.ceil(Math.random() * 10000);
    employeesModalBox.querySelector(".update-btn").style.display = "none";
    employeesModalBox.querySelector(".delete-btn").style.display = "none";
    employeesModalBox.querySelector(".create-btn").style.display = "block";
    employeesModalBox.querySelector(".password-group").style.display = "block";
    employeesModalBox.querySelector(".password-group input").value =
      "boatie" + randomNumber;
    employeesModalBox.querySelector(".password-group input").readOnly = true;
  } else if (userExists === true) {
    //console.log(evt.target.dataset.user_id);
    const userId = evt.target.dataset.user_id;
    employeesModalBox.querySelector(".modal-title").textContent =
      "Manage Employee";
    fetchSingleUser(userId);
    employeesModalBox.classList.add("active");
    employeesModalBox.querySelector(".password-group").style.display = "none";
    employeesModalBox.querySelector(".update-btn").style.display = "block";
    employeesModalBox.querySelector(".delete-btn").style.display = "block";
    employeesModalBox.querySelector(".create-btn").style.display = "none";
  }
}

function fetchSingleUser(userId) {
  let formData = new FormData();
  formData.append("id", userId);

  axios
    .post("../api/get-single-user.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        // console.log("response success");
        updateModalUser(response.data);
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      // console.log(error);
    });
}

function updateModalUser(data) {
  let user = data.data.user;

  //console.log("update");
  employeesModalBox.querySelector(".first-name-group input").value =
    user.first_name;
  employeesModalBox.querySelector(".last-name-group input").value =
    user.last_name;
  employeesModalBox.querySelector(".email-group input").value = user.email;
  employeesModalBox.querySelector(".phone-group input").value = user.phone;

  let privilegeOptions = employeesModalBox.querySelectorAll(
    ".privilege-group option"
  );

  privilegeOptions.forEach(option => {
    if (option.value == user.privilege) {
      option.selected = "selected";
    }
  });

  employeesModalBox.querySelector(".update-btn").dataset.id = user.user_id;
  employeesModalBox.querySelector(".delete-btn").dataset.id = user.user_id;
}

// Close Employees Modal Box
const employeesModalBox = document.querySelector("#employees-modal");

employeesModalBox
  .querySelector(".close-button")
  .addEventListener("click", closeEmployeesModalBox);

employeesModalBox
  .querySelector(".shadow-background")
  .addEventListener("click", closeEmployeesModalBox);

function closeEmployeesModalBox() {
  employeesModalBox.classList.remove("active");
}

/**
 * Fetch Users pagination
 *
 * @param {integer} pageArg Optional number of the page, starts at page 0, default 0
 * @param {integer} perPageArg Optional number of items per page, defualt 30
 */

function fetchUsers(pageArg, perPageArg) {
  page = pageArg && pageArg == 0 ? pageArg : page;
  perPage = perPageArg ? perPageArg : perPage;

  let formData = new FormData();
  formData.append("page", Number(page));
  formData.append("perPage", Number(perPage));

  axios
    .post("../api/get-users.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        displayUsers(response.data);
        paginationHandler(response.data);
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      // console.log(error);
    });
}

function displayUsers(data) {
  //First clean the table
  cleanUsersTable();

  //Then display the new data
  const template = document.querySelector("#employees-template").content;
  const container = document.querySelector("#employees-table");
  let users = data.data.users;
  // console.log(data.data.users);
  users.forEach(elem => {
    //console.log(elem);
    let clone = template.cloneNode(true);
    clone.firstElementChild.id = "user-" + elem.user_id;
    clone.querySelector(".employee-name").textContent =
      elem.first_name + " " + elem.last_name;
    clone.querySelector(".employee-email").textContent = elem.email;
    clone.querySelector(".employee-phone").textContent = elem.phone;

    let privilege = elem.privilege;
    switch (privilege) {
      case 0:
        clone.querySelector(".employee-privilege").textContent = "Admin";
        break;
      case 1:
        clone.querySelector(".employee-privilege").textContent = "Manager";
        break;
      case 2:
        clone.querySelector(".employee-privilege").textContent = "Sailor";
        break;
      default:
        clone.querySelector(".employee-privilege").textContent = "Not assigned";
        break;
    }

    clone.querySelector(".manage-button").dataset.user_id = elem.user_id;
    container.appendChild(clone);
  });
  selectUsersButtons();
}

// Remove all trips in the table and add the table headers
function cleanUsersTable() {
  // Remove all users. This is a fast method to do so: https://stackoverflow.com/a/3955238
  const container = document.querySelector("#employees-table");
  while (container.firstChild) {
    container.removeChild(container.firstChild);
  }

  // Add table headers
  container.innerHTML =
    "<tr><th>Name</th><th>Email</th><th>Phone</th><th>Priviledge</th><th> </th></tr>";
}

// Handle pagination
let pagination = document.querySelector("#pagination");
function paginationHandler(data) {
  //Get the returned pagination values
  page = data.data.page;
  maxPage = data.data.out_of_pages;

  //Update pagination text
  pagination.querySelector(".pagination-status").textContent =
    page + "/" + maxPage;

  //
  window.history.replaceState(null, null, `?page=${page}&limit=${perPage}`); // -> set url param
}

// Pagination next button handler
let paginationNextButton = document.querySelector("#pagination .next");
paginationNextButton.addEventListener("click", paginationNext);

function paginationNext(e) {
  e.preventDefault();
  if (page < maxPage) {
    ++page;
    fetchUsers();
  }

  if (page == maxPage) {
    paginationNextButton.style.opacity = "0";
    paginationNextButton.style.pointerEvents = "none";

    paginationPreviousButton.style.opacity = "1";
    paginationPreviousButton.style.pointerEvents = "auto";
  }
}
// Pagination next previous handler
let paginationPreviousButton = document.querySelector("#pagination .previous");
paginationPreviousButton.addEventListener("click", paginationPrevious);

function paginationPrevious(e) {
  e.preventDefault();
  if (page < maxPage || page > 1) {
    --page;
    fetchUsers();
  }

  if (page == 1) {
    paginationNextButton.style.opacity = "1";
    paginationNextButton.style.pointerEvents = "auto";

    paginationPreviousButton.style.opacity = "0";
    paginationPreviousButton.style.pointerEvents = "none";
  }
}

// Validate input fields

const employeeInputs = document.querySelectorAll(
  ".handle-employee-container input"
);
employeeInputs.forEach(input => {
  input.addEventListener("blur", checkFields);
});

function checkFields(evt) {
  // console.log(evt.target.name);
  if (evt.target.value.length == 0 || !evt.target.value) {
    document.querySelector(".error-message").textContent =
      "add a " + evt.target.name;
    evt.target.classList.add("error");
    return false;
  }
  if (evt.target.value.length < 2) {
    document.querySelector(".error-message").textContent =
      evt.target.name + " is too short";
    evt.target.classList.add("error");
    return false;
  }
  if (evt.target.name == "email") {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (re.test(String(evt.target.value).toLowerCase()) == false) {
      document.querySelector(".error-message").textContent =
        evt.target.name + " not valid";
      evt.target.classList.add("error");
      return false;
    }
  }
  document.querySelector(".error-message").textContent = "";
  evt.target.classList.remove("error");
  return true;
}

/**
 * Create new user
 *
 *
 */

function createNewUser() {
  let formData = new FormData();
  form = document.querySelector(".handle-employee-container");
  form.elements.forEach(element => {
    //console.log(element.name, element.value);
    // console.log(element.tagName);
    if (element.tagName != "BUTTON") {
      formData.append(element.name, element.value);
    }
  });

  axios
    .post("../api/create-user.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        //console.log("status 200");
        fetchUsers();
        closeEmployeesModalBox();
      } else {
      }
    })
    .catch(function(error) {
      // console.log(error);
    });
}

/**
 * Updates user in the frontend
 *
 * @param data data from the form in the modal
 *
 */

function updateUserFrontend(data) {
  closeEmployeesModalBox();
  employeeTr = document.querySelector("#user-" + data.get("id"));
  employeeTr.querySelector(".employee-name").textContent =
    data.get("first-name") + " " + data.get("last-name");
  employeeTr.querySelector(".employee-email").textContent = data.get("email");
  employeeTr.querySelector(".employee-phone").textContent = data.get("phone");

  //make a switch for visual privilege display
  let privilege = data.get("privilege-select");
  // console.log(privilege);
  switch (privilege) {
    case "0":
      employeeTr.querySelector(".employee-privilege").textContent = "Admin";
      break;
    case "1":
      employeeTr.querySelector(".employee-privilege").textContent = "Manager";
      break;
    case "2":
      employeeTr.querySelector(".employee-privilege").textContent = "Sailor";
      break;
    default:
      employeeTr.querySelector(".employee-privilege").textContent =
        "Not assigned";
      break;
  }
}

/**
 * Updates user in the backend
 *
 *
 *
 */

function updateUser() {
  let formData = new FormData();
  let id = employeesModalBox.querySelector(".update-btn").dataset.id;
  formData.append("id", id);
  form = document.querySelector(".handle-employee-container");
  form.elements.forEach(element => {
    //console.log(element.tagName);
    if (element.tagName != "BUTTON") {
      formData.append(element.name, element.value);
    }
  });
  updateUserFrontend(formData);
  axios
    .post("../api/update-user.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        // console.log("status 200");
      } else {
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}

/**
 * Deletes user in the frontend
 *
 * @param id id of the user
 *
 */

function deleteFrontendUser(id) {
  employeeTr = document.querySelector("#user-" + id);
  //console.log(employeeTr);
  closeEmployeesModalBox();
  employeeTr.remove();
}

/**
 * Deletes user in the backend
 *
 *
 */

function deleteUser() {
  let formData = new FormData();
  let id = employeesModalBox.querySelector(".delete-btn").dataset.id;
  formData.append("id", id);
  //console.log(id);
  deleteFrontendUser(id);
  axios
    .post("../api/delete-user.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        //console.log("status 200");
      } else {
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}
