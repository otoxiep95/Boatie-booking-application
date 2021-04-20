<?php


/**
 * Delete event attendees
 */
require_once(__DIR__ . '/../../src/init.php');
$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}
if (!$users->hasPrivilege()) {
    ApiResponse::error("Something went wrong");
}

//check if id exists
if (empty($_POST['id']) && !strlen($_POST['id'])) {
    ApiResponse::error('Id value is missing');
}

/**
 * Set variables
 * 
 */

$customerId = (int) $_POST['id'];

$events = new Events();
$data = $events->deleteAttendeeByID($customerId);

// Send API response
ApiResponse::success((object) [
    "message" => "Success, event deleted",
    "data" => $data
]);
