<?php

require_once(__DIR__ . '/../../src/init.php');

/**
 * Get attendees of a single event
 * 
 */

$users = new Users();
if (!$users->isLoggedIn()) {
    ApiResponse::error("Something went wrong");
}


if (empty($_POST['id']) && !strlen($_POST['id'])) {
    ApiResponse::error('id is missing');
}

$id = (int) $_POST['id'];

// Get attendees from event id
$events = new Events();
$data = $events->getAttendeesByEventId($id);


// Send API response with paginated events
ApiResponse::success((object) [
    "message" => "Success, attendees fetched",
    "data" => $data
]);
