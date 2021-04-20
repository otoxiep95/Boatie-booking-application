/* ==========================================================================
   Global variables
   ========================================================================== */
let page = 1; // the current page
let perPage = 20; // limit of items per page
let maxPage = 2; // max available pages for pagination. Will instantly be updated to get the correct value from the DB.
const deleteCustomerButton = document.querySelector("button#delete-customer");
/*  ==========================================================================
    Initialize
    ========================================================================== */
document.addEventListener("DOMContentLoaded", init);

function init() {
  /**
   * Check if url parameters exist and update page & perPage value if true
   * tos check if key exists in object, use
   *    "key" in obj // -> returns true if key exists
   */
  urlParam = getURLParam();
  page = "page" in urlParam ? urlParam["page"] : page;
  perPage = "limit" in urlParam ? urlParam["limit"] : perPage;

  //do stuff after page has loaded
  fetchCustomers();
  selectCustomersButtons();
  //shiftTripsPastUpcoming(pastTrips);
}
/*  ==========================================================================
    Functions 
    ========================================================================== */

/**
 * "Manage" buttons onclick handler.
 * Open the mdoal window when a Manage button is clicked
 * and fetch the requested customers data by id
 */
let customersManageButtons;

function selectCustomersButtons() {
  customersManageButtons = document.querySelectorAll(
    "#dashboard-customers .manage-button"
  );
  customersManageButtons.forEach(btn => {
    btn.addEventListener("click", openCustomersModalBox);
  });
}

function openCustomersModalBox(evt) {
  //console.log(evt.target.dataset.customer_id);
  const customersId = evt.target.dataset.customer_id;
  fetchSingleCustomer(customersId);
  customersModalBox.classList.add("active");
}

/**
 * Fetch a single customer data
 *
 * @param {int} customersId The id of the single customers to fetch and display in the modal
 */
