<?php

/**
 * Get available time slots of a date to book a trip
 */

require_once(__DIR__ . '/../../src/init.php');

/**
 * Checking for numerical values in an API POST request:
 * 
 * Check if empty OR if the variable contains no value. 
 * (Both are needed since the value "0" is seen as empty 
 * and would result into true-> ApiResponse::error('Page value is missing'))
 */
if (empty($_POST['pickup_loc_id']) && !strlen($_POST['pickup_loc_id'])) {
    ApiResponse::error('Pickup location id value is missing');
}
if (empty($_POST['dropoff_loc_id']) && !strlen($_POST['dropoff_loc_id'])) {
    ApiResponse::error('Dropoff location id value is missing');
}
if (empty($_POST['duration']) && !strlen($_POST['duration'])) {
    ApiResponse::error('Duration value is missing');
}
/**
 * isset is used for normal values
 */
if (!isset($_POST['date'])) {
    ApiResponse::error('Date value is missing');
}

//Sanitize values which are not proven numeric values
foreach ($_POST as $key => $arg) {
    if (!is_numeric($arg)) {
        $_POST[$key] = ht($arg);
    }
}
$trips = new Trips();
$avilableTimeSlots = $trips->availableTimeSlots($_POST['date'], $_POST['duration'], $_POST['pickup_loc_id'], $_POST['dropoff_loc_id']);

// Send API response with time slots
ApiResponse::success((object) [
    "message" => "Successfully fetched available time slots",
    "data" => $avilableTimeSlots['slots'],
    "date_available" => $avilableTimeSlots['date_available']
]);
