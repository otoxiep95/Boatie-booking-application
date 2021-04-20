<?php

/**
 * Get single trip by its id
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

if (empty($_POST['id']) && !strlen($_POST['id'])) {
    ApiResponse::error('Trip id is missing');
}

$id = (int) $_POST['id'];

// Get single trip
$trips = new Trips();
$data = Trips::getTripById($id);


// Send API response with paginated trips
ApiResponse::success((object) [
    "message" => "Success, trip fetched",
    "data" => $data
]);