function fetchSingleCustomer(customerId) {
  let formData = new FormData();
  formData.append("id", customerId);

  axios
    .post("../api/get-single-customer.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        updateModalCustomer(response.data);
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}

/**
 * Update the modal window with the new requested customer
 *
 * @param {json} data Object or array containing data of a single customer
 */
function updateModalCustomer(data) {
  let customer = data.data.customer;
  customersModalBox.querySelector(
    ".customer-details .customer-modal-name .item-value"
  ).textContent = customer.first_name + " " + customer.last_name;

  customersModalBox.querySelector(
    ".customer-details .customer-modal-email .item-value"
  ).textContent = customer.email;

  customersModalBox.querySelector(
    ".customer-details .customer-modal-phone .item-value"
  ).textContent = customer.phone;

  customersModalBox.querySelector(
    ".customer-details .customer-modal-date .item-value"
  ).textContent = customer.date;

  customersModalBox.querySelector(
    ".customer-details .customer-modal-start-time .item-value"
  ).textContent = customer.start_time;

  customersModalBox.querySelector(
    ".customer-details .customer-modal-end-time .item-value"
  ).textContent = customer.end_time;

  customersModalBox.querySelector(
    ".customer-details .customer-modal-event .item-value"
  ).textContent = customer.name ? customer.name : "Not available";

  customersModalBox.querySelector(
    ".customer-details .customer-modal-group-size .item-value"
  ).textContent = customer.group_size ? customer.group_size : "Not available";

  customersModalBox.querySelector(
    ".customer-details .customer-modal-pickup-location .item-value"
  ).textContent = customer.pickup_loc_name;

  customersModalBox.querySelector(
    ".customer-details .customer-modal-dropoff-location .item-value"
  ).textContent = customer.dropoff_loc_name;

  deleteCustomerButton.dataset.id = customer.customer_id;
}

/**
 * Close Customers Modal Box on click
 */
const customersModalBox = document.querySelector("#manage-customers-modal");

customersModalBox
  .querySelector(".close-button")
  .addEventListener("click", closeCustomersModalBox);

customersModalBox
  .querySelector(".shadow-background")
  .addEventListener("click", closeCustomersModalBox);

function closeCustomersModalBox() {
  customersModalBox.classList.remove("active");
}

/**
 * Handle search field, fetch on 2 characters only.
 *
 */

let isSearch = false;
let refetchActive = false;
const searchLink = "../api/search-customers.php";
const normalLink = "../api/get-customers.php";
let fetchLink = normalLink;
const searchField = document.querySelector("#search-field");
searchField.addEventListener("input", searchHandler);

function searchHandler(e) {
  //Check for input length
  if (e.currentTarget.value.length > 1) {
    //Allow search, swap out links, fetch again, and allow to fetch when input has less than two chars, and revert to first page
    page = 1;
    isSearch = true;
    fetchLink = searchLink;
    fetchCustomers();
    refetchActive = true;
  } else {
    //Disallow search, swap out links and only allow to fetch once
    isSearch = false;
    fetchLink = normalLink;
    if (refetchActive) {
      fetchCustomers();
      refetchActive = false;
    }
  }
}

/**
 * Search input field - delete value inside to stop the search method
 */
const closeSearchBtn = document.querySelector(
  ".search-container .close-search"
);
closeSearchBtn.addEventListener("click", closeSearchBtnHandler);
console.log(closeSearchBtn);
function closeSearchBtnHandler() {
  searchField.value = "";
  isSearch = false;
  fetchLink = normalLink;
  if (refetchActive) {
    fetchCustomers();
    refetchActive = false;
  }
}

/**
 * Fetch Customer pagination
 *
 * @param {integer} pageArg Optional number of the page, starts at page 0, default 0
 * @param {integer} perPageArg Optional number of items per page, defualt 30
 */

function fetchCustomers(pageArg, perPageArg) {
  page = pageArg && pageArg == 0 ? pageArg : page;
  perPage = perPageArg ? perPageArg : perPage;

  let formData = new FormData();
  formData.append("page", Number(page));
  formData.append("perPage", Number(perPage));

  if (isSearch) {
    formData.append("term", searchField.value);
  }

  axios
    .post(fetchLink, formData)
    .then(function(response) {
      // console.log(response.data);
      if (response.data.statusCode == 200) {
        cleanCustomersTable(); //First clean the table
        paginationHandler(response.data); //handle pagination setup based on current page and max pages
        displayCustomers(response.data); // display the customers
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
 * Display the fetched customers using a <template>
 *
 * @param {json} data Object or array containing the data
 */
function displayCustomers(data) {
  //Then display the new data
  const template = document.querySelector("#customers-template").content;
  const container = document.querySelector("#customers-table");
  let customers = data.data.customers;
  //console.log(data);
  customers.forEach(elem => {
    //console.log(elem);
    let clone = template.cloneNode(true);
    clone.firstElementChild.id = "customer-" + elem.customer_id;

    clone.querySelector(".customer-date").textContent = elem.date;
    clone.querySelector(".customer-time-of-booking").textContent =
      elem.time_of_booking;
    clone.querySelector(".customer-name").textContent =
      elem.first_name + " " + elem.last_name;
    clone.querySelector(".customer-email").textContent = elem.email;
    clone.querySelector(".customer-phone").textContent = elem.phone;
    clone.querySelector(".customer-paid").textContent = elem.paid
      ? "Yes"
      : "No";

    clone.querySelector(".manage-button").dataset.customer_id =
      elem.customer_id;
    container.appendChild(clone);
  });
  selectCustomersButtons();
  // Hide/display prev/next button
  paginationPrevNext();
}

/**
 * Remove all customers in the table and add the table headers
 */
function cleanCustomersTable() {
  // Remove all customers. This is a fast method to do so: https://stackoverflow.com/a/3955238
  const container = document.querySelector("#customers-table");
  while (container.firstChild) {
    container.removeChild(container.firstChild);
  }

  // Add table headers
  container.innerHTML =
    "<tr><th>Time</th><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Paid</th><th> </th></tr>";
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
  if (page > maxPage) {
    page = maxPage;
    fetchCustomers();
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
  window.history.replaceState(null, null, `?page=${page}&limit=${perPage}`); // -> set url param
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
  fetchCustomers();
  simpleBarScrollBackToTop(dashboardListSimpleBar); // scroll back to top
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
  fetchCustomers();
  simpleBarScrollBackToTop(dashboardListSimpleBar); // scroll back to top
}

/**
 * Hide or display prev / next button based on the current page
 */
function paginationPrevNext() {
  // console.log("Page is: " + page);
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

// Register the simplebar div (optional, but needed if we want to scroll back to top)
const dashboardListSimpleBar = new SimpleBar(
  document.querySelector(".dashboard-list .list"),
  {
    autoHide: false
  }
);

/**
 * Tell simplebar to scroll the elem back to top
 *
 * @param {DOMElement} elem Element of the simplebar div
 * */
function simpleBarScrollBackToTop(elem) {
  dashboardListSimpleBar.getScrollElement().scrollTop = 0;
}

/**
 * Make modal box scrollbar always visible
 */
const modalBoxSimpleBar = new SimpleBar(
  document.querySelector(".modal-box-sub-wrapper"),
  {
    autoHide: false
  }
);

/**
 * Delete the customer
 */

deleteCustomerButton.addEventListener("click", deleteCustomerById);

function deleteCustomerById(e) {
  let id = e.target.dataset.id;
  let formData = new FormData();
  formData.append("id", id);

  //Optimistic deletion front-end
  document.querySelector("#customer-" + id).remove();
  closeCustomersModalBox();
  //Delete from database
  axios
    .post("../api/delete-customer-by-id.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        // success
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
 *
 * FYI
 * URL params inside JavaScript
 *
 * paginationwindow.history.replaceState(null, null, "?page=1&limit=20"); // -> set url param
 * getURLParam() //-> get url param as an object
 */
