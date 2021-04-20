<?php

/**
 * Search for the customer using a search value
 */

require_once(__DIR__ . '/../../src/init.php');
$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}

/**
 * Checking for numerical values in an API POST request:
 * 
 * Check if empty OR if the variable contains no value. 
 * (Both are needed since the value "0" is seen as empty 
 * and would result into true-> ApiResponse::error('Page value is missing'))
 */
if (empty($_POST['page']) && !strlen($_POST['page'])) {
    ApiResponse::error('Page value is missing');
}
if (empty($_POST['perPage']) && !strlen($_POST['perPage'])) {
    ApiResponse::error('perPage value is missing');
}
if (empty($_POST['term']) && !strlen($_POST['term'])) {
    ApiResponse::error('Search term is missing');
}

/**
 * Since pastTrips values are either TRUE or FALSE, we have to use isset
 */
/* if (is_bool($_POST['pastTrips'])) {
    ApiResponse::error('pastTrips value is missing');
} */

// Set page variables
$page = (int)  ht($_POST['page']);
$perPage = (int) ht($_POST['perPage']);
$term = ht($_POST['term']);

// Get trips with pagination
$customers = new Customers();
$data = $customers->search($page, $perPage, $term);

// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Search successfull",
    "data" => $data
]);
