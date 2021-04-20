<?php

/**
 * Get all trips
 */

require_once(__DIR__ . '/../../src/init.php');
$users = new Users();

if (!$users->isLoggedIn()) {
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

/**
 * Since pastTrips values are either TRUE or FALSE, we have to use isset
 */
if (is_bool($_POST['pastTrips'])) {
    ApiResponse::error('pastTrips value is missing');
}

// Set page variables
$page = (int)  ht($_POST['page']);
$perPage = (int) ht($_POST['perPage']);
$pastTrips = filter_var(ht($_POST['pastTrips']), FILTER_VALIDATE_BOOLEAN); // make sure the value is a Boolean and not a string. Aka, if the value is "true" then convert it to TRUE


// Get trips with pagination
$trips = new Trips();
$data = $trips->getTrips($page, $perPage, $pastTrips);

// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, properties fetched",
    "data" => $data
]);
